/*! For license information please see wepay-bank-account.js.LICENSE.txt */
(()=>{function e(e,n){for(var t=0;t<n.length;t++){var o=n[t];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(e,o.key,o)}}var n=function(){function n(){!function(e,n){if(!(e instanceof n))throw new TypeError("Cannot call a class as a function")}(this,n)}var t,o,r;return t=n,(o=[{key:"initializeWePay",value:function(){var e,n=null===(e=document.querySelector('meta[name="wepay-environment"]'))||void 0===e?void 0:e.content;return WePay.set_endpoint("staging"===n?"stage":"production"),this}},{key:"showBankPopup",value:function(){var e,n;WePay.bank_account.create({client_id:null===(e=document.querySelector("meta[name=wepay-client-id]"))||void 0===e?void 0:e.content,email:null===(n=document.querySelector("meta[name=contact-email]"))||void 0===n?void 0:n.content},(function(e){e.error?(errors.textContent="",errors.textContent=e.error_description,errors.hidden=!1):(document.querySelector('input[name="bank_account_id"]').value=e.bank_account_id,document.getElementById("server_response").submit())}),(function(e){e.error&&(errors.textContent="",errors.textContent=e.error_description,errors.hidden=!1)}))}},{key:"handle",value:function(){this.initializeWePay().showBankPopup()}}])&&e(t.prototype,o),r&&e(t,r),n}();document.addEventListener("DOMContentLoaded",(function(){(new n).handle()}))})();