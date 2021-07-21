@extends('portal.ninja2020.layout.payments', ['gateway_title' => ctrans('texts.credit_card'), 'card_title' => ctrans('texts.credit_card')])

@section('gateway_head')
@endsection

@section('gateway_content')
    
    @if(!Request::isSecure())
        <p class="alert alert-failure">{{ ctrans('texts.https_required') }}</p>
    @endif

    <div class="alert alert-failure mb-4" hidden id="errors"></div>
    <form action="{{ route('client.payment_methods.store', ['method' => App\Models\GatewayType::CREDIT_CARD]) }}"  method="post" id="server_response">
        @csrf

    <div class="w-screen items-center">

        <div id='pt_hpf_form'><!--iframe sensitive data payment fields inserted here--></div>

    </div>

    <input type="hidden" name="company_gateway_id" value="{{ $gateway->company_gateway->id }}">
    <input type="txt" id=HPF_Token name= HPF_Token hidden>
    <input type="txt" id=enc_key name= enc_key hidden>

    <div class="bg-white px-4 py-5 flex justify-end">
        <button
            type="submit"
            id="{{ $id ?? 'pay-now' }}"
            class="button button-primary bg-primary {{ $class ?? '' }}">
            <span>{{ ctrans('texts.add_payment_method') }}</span>
        </button>
    </div>

    </form>
@endsection

@section('gateway_footer')

<script src='https://protect.paytrace.com/js/protect.min.js'></script>

<script>

  // Minimal Protect.js setup call
PTPayment.setup({
  styles:
  {
   'code': {
    'font_color':'#5D99CA',
    'border_color':'#EF9F6D',
    'label_color':'#EF9F6D',
    'label_size':'20px',
    'background_color':'white',
    'border_style':'dotted',
    'font_size':'15pt',
    'height':'30px',
    'width':'100px'
 },
   'cc': {
    'font_color':'#5D99CA',
    'border_color':'#EF9F6D',
    'label_color':'#EF9F6D',
    'label_size':'20px',
    'background_color':'white',
    'border_style':'solid',
    'font_size':'15pt',
    'height':'30px',
    'width':'300px'
 },
   'exp': {
    'font_color':'#5D99CA',
    'border_color':'#EF9F6D',
    'label_color':'#EF9F6D',
    'label_size':'20px',
    'background_color':'white',
    'border_style':'dashed',
    'font_size':'15pt',
    'height':'30px',
    'width':'85px',
    'type':'dropdown'
  }
 },
  authorization: { 'clientKey': "{!! $client_key !!}" }
}).then(function(instance){


    PTPayment.getControl("securityCode").label.text("{!! ctrans('texts.cvv')!!}");
    PTPayment.getControl("creditCard").label.text("{!! ctrans('texts.card_number')!!}");
    PTPayment.getControl("expiration").label.text("{!! ctrans('texts.expires')!!}");
    //PTPayment.style({'cc': {'label_color': 'red'}});
    //PTPayment.style({'code': {'label_color': 'red'}});
    //PTPayment.style({'exp': {'label_color': 'red'}});
    //PTPayment.style({'exp':{'type':'dropdown'}});

    //PTPayment.theme('horizontal');
    // this can be any event we chose. We will use the submit event and stop any default event handling and prevent event handling bubbling.
    document.getElementById("server_response").addEventListener("submit",function(e){
    e.preventDefault();
    e.stopPropagation();

    // To trigger the validation of sensitive data payment fields within the iframe before calling the tokenization process:
    PTPayment.validate(function(validationErrors) {

    if (validationErrors.length >= 1) {

        let errors = document.getElementById('errors');

        errors.textContent = '';
        errors.textContent = validationErrors[0].description;
        errors.hidden = false;

    } else {
     // no error so tokenize
     instance.process()
     .then( (r) => {
        submitPayment(r);
        }, (err) => {
        handleError(err);
        });
    }
    });

    });

});


function handleError(err){
    document.write(JSON.stringify(err));
}

function submitPayment(r){

  var hpf_token = document.getElementById("HPF_Token");
  var enc_key = document.getElementById("enc_key");
  hpf_token.value = r.message.hpf_token;
  enc_key.value = r.message.enc_key;

  document.getElementById("server_response").submit();

}

</script>

@endsection
