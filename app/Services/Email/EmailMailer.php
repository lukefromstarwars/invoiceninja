<?php
/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2022. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */

namespace App\Services\Email;

use App\DataMapper\Analytics\EmailFailure;
use App\DataMapper\Analytics\EmailSuccess;
use App\DataMapper\EmailTemplateDefaults;
use App\Events\Invoice\InvoiceWasEmailedAndFailed;
use App\Events\Payment\PaymentWasEmailedAndFailed;
use App\Jobs\Util\SystemLogger;
use App\Libraries\Google\Google;
use App\Libraries\MultiDB;
use App\Models\Account;
use App\Models\ClientContact;
use App\Models\Company;
use App\Models\InvoiceInvitation;
use App\Models\Payment;
use App\Models\SystemLog;
use App\Models\User;
use App\Services\Email\EmailObject;
use App\Utils\Ninja;
use App\Utils\Traits\MakesHash;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use League\CommonMark\CommonMarkConverter;
use Turbo124\Beacon\Facades\LightLogs;

class EmailMailer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, MakesHash;

    public $tries = 3; //number of retries

    public $backoff = 30; //seconds to wait until retry

    public $deleteWhenMissingModels = true;

    public $override;

    private $mailer;

    protected $client_postmark_secret = false;

    protected $client_mailgun_secret = false;

    protected $client_mailgun_domain = false;

    public function __construct(public EmailService $email_service, public Mailable $email_mailable){}

    public function handle(): void
    {
    	MultiDB::setDb($this->email_service->company->db);

    	//decode all attachments
    	$this->setMailDriver();

    	$mailer = Mail::mailer($this->mailer);

        if($this->client_postmark_secret){
            nlog("inside postmark config");
            nlog($this->client_postmark_secret);
            $mailer->postmark_config($this->client_postmark_secret);
        }

        if($this->client_mailgun_secret){
            $mailer->mailgun_config($this->client_mailgun_secret, $this->client_mailgun_domain);
        }


        //send email
        try {
            
		    nlog("Using mailer => ". $this->mailer. " ". now()->toDateTimeString());
		    $mailer->send($this->email_mailable);

	        Cache::increment($this->email_service->company->account->key);

	        LightLogs::create(new EmailSuccess($this->email_service->company->company_key))
	                 ->send();

        } catch (\Exception | \RuntimeException | \Google\Service\Exception $e) {
            
            nlog("error failed with {$e->getMessage()}");
            
            $this->cleanUpMailers();

            $message = $e->getMessage();

            /**
             * Post mark buries the proper message in a a guzzle response
             * this merges a text string with a json object
             * need to harvest the ->Message property using the following
             */
            if($e instanceof ClientException) { //postmark specific failure

                $response = $e->getResponse();
                $message_body = json_decode($response->getBody()->getContents());
                
                if($message_body && property_exists($message_body, 'Message')){
                    $message = $message_body->Message;
                    nlog($message);
                }
                
            }

            /* If the is an entity attached to the message send a failure mailer */
            $this->entityEmailFailed($message);

            /* Don't send postmark failures to Sentry */
            if(Ninja::isHosted() && (!$e instanceof ClientException)) 
                app('sentry')->captureException($e);

            $message = null;
            // $this->email_service = null;
            // $this->email_mailable = null;
    
        }

    }

    /**
     * Entity notification when an email fails to send
     * 
     * @param  string $message
     * @return void
     */
    private function entityEmailFailed($message)
    {

    	if(!$this->email_service->email_object->entity_id)
            return;

        switch ($this->email_service->email_object->entity_class) {
            case Invoice::class:
            	$invitation = InvoiceInvitation::withTrashed()->find($this->email_service->email_object->entity_id);
            	if($invitation)
                	event(new InvoiceWasEmailedAndFailed($invitation, $this->email_service->company, $message, $this->email_service->email_object->reminder_template, Ninja::eventVars(auth()->user() ? auth()->user()->id : null)));
                break;
            case Payment::class:
            	$payment = Payment::withTrashed()->find($this->email_service->email_object->entity_id);
                if($payment)
                	event(new PaymentWasEmailedAndFailed($payment, $this->email_service->company, $message, Ninja::eventVars(auth()->user() ? auth()->user()->id : null)));
                break;
            default:
                # code...
                break;
        }

        if ($this->email_service->email_object->client_contact instanceof ClientContact) 
            $this->logMailError($message, $this->email_service->email_object->client_contact);

    }

	private function setMailDriver(): self
    {

        switch ($this->email_service->email_object->settings->email_sending_method) {
            case 'default':
                $this->mailer = config('mail.default');
                break;
            case 'gmail':
                $this->mailer = 'gmail';
                $this->setGmailMailer();
                return $this;
            case 'office365':
                $this->mailer = 'office365';
                $this->setOfficeMailer();
                return $this;
            case 'client_postmark':
                $this->mailer = 'postmark';
                $this->setPostmarkMailer();
                return $this;
            case 'client_mailgun':
                $this->mailer = 'mailgun';
                $this->setMailgunMailer();
                return $this;

            default:
                break;
        }

        if(Ninja::isSelfHost())
            $this->setSelfHostMultiMailer();

        return $this;

    }

    /**
     * Allows configuration of multiple mailers
     * per company for use by self hosted users
     */
    private function setSelfHostMultiMailer(): void
    {

        if (env($this->email_service->company->id . '_MAIL_HOST')) 
        {

            config([
                'mail.mailers.smtp' => [
                    'transport' => 'smtp',
                    'host' => env($this->email_service->company->id . '_MAIL_HOST'),
                    'port' => env($this->email_service->company->id . '_MAIL_PORT'),
                    'username' => env($this->email_service->company->id . '_MAIL_USERNAME'),
                    'password' => env($this->email_service->company->id . '_MAIL_PASSWORD'),
                ],
            ]);

            if(env($this->email_service->company->id . '_MAIL_FROM_ADDRESS'))
            {
            $this->email_mailable
                 ->from(env($this->email_service->company->id . '_MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS')), env($this->email_service->company->id . '_MAIL_FROM_NAME', env('MAIL_FROM_NAME')));
            }

        }

    }


    /**
     * Ensure we discard any data that is not required
     * 
     * @return void
     */
    private function cleanUpMailers(): void
    {
        $this->client_postmark_secret = false;

        $this->client_mailgun_secret = false;

        $this->client_mailgun_domain = false;

        //always dump the drivers to prevent reuse 
        app('mail.manager')->forgetMailers();
    }

    /** 
     * Check to ensure no cross account
     * emails can be sent.
     * 
     * @param User $user
     */
    private function checkValidSendingUser($user)
    {
        /* Always ensure the user is set on the correct account */
        if($user->account_id != $this->email_service->company->account_id){

            $this->email_service->email_object->settings->email_sending_method = 'default';
            return $this->setMailDriver();
        }
    }

    /**
     * Resolves the sending user
     * when configuring the Mailer
     * on behalf of the client
     * 
     * @return User $user
     */
    private function resolveSendingUser(): ?User
    {
        $sending_user = $this->email_service->email_object->settings->gmail_sending_user_id;

        $user = User::find($this->decodePrimaryKey($sending_user));

        return $user;
    }

    /**
     * Configures Mailgun using client supplied secret
     * as the Mailer
     */
    private function setMailgunMailer()
    {
        if(strlen($this->email_service->email_object->settings->mailgun_secret) > 2 && strlen($this->email_service->email_object->settings->mailgun_domain) > 2){
            $this->client_mailgun_secret = $this->email_service->email_object->settings->mailgun_secret;
            $this->client_mailgun_domain = $this->email_service->email_object->settings->mailgun_domain;
        }
        else{
            $this->email_service->email_object->settings->email_sending_method = 'default';
            return $this->setMailDriver();
        }


        $user = $this->resolveSendingUser();

            $this->mailable
             ->from($user->email, $user->name());
    }

    /**
     * Configures Postmark using client supplied secret
     * as the Mailer
     */
    private function setPostmarkMailer()
    {
        if(strlen($this->email_service->email_object->settings->postmark_secret) > 2){
            $this->client_postmark_secret = $this->email_service->email_object->settings->postmark_secret;
        }
        else{
            $this->email_service->email_object->settings->email_sending_method = 'default';
            return $this->setMailDriver();
        }

        $user = $this->resolveSendingUser();

            $this->mailable
             ->from($user->email, $user->name());
    }

    /**
     * Configures Microsoft via Oauth
     * as the Mailer
     */
    private function setOfficeMailer()
    {
        $user = $this->resolveSendingUser();
        
        $this->checkValidSendingUser($user);
        
        nlog("Sending via {$user->name()}");

        $token = $this->refreshOfficeToken($user);

        if($token)
        {
            $user->oauth_user_token = $token;
            $user->save();

        }
        else {

            $this->email_service->email_object->settings->email_sending_method = 'default';
            return $this->setMailDriver();
        
        }

        $this->mailable
             ->from($user->email, $user->name())
             ->withSymfonyMessage(function ($message) use($token) {
                $message->getHeaders()->addTextHeader('gmailtoken', $token);     
             });

        sleep(rand(1,3));
    }

    /**
     * Configures GMail via Oauth
     * as the Mailer
     */
    private function setGmailMailer()
    {

        $user = $this->resolveSendingUser();

        $this->checkValidSendingUser($user);
        
        nlog("Sending via {$user->name()}");

        $google = (new Google())->init();

        try{

            if ($google->getClient()->isAccessTokenExpired()) {
                $google->refreshToken($user);
                $user = $user->fresh();
            }

            $google->getClient()->setAccessToken(json_encode($user->oauth_user_token));

            sleep(rand(2,4));
        }
        catch(\Exception $e) {
            $this->logMailError('Gmail Token Invalid', $this->email_service->company->clients()->first());
            $this->email_service->email_object->settings->email_sending_method = 'default';
            return $this->setMailDriver();
        }

        /**
         * If the user doesn't have a valid token, notify them
         */

        if(!$user->oauth_user_token) {
            $this->email_service->company->account->gmailCredentialNotification();
            $this->email_service->email_object->settings->email_sending_method = 'default';
            return $this->setMailDriver();
        }

        /*
         *  Now that our token is refreshed and valid we can boot the
         *  mail driver at runtime and also set the token which will persist
         *  just for this request.
        */

        $token = $user->oauth_user_token->access_token;

        if(!$token) {
            $this->email_service->company->account->gmailCredentialNotification();
            $this->email_service->email_object->settings->email_sending_method = 'default';
            return $this->setMailDriver();
        }

        $this->mailable
             ->from($user->email, $user->name())
             ->withSymfonyMessage(function ($message) use($token) {
                $message->getHeaders()->addTextHeader('gmailtoken', $token);     
             });

    }

    /**
     * Logs any errors to the SystemLog
     * 
     * @param  string $errors
     * @param  App\Models\User | App\Models\Client $recipient_object
     * @return void
     */
    private function logMailError($errors, $recipient_object) :void
    {

        (new SystemLogger(
            $errors,
            SystemLog::CATEGORY_MAIL,
            SystemLog::EVENT_MAIL_SEND,
            SystemLog::TYPE_FAILURE,
            $recipient_object,
            $this->email_service->company
        ))->handle();

        $job_failure = new EmailFailure($this->email_service->company->company_key);
        $job_failure->string_metric5 = 'failed_email';
        $job_failure->string_metric6 = substr($errors, 0, 150);

        LightLogs::create($job_failure)
                 ->send();

        $job_failure = null;

    }

    /**
     * Attempts to refresh the Microsoft refreshToken
     * 
     * @param  App\Models\User
     * @return string | bool
     */
    private function refreshOfficeToken($user)
    {
        $expiry = $user->oauth_user_token_expiry ?: now()->subDay();

        if($expiry->lt(now()))
        {
            $guzzle = new \GuzzleHttp\Client(); 
            $url = 'https://login.microsoftonline.com/common/oauth2/v2.0/token'; 

            $token = json_decode($guzzle->post($url, [
                'form_params' => [
                    'client_id' => config('ninja.o365.client_id') ,
                    'client_secret' => config('ninja.o365.client_secret') ,
                    'scope' => 'email Mail.Send offline_access profile User.Read openid',
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $user->oauth_user_refresh_token
                ],
            ])->getBody()->getContents());
            
            if($token){
                
                $user->oauth_user_refresh_token = property_exists($token, 'refresh_token') ? $token->refresh_token : $user->oauth_user_refresh_token;
                $user->oauth_user_token = $token->access_token;
                $user->oauth_user_token_expiry = now()->addSeconds($token->expires_in);
                $user->save();

                return $token->access_token;
            }

            return false;
        }

        return $user->oauth_user_token;
        
    }

    public function failed($exception = null)
    {
        
    }

}
