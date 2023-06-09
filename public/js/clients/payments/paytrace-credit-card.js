/*! For license information please see paytrace-credit-card.js.LICENSE.txt */
(()=>{function e(t){return e="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},e(t)}function t(t,n){for(var r=0;r<n.length;r++){var o=n[r];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(t,(a=o.key,i=void 0,i=function(t,n){if("object"!==e(t)||null===t)return t;var r=t[Symbol.toPrimitive];if(void 0!==r){var o=r.call(t,n||"default");if("object"!==e(o))return o;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===n?String:Number)(t)}(a,"string"),"symbol"===e(i)?i:String(i)),o)}var a,i}(new(function(){function e(){var t;!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e),this.clientKey=null===(t=document.querySelector("meta[name=paytrace-client-key]"))||void 0===t?void 0:t.content}var n,r,o;return n=e,(r=[{key:"creditCardStyles",get:function(){return{font_color:"#000",border_color:"#a1b1c9",border_style:"dotted",font_size:"13pt",input_border_radius:"3px",input_border_width:"1px",input_font:"Times New Roman, arial, fantasy",input_font_weight:"400",input_margin:"5px 0px 5px 0px",input_padding:"0px 5px 0px 5px",label_color:"#a0aec0",label_size:"16px",label_width:"150px",label_font:"Times New Roman, sans-serif, serif",label_font_weight:"light",label_margin:"5px 0px 0px 0px",label_padding:"0px 5px 0px 5px",background_color:"white",height:"30px",width:"370px",padding_bottom:"0px"}}},{key:"codeStyles",get:function(){return{font_color:"#000",border_color:"#a1b1c9",border_style:"dotted",font_size:"13pt",input_border_radius:"2px",input_border_width:"1px",input_font:"serif, cursive, fantasy",input_font_weight:"700",input_margin:"5px 0px 5px 20px",input_padding:"0px 5px 0px 5px",label_color:"#a0aec0",label_size:"16px",label_width:"150px",label_font:"sans-serif, arial, serif",label_font_weight:"bold",label_margin:"5px 0px 0px 20px",label_padding:"2px 5px 2px 5px",background_color:"white",height:"30px",width:"150px",padding_bottom:"2px"}}},{key:"expStyles",get:function(){return{font_color:"#000",border_color:"#a1b1c9",border_style:"dashed",font_size:"12pt",input_border_radius:"0px",input_border_width:"2px",input_font:"arial, cursive, fantasy",input_font_weight:"400",input_margin:"5px 0px 5px 0px",input_padding:"0px 5px 0px 5px",label_color:"#a0aec0",label_size:"16px",label_width:"150px",label_font:"arial, fantasy, serif",label_font_weight:"normal",label_margin:"5px 0px 0px 0px",label_padding:"2px 5px 2px 5px",background_color:"white",height:"30px",width:"85px",padding_bottom:"2px",type:"dropdown"}}},{key:"updatePayTraceLabels",value:function(){window.PTPayment.getControl("securityCode").label.text(document.querySelector("meta[name=ctrans-cvv]").content),window.PTPayment.getControl("creditCard").label.text(document.querySelector("meta[name=ctrans-card_number]").content),window.PTPayment.getControl("expiration").label.text(document.querySelector("meta[name=ctrans-expires]").content)}},{key:"setupPayTrace",value:function(){return window.PTPayment.setup({styles:{code:this.codeStyles,cc:this.creditCardStyles,exp:this.expStyles},authorization:{clientKey:this.clientKey}})}},{key:"handlePaymentWithCreditCard",value:function(e){var t=this;e.target.parentElement.disabled=!0,document.getElementById("errors").hidden=!0,window.PTPayment.validate((function(n){if(n.length>=1){var r=document.getElementById("errors");return r.textContent=n[0].description,r.hidden=!1,e.target.parentElement.disabled=!1}t.ptInstance.process().then((function(e){document.getElementById("HPF_Token").value=e.message.hpf_token,document.getElementById("enc_key").value=e.message.enc_key;var t=document.querySelector('input[name="token-billing-checkbox"]:checked');t&&(document.querySelector('input[name="store_card"]').value=t.value),document.getElementById("server_response").submit()})).catch((function(e){document.getElementById("errors").textContent=JSON.stringify(e),document.getElementById("errors").hidden=!1,console.log(e)}))}))}},{key:"handlePaymentWithToken",value:function(e){e.target.parentElement.disabled=!0,document.getElementById("server_response").submit()}},{key:"handle",value:function(){var e,t=this;Array.from(document.getElementsByClassName("toggle-payment-with-token")).forEach((function(e){return e.addEventListener("click",(function(e){document.getElementById("paytrace--credit-card-container").classList.add("hidden"),document.getElementById("save-card--container").style.display="none",document.querySelector("input[name=token]").value=e.target.dataset.token}))})),null===(e=document.getElementById("toggle-payment-with-credit-card"))||void 0===e||e.addEventListener("click",(function(e){document.getElementById("paytrace--credit-card-container").classList.remove("hidden"),document.getElementById("save-card--container").style.display="grid",document.querySelector("input[name=token]").value="",t.setupPayTrace().then((function(e){t.ptInstance=e,t.updatePayTraceLabels()}))})),document.getElementById("pay-now").addEventListener("click",(function(e){return""===document.querySelector("input[name=token]").value?t.handlePaymentWithCreditCard(e):t.handlePaymentWithToken(e)}))}}])&&t(n.prototype,r),o&&t(n,o),Object.defineProperty(n,"prototype",{writable:!1}),e}())).handle()})();