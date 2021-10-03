/*! For license information please see square-credit-card.js.LICENSE.txt */
!function(t){var e={};function n(r){if(e[r])return e[r].exports;var o=e[r]={i:r,l:!1,exports:{}};return t[r].call(o.exports,o,o.exports,n),o.l=!0,o.exports}n.m=t,n.c=e,n.d=function(t,e,r){n.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:r})},n.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},n.t=function(t,e){if(1&e&&(t=n(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var r=Object.create(null);if(n.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var o in t)n.d(r,o,function(e){return t[e]}.bind(null,o));return r},n.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return n.d(e,"a",e),e},n.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},n.p="/",n(n.s=25)}({25:function(t,e,n){t.exports=n("flnV")},flnV:function(t,e,n){"use strict";n.r(e);var r=n("o0o1"),o=n.n(r);function a(t,e,n,r,o,a,i){try{var c=t[a](i),u=c.value}catch(t){return void n(t)}c.done?e(u):Promise.resolve(u).then(r,o)}function i(t){return function(){var e=this,n=arguments;return new Promise((function(r,o){var i=t.apply(e,n);function c(t){a(i,r,o,c,u,"next",t)}function u(t){a(i,r,o,c,u,"throw",t)}c(void 0)}))}}function c(t,e){for(var n=0;n<e.length;n++){var r=e[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(t,r.key,r)}}(new(function(){function t(){!function(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}(this,t),this.appId=document.querySelector("meta[name=square-appId]").content,this.locationId=document.querySelector("meta[name=square-locationId]").content,this.isLoaded=!1}var e,n,r,a,u,s,l,d;return e=t,(n=[{key:"init",value:(d=i(o.a.mark((function t(){var e;return o.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return this.payments=Square.payments(this.appId,this.locationId),t.next=3,this.payments.card();case 3:return this.card=t.sent,t.next=6,this.card.attach("#card-container");case 6:this.isLoaded=!0,(e=document.querySelector(".sq-card-iframe-container"))&&e.setAttribute("style","150px !important"),document.querySelector(".toggle-payment-with-token")&&document.getElementById("card-container").classList.add("hidden");case 11:case"end":return t.stop()}}),t,this)}))),function(){return d.apply(this,arguments)})},{key:"completePaymentWithoutToken",value:(l=i(o.a.mark((function t(e){var n,r,a,i,c;return o.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return document.getElementById("errors").hidden=!0,e.target.parentElement.disabled=!0,t.next=4,this.card.tokenize();case 4:return n=t.sent,console.log("square token = "+n.token),t.prev=6,a={amount:document.querySelector("meta[name=amount]").content,billingContact:document.querySelector("meta[name=square_contact]").content,currencyCode:document.querySelector("meta[name=currencyCode]").content,intent:"CHARGE"},console.log(a),t.next=11,this.payments.verifyBuyer(n.token,a);case 11:i=t.sent,r=i.token,t.next=19;break;case 15:t.prev=15,t.t0=t.catch(6),console.log(t.t0),die("failed in the catch");case 19:if(console.debug("Verification Token:",r),document.querySelector('input[name="verificationToken"]').value=r,"OK"!==n.status){t.next=26;break}return document.getElementById("sourceId").value=n.token,(c=document.querySelector('input[name="token-billing-checkbox"]:checked'))&&(document.querySelector('input[name="store_card"]').value=c.value),t.abrupt("return",document.getElementById("server_response").submit());case 26:document.getElementById("errors").textContent=n.errors[0].message,document.getElementById("errors").hidden=!1,e.target.parentElement.disabled=!1;case 29:case"end":return t.stop()}}),t,this,[[6,15]])}))),function(t){return l.apply(this,arguments)})},{key:"completePaymentUsingToken",value:(s=i(o.a.mark((function t(e){return o.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return e.target.parentElement.disabled=!0,t.abrupt("return",document.getElementById("server_response").submit());case 2:case"end":return t.stop()}}),t)}))),function(t){return s.apply(this,arguments)})},{key:"verifyBuyer",value:(u=i(o.a.mark((function t(e){var n,r;return o.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return console.log("in verify buyer"),n={amount:document.querySelector("meta[name=amount]").content,billingContact:document.querySelector("meta[name=square_contact]").content,currencyCode:document.querySelector("meta[name=currencyCode]").content,intent:"CHARGE"},t.next=4,this.payments.verifyBuyer(e,n);case 4:return r=t.sent,console.log(" verification toke = "+r.token),t.abrupt("return",r.token);case 7:case"end":return t.stop()}}),t,this)}))),function(t){return u.apply(this,arguments)})},{key:"handle",value:(a=i(o.a.mark((function t(){var e,n,r,a,c=this;return o.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return t.next=2,this.init();case 2:null===(e=document.getElementById("authorize-card"))||void 0===e||e.addEventListener("click",(function(t){return c.completePaymentWithoutToken(t)})),null===(n=document.getElementById("pay-now"))||void 0===n||n.addEventListener("click",(function(t){return document.querySelector("input[name=token]").value?c.completePaymentUsingToken(t):c.completePaymentWithoutToken(t)})),Array.from(document.getElementsByClassName("toggle-payment-with-token")).forEach((function(t){return t.addEventListener("click",(function(t){document.getElementById("card-container").classList.add("hidden"),document.getElementById("save-card--container").style.display="none",document.querySelector("input[name=token]").value=t.target.dataset.token}))})),null===(r=document.getElementById("toggle-payment-with-credit-card"))||void 0===r||r.addEventListener("click",function(){var t=i(o.a.mark((function t(e){return o.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:document.getElementById("card-container").classList.remove("hidden"),document.getElementById("save-card--container").style.display="grid",document.querySelector("input[name=token]").value="";case 3:case"end":return t.stop()}}),t)})));return function(e){return t.apply(this,arguments)}}()),document.querySelector(".toggle-payment-with-token")||null===(a=document.getElementById("toggle-payment-with-credit-card"))||void 0===a||a.click();case 8:case"end":return t.stop()}}),t,this)}))),function(){return a.apply(this,arguments)})}])&&c(e.prototype,n),r&&c(e,r),t}())).handle()},ls82:function(t,e,n){var r=function(t){"use strict";var e=Object.prototype,n=e.hasOwnProperty,r="function"==typeof Symbol?Symbol:{},o=r.iterator||"@@iterator",a=r.asyncIterator||"@@asyncIterator",i=r.toStringTag||"@@toStringTag";function c(t,e,n){return Object.defineProperty(t,e,{value:n,enumerable:!0,configurable:!0,writable:!0}),t[e]}try{c({},"")}catch(t){c=function(t,e,n){return t[e]=n}}function u(t,e,n,r){var o=e&&e.prototype instanceof d?e:d,a=Object.create(o.prototype),i=new E(r||[]);return a._invoke=function(t,e,n){var r="suspendedStart";return function(o,a){if("executing"===r)throw new Error("Generator is already running");if("completed"===r){if("throw"===o)throw a;return S()}for(n.method=o,n.arg=a;;){var i=n.delegate;if(i){var c=b(i,n);if(c){if(c===l)continue;return c}}if("next"===n.method)n.sent=n._sent=n.arg;else if("throw"===n.method){if("suspendedStart"===r)throw r="completed",n.arg;n.dispatchException(n.arg)}else"return"===n.method&&n.abrupt("return",n.arg);r="executing";var u=s(t,e,n);if("normal"===u.type){if(r=n.done?"completed":"suspendedYield",u.arg===l)continue;return{value:u.arg,done:n.done}}"throw"===u.type&&(r="completed",n.method="throw",n.arg=u.arg)}}}(t,n,i),a}function s(t,e,n){try{return{type:"normal",arg:t.call(e,n)}}catch(t){return{type:"throw",arg:t}}}t.wrap=u;var l={};function d(){}function f(){}function h(){}var p={};p[o]=function(){return this};var y=Object.getPrototypeOf,m=y&&y(y(L([])));m&&m!==e&&n.call(m,o)&&(p=m);var v=h.prototype=d.prototype=Object.create(p);function g(t){["next","throw","return"].forEach((function(e){c(t,e,(function(t){return this._invoke(e,t)}))}))}function w(t,e){var r;this._invoke=function(o,a){function i(){return new e((function(r,i){!function r(o,a,i,c){var u=s(t[o],t,a);if("throw"!==u.type){var l=u.arg,d=l.value;return d&&"object"==typeof d&&n.call(d,"__await")?e.resolve(d.__await).then((function(t){r("next",t,i,c)}),(function(t){r("throw",t,i,c)})):e.resolve(d).then((function(t){l.value=t,i(l)}),(function(t){return r("throw",t,i,c)}))}c(u.arg)}(o,a,r,i)}))}return r=r?r.then(i,i):i()}}function b(t,e){var n=t.iterator[e.method];if(void 0===n){if(e.delegate=null,"throw"===e.method){if(t.iterator.return&&(e.method="return",e.arg=void 0,b(t,e),"throw"===e.method))return l;e.method="throw",e.arg=new TypeError("The iterator does not provide a 'throw' method")}return l}var r=s(n,t.iterator,e.arg);if("throw"===r.type)return e.method="throw",e.arg=r.arg,e.delegate=null,l;var o=r.arg;return o?o.done?(e[t.resultName]=o.value,e.next=t.nextLoc,"return"!==e.method&&(e.method="next",e.arg=void 0),e.delegate=null,l):o:(e.method="throw",e.arg=new TypeError("iterator result is not an object"),e.delegate=null,l)}function k(t){var e={tryLoc:t[0]};1 in t&&(e.catchLoc=t[1]),2 in t&&(e.finallyLoc=t[2],e.afterLoc=t[3]),this.tryEntries.push(e)}function x(t){var e=t.completion||{};e.type="normal",delete e.arg,t.completion=e}function E(t){this.tryEntries=[{tryLoc:"root"}],t.forEach(k,this),this.reset(!0)}function L(t){if(t){var e=t[o];if(e)return e.call(t);if("function"==typeof t.next)return t;if(!isNaN(t.length)){var r=-1,a=function e(){for(;++r<t.length;)if(n.call(t,r))return e.value=t[r],e.done=!1,e;return e.value=void 0,e.done=!0,e};return a.next=a}}return{next:S}}function S(){return{value:void 0,done:!0}}return f.prototype=v.constructor=h,h.constructor=f,f.displayName=c(h,i,"GeneratorFunction"),t.isGeneratorFunction=function(t){var e="function"==typeof t&&t.constructor;return!!e&&(e===f||"GeneratorFunction"===(e.displayName||e.name))},t.mark=function(t){return Object.setPrototypeOf?Object.setPrototypeOf(t,h):(t.__proto__=h,c(t,i,"GeneratorFunction")),t.prototype=Object.create(v),t},t.awrap=function(t){return{__await:t}},g(w.prototype),w.prototype[a]=function(){return this},t.AsyncIterator=w,t.async=function(e,n,r,o,a){void 0===a&&(a=Promise);var i=new w(u(e,n,r,o),a);return t.isGeneratorFunction(n)?i:i.next().then((function(t){return t.done?t.value:i.next()}))},g(v),c(v,i,"Generator"),v[o]=function(){return this},v.toString=function(){return"[object Generator]"},t.keys=function(t){var e=[];for(var n in t)e.push(n);return e.reverse(),function n(){for(;e.length;){var r=e.pop();if(r in t)return n.value=r,n.done=!1,n}return n.done=!0,n}},t.values=L,E.prototype={constructor:E,reset:function(t){if(this.prev=0,this.next=0,this.sent=this._sent=void 0,this.done=!1,this.delegate=null,this.method="next",this.arg=void 0,this.tryEntries.forEach(x),!t)for(var e in this)"t"===e.charAt(0)&&n.call(this,e)&&!isNaN(+e.slice(1))&&(this[e]=void 0)},stop:function(){this.done=!0;var t=this.tryEntries[0].completion;if("throw"===t.type)throw t.arg;return this.rval},dispatchException:function(t){if(this.done)throw t;var e=this;function r(n,r){return i.type="throw",i.arg=t,e.next=n,r&&(e.method="next",e.arg=void 0),!!r}for(var o=this.tryEntries.length-1;o>=0;--o){var a=this.tryEntries[o],i=a.completion;if("root"===a.tryLoc)return r("end");if(a.tryLoc<=this.prev){var c=n.call(a,"catchLoc"),u=n.call(a,"finallyLoc");if(c&&u){if(this.prev<a.catchLoc)return r(a.catchLoc,!0);if(this.prev<a.finallyLoc)return r(a.finallyLoc)}else if(c){if(this.prev<a.catchLoc)return r(a.catchLoc,!0)}else{if(!u)throw new Error("try statement without catch or finally");if(this.prev<a.finallyLoc)return r(a.finallyLoc)}}}},abrupt:function(t,e){for(var r=this.tryEntries.length-1;r>=0;--r){var o=this.tryEntries[r];if(o.tryLoc<=this.prev&&n.call(o,"finallyLoc")&&this.prev<o.finallyLoc){var a=o;break}}a&&("break"===t||"continue"===t)&&a.tryLoc<=e&&e<=a.finallyLoc&&(a=null);var i=a?a.completion:{};return i.type=t,i.arg=e,a?(this.method="next",this.next=a.finallyLoc,l):this.complete(i)},complete:function(t,e){if("throw"===t.type)throw t.arg;return"break"===t.type||"continue"===t.type?this.next=t.arg:"return"===t.type?(this.rval=this.arg=t.arg,this.method="return",this.next="end"):"normal"===t.type&&e&&(this.next=e),l},finish:function(t){for(var e=this.tryEntries.length-1;e>=0;--e){var n=this.tryEntries[e];if(n.finallyLoc===t)return this.complete(n.completion,n.afterLoc),x(n),l}},catch:function(t){for(var e=this.tryEntries.length-1;e>=0;--e){var n=this.tryEntries[e];if(n.tryLoc===t){var r=n.completion;if("throw"===r.type){var o=r.arg;x(n)}return o}}throw new Error("illegal catch attempt")},delegateYield:function(t,e,n){return this.delegate={iterator:L(t),resultName:e,nextLoc:n},"next"===this.method&&(this.arg=void 0),l}},t}(t.exports);try{regeneratorRuntime=r}catch(t){Function("r","regeneratorRuntime = r")(r)}},o0o1:function(t,e,n){t.exports=n("ls82")}});