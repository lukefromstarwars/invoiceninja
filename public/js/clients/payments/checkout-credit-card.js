/*! For license information please see checkout-credit-card.js.LICENSE.txt */
(()=>{function e(t){return e="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},e(t)}function t(t,n){for(var o=0;o<n.length;o++){var r=n[o];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(t,(a=r.key,i=void 0,i=function(t,n){if("object"!==e(t)||null===t)return t;var o=t[Symbol.toPrimitive];if(void 0!==o){var r=o.call(t,n||"default");if("object"!==e(r))return r;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===n?String:Number)(t)}(a,"string"),"symbol"===e(i)?i:String(i)),r)}var a,i}(new(function(){function e(){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e),this.tokens=[]}var n,o,r;return n=e,(o=[{key:"mountFrames",value:function(){console.log("Mount checkout frames..")}},{key:"handlePaymentUsingToken",value:function(e){document.getElementById("checkout--container").classList.add("hidden"),document.getElementById("pay-now-with-token--container").classList.remove("hidden"),document.getElementById("save-card--container").style.display="none",document.querySelector("input[name=token]").value=e.target.dataset.token}},{key:"handlePaymentUsingCreditCard",value:function(e){var t;document.getElementById("checkout--container").classList.remove("hidden"),document.getElementById("pay-now-with-token--container").classList.add("hidden"),document.getElementById("save-card--container").style.display="grid",document.querySelector("input[name=token]").value="";var n=document.getElementById("pay-button"),o=null!==(t=document.querySelector('meta[name="public-key"]').content)&&void 0!==t?t:"",r=document.getElementById("payment-form");Frames.init(o),Frames.addEventHandler(Frames.Events.CARD_VALIDATION_CHANGED,(function(e){n.disabled=!Frames.isCardValid()})),Frames.addEventHandler(Frames.Events.CARD_TOKENIZATION_FAILED,(function(e){pay.button.disabled=!1})),Frames.addEventHandler(Frames.Events.CARD_TOKENIZED,(function(e){n.disabled=!0,document.querySelector('input[name="gateway_response"]').value=JSON.stringify(e),document.querySelector('input[name="store_card"]').value=document.querySelector("input[name=token-billing-checkbox]:checked").value,document.getElementById("server-response").submit()})),r.addEventListener("submit",(function(e){e.preventDefault(),Frames.submitCard()}))}},{key:"completePaymentUsingToken",value:function(e){var t=document.getElementById("pay-now-with-token");t.disabled=!0,t.querySelector("svg").classList.remove("hidden"),t.querySelector("span").classList.add("hidden"),document.getElementById("server-response").submit()}},{key:"handle",value:function(){var e=this;this.handlePaymentUsingCreditCard(),Array.from(document.getElementsByClassName("toggle-payment-with-token")).forEach((function(t){return t.addEventListener("click",e.handlePaymentUsingToken)})),document.getElementById("toggle-payment-with-credit-card").addEventListener("click",this.handlePaymentUsingCreditCard),document.getElementById("pay-now-with-token").addEventListener("click",this.completePaymentUsingToken)}}])&&t(n.prototype,o),r&&t(n,r),Object.defineProperty(n,"prototype",{writable:!1}),e}())).handle()})();