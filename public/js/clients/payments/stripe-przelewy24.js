/*! For license information please see stripe-przelewy24.js.LICENSE.txt */
(()=>{var e,t,n,o;function r(e,t){for(var n=0;n<t.length;n++){var o=t[n];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(e,o.key,o)}}function a(e,t,n){return t&&r(e.prototype,t),n&&r(e,n),Object.defineProperty(e,"prototype",{writable:!1}),e}function d(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}var c=a((function e(t,n){var o=this;!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e),d(this,"setupStripe",(function(){o.stripeConnect?o.stripe=Stripe(o.key,{stripeAccount:o.stripeConnect}):o.stripe=Stripe(o.key);var e=o.stripe.elements();return o.p24bank=e.create("p24Bank",{style:{base:{padding:"10px 12px",color:"#32325d",fontSize:"16px","::placeholder":{color:"#aab7c4"}}}}),o.p24bank.mount("#p24-bank-element"),o})),d(this,"handle",(function(){document.getElementById("pay-now").addEventListener("click",(function(e){var t=document.getElementById("errors");return""===document.getElementById("p24-name").value?(document.getElementById("p24-name").focus(),t.textContent=document.querySelector("meta[name=translation-name-required]").content,void(t.hidden=!1)):""===document.getElementById("p24-email-address").value?(document.getElementById("p24-email-address").focus(),t.textContent=document.querySelector("meta[name=translation-email-required]").content,void(t.hidden=!1)):document.getElementById("p24-mandate-acceptance").checked?(document.getElementById("pay-now").disabled=!0,document.querySelector("#pay-now > svg").classList.remove("hidden"),document.querySelector("#pay-now > span").classList.add("hidden"),void o.stripe.confirmP24Payment(document.querySelector("meta[name=pi-client-secret").content,{payment_method:{p24:o.p24bank,billing_details:{name:document.getElementById("p24-name").value,email:document.getElementById("p24-email-address").value}},payment_method_options:{p24:{tos_shown_and_accepted:document.getElementById("p24-mandate-acceptance").checked}},return_url:document.querySelector('meta[name="return-url"]').content}).then((function(e){e.error?(t.textContent=e.error.message,t.hidden=!1,document.getElementById("pay-now").disabled=!1,document.querySelector("#pay-now > svg").classList.add("hidden"),document.querySelector("#pay-now > span").classList.remove("hidden")):"succeeded"===e.paymentIntent.status&&(window.location=document.querySelector('meta[name="return-url"]').content)}))):(document.getElementById("p24-mandate-acceptance").focus(),t.textContent=document.querySelector("meta[name=translation-terms-required]").content,void(t.hidden=!1))}))})),this.key=t,this.errors=document.getElementById("errors"),this.stripeConnect=n}));new c(null!==(e=null===(t=document.querySelector('meta[name="stripe-publishable-key"]'))||void 0===t?void 0:t.content)&&void 0!==e?e:"",null!==(n=null===(o=document.querySelector('meta[name="stripe-account-id"]'))||void 0===o?void 0:o.content)&&void 0!==n?n:"").setupStripe().handle()})();