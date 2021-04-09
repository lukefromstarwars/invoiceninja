<?php
/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2021. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://opensource.org/licenses/AAL
 */

namespace App\Http\Controllers;

use App\Utils\Ninja;
use Cz\Git\GitException;
use Cz\Git\GitRepository;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Artisan;

class SelfUpdateController extends BaseController
{
    use DispatchesJobs;

    public function __construct()
    {
    }

    /**
     * @OA\Post(
     *      path="/api/v1/self-update",
     *      operationId="selfUpdate",
     *      tags={"update"},
     *      summary="Performs a system update",
     *      description="Performs a system update",
     *      @OA\Parameter(ref="#/components/parameters/X-Api-Secret"),
     *      @OA\Parameter(ref="#/components/parameters/X-Api-Token"),
     *      @OA\Parameter(ref="#/components/parameters/X-Api-Password"),
     *      @OA\Parameter(ref="#/components/parameters/X-Requested-With"),
     *      @OA\Parameter(ref="#/components/parameters/include"),
     *      @OA\Response(
     *          response=200,
     *          description="Success/failure response"
     *       ),
     *       @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(ref="#/components/schemas/ValidationError"),
     *
     *       ),
     *       @OA\Response(
     *           response="default",
     *           description="Unexpected Error",
     *           @OA\JsonContent(ref="#/components/schemas/Error"),
     *       ),
     *     )
     */
    public function update(\Codedge\Updater\UpdaterManager $updater)
    {
        set_time_limit(0);
        define('STDIN', fopen('php://stdin', 'r'));

        if (Ninja::isNinja()) {
            return response()->json(['message' => ctrans('texts.self_update_not_available')], 403);
        }


        // $it = new \RecursiveDirectoryIterator("/home/david/Development/invoiceninja");

        // $x=1;
        // // Loop through files
        // foreach(new \RecursiveIteratorIterator($it) as $file) {

        // var_dum
        //    if(is_file($file) && is_writable($file))
        //     nlog('writable ' .$x);
        //   elseif(is_file($file))
        //     nlog("{$file} is not writable {$x}");
        //   //exit;
          
        //   $x++;
        //   // if ($file->isWritable() === false) {
        //   //       $checkPermission = false;
        //   //   }
          
        // }

        // Check if new version is available
        if($updater->source()->isNewVersionAvailable()) {

            // Get the new version available
            $versionAvailable = $updater->source()->getVersionAvailable();

            // Create a release
            $release = $updater->source()->fetch($versionAvailable);

            // Run the update process
            $updater->source()->update($release);

        }
            
        $cacheCompiled = base_path('bootstrap/cache/compiled.php');
        if (file_exists($cacheCompiled)) { unlink ($cacheCompiled); }
        $cacheServices = base_path('bootstrap/cache/services.php');
        if (file_exists($cacheServices)) { unlink ($cacheServices); }

        Artisan::call('clear-compiled');
        Artisan::call('cache:clear');
        Artisan::call('debugbar:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');

        return response()->json(['message' => 'Update completed'], 200);

    }

    public function checkVersion()
    {
        return trim(file_get_contents(config('ninja.version_url')));
    }
}
