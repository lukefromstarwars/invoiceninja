/*! For license information please see stripe-acss.js.LICENSE.txt */
(()=>{var e,t,n,r;function o(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}function a(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}var d=function(){function e(t,n){var r=this;!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e),a(this,"setupStripe",(function(){return r.stripe=Stripe(r.key),r.stripeConnect&&(r.stripe.stripeAccount=c),r})),a(this,"handle",(function(){document.getElementById("pay-now").addEventListener("click",(function(e){var t=document.getElementById("errors");return""===document.getElementById("acss-name").value?(document.getElementById("acss-name").focus(),t.textContent=document.querySelector("meta[name=translation-name-required]").content,void(t.hidden=!1)):""===document.getElementById("acss-email-address").value?(document.getElementById("acss-email-address").focus(),t.textContent=document.querySelector("meta[name=translation-email-required]").content,void(t.hidden=!1)):(document.getElementById("pay-now").disabled=!0,document.querySelector("#pay-now > svg").classList.remove("hidden"),document.querySelector("#pay-now > span").classList.add("hidden"),void r.stripe.confirmAcssDebitPayment(document.querySelector("meta[name=pi-client-secret").content,{payment_method:{billing_details:{name:document.getElementById("acss-name").value,email:document.getElementById("acss-email-address").value}}}).then((function(e){return e.error?r.handleFailure(e.error.message):r.handleSuccess(e)})))}))})),this.key=t,this.errors=document.getElementById("errors"),this.stripeConnect=n}var t,n,r;return t=e,(n=[{key:"handleSuccess",value:function(e){document.querySelector('input[name="gateway_response"]').value=JSON.stringify(e.paymentIntent),document.getElementById("server-response").submit()}},{key:"handleFailure",value:function(e){var t=document.getElementById("errors");t.textContent="",t.textContent=e,t.hidden=!1,document.getElementById("pay-now").disabled=!1,document.querySelector("#pay-now > svg").classList.add("hidden"),document.querySelector("#pay-now > span").classList.remove("hidden")}}])&&o(t.prototype,n),r&&o(t,r),e}(),i=null!==(e=null===(t=document.querySelector('meta[name="stripe-publishable-key"]'))||void 0===t?void 0:t.content)&&void 0!==e?e:"",c=null!==(n=null===(r=document.querySelector('meta[name="stripe-account-id"]'))||void 0===r?void 0:r.content)&&void 0!==n?n:"";new d(i,c).setupStripe().handle()})();