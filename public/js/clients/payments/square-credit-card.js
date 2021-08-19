/*! For license information please see square-credit-card.js.LICENSE.txt */
!function(t){var e={};function r(n){if(e[n])return e[n].exports;var o=e[n]={i:n,l:!1,exports:{}};return t[n].call(o.exports,o,o.exports,r),o.l=!0,o.exports}r.m=t,r.c=e,r.d=function(t,e,n){r.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:n})},r.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},r.t=function(t,e){if(1&e&&(t=r(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var n=Object.create(null);if(r.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var o in t)r.d(n,o,function(e){return t[e]}.bind(null,o));return n},r.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return r.d(e,"a",e),e},r.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},r.p="/",r(r.s=23)}({23:function(t,e,r){t.exports=r("flnV")},flnV:function(t,e,r){"use strict";r.r(e);var n=r("o0o1"),o=r.n(n);function i(t,e,r,n,o,i,a){try{var c=t[i](a),u=c.value}catch(t){return void r(t)}c.done?e(u):Promise.resolve(u).then(n,o)}function a(t){return function(){var e=this,r=arguments;return new Promise((function(n,o){var a=t.apply(e,r);function c(t){i(a,n,o,c,u,"next",t)}function u(t){i(a,n,o,c,u,"throw",t)}c(void 0)}))}}function c(t,e){for(var r=0;r<e.length;r++){var n=e[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(t,n.key,n)}}(new(function(){function t(){!function(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}(this,t),this.appId=document.querySelector("meta[name=square-appId]").content,this.locationId=document.querySelector("meta[name=square-locationId]").content,this.isLoaded=!1}var e,r,n,i,u,s,l;return e=t,(r=[{key:"init",value:(l=a(o.a.mark((function t(){var e;return o.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return this.payments=Square.payments(this.appId,this.locationId),t.next=3,this.payments.card();case 3:return this.card=t.sent,t.next=6,this.card.attach("#card-container");case 6:this.isLoaded=!0,(e=document.querySelector(".sq-card-iframe-container"))&&e.setAttribute("style","150px !important"),document.querySelector(".toggle-payment-with-token")&&document.getElementById("card-container").classList.add("hidden");case 11:case"end":return t.stop()}}),t,this)}))),function(){return l.apply(this,arguments)})},{key:"completePaymentWithoutToken",value:(s=a(o.a.mark((function t(e){var r,n;return o.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return document.getElementById("errors").hidden=!0,e.target.parentElement.disabled=!0,t.next=4,this.card.tokenize();case 4:if("OK"!==(r=t.sent).status){t.next=10;break}return document.getElementById("sourceId").value=r.token,(n=document.querySelector('input[name="token-billing-checkbox"]:checked'))&&(document.querySelector('input[name="store_card"]').value=n.value),t.abrupt("return",document.getElementById("server_response").submit());case 10:document.getElementById("errors").textContent=r.errors[0].message,document.getElementById("errors").hidden=!1,e.target.parentElement.disabled=!1;case 13:case"end":return t.stop()}}),t,this)}))),function(t){return s.apply(this,arguments)})},{key:"completePaymentUsingToken",value:(u=a(o.a.mark((function t(e){return o.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return e.target.parentElement.disabled=!0,t.abrupt("return",document.getElementById("server_response").submit());case 2:case"end":return t.stop()}}),t)}))),function(t){return u.apply(this,arguments)})},{key:"handle",value:(i=a(o.a.mark((function t(){var e,r,n,i,c=this;return o.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return t.next=2,this.init();case 2:null===(e=document.getElementById("authorize-card"))||void 0===e||e.addEventListener("click",(function(t){return c.completePaymentWithoutToken(t)})),null===(r=document.getElementById("pay-now"))||void 0===r||r.addEventListener("click",(function(t){return document.querySelector("input[name=token]").value?c.completePaymentUsingToken(t):c.completePaymentWithoutToken(t)})),Array.from(document.getElementsByClassName("toggle-payment-with-token")).forEach((function(t){return t.addEventListener("click",(function(t){document.getElementById("card-container").classList.add("hidden"),document.getElementById("save-card--container").style.display="none",document.querySelector("input[name=token]").value=t.target.dataset.token}))})),null===(n=document.getElementById("toggle-payment-with-credit-card"))||void 0===n||n.addEventListener("click",function(){var t=a(o.a.mark((function t(e){return o.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:document.getElementById("card-container").classList.remove("hidden"),document.getElementById("save-card--container").style.display="grid",document.querySelector("input[name=token]").value="";case 3:case"end":return t.stop()}}),t)})));return function(e){return t.apply(this,arguments)}}()),document.querySelector(".toggle-payment-with-token")||null===(i=document.getElementById("toggle-payment-with-credit-card"))||void 0===i||i.click();case 8:case"end":return t.stop()}}),t,this)}))),function(){return i.apply(this,arguments)})}])&&c(e.prototype,r),n&&c(e,n),t}())).handle()},ls82:function(t,e,r){var n=function(t){"use strict";var e=Object.prototype,r=e.hasOwnProperty,n="function"==typeof Symbol?Symbol:{},o=n.iterator||"@@iterator",i=n.asyncIterator||"@@asyncIterator",a=n.toStringTag||"@@toStringTag";function c(t,e,r){return Object.defineProperty(t,e,{value:r,enumerable:!0,configurable:!0,writable:!0}),t[e]}try{c({},"")}catch(t){c=function(t,e,r){return t[e]=r}}function u(t,e,r,n){var o=e&&e.prototype instanceof d?e:d,i=Object.create(o.prototype),a=new k(n||[]);return i._invoke=function(t,e,r){var n="suspendedStart";return function(o,i){if("executing"===n)throw new Error("Generator is already running");if("completed"===n){if("throw"===o)throw i;return I()}for(r.method=o,r.arg=i;;){var a=r.delegate;if(a){var c=b(a,r);if(c){if(c===l)continue;return c}}if("next"===r.method)r.sent=r._sent=r.arg;else if("throw"===r.method){if("suspendedStart"===n)throw n="completed",r.arg;r.dispatchException(r.arg)}else"return"===r.method&&r.abrupt("return",r.arg);n="executing";var u=s(t,e,r);if("normal"===u.type){if(n=r.done?"completed":"suspendedYield",u.arg===l)continue;return{value:u.arg,done:r.done}}"throw"===u.type&&(n="completed",r.method="throw",r.arg=u.arg)}}}(t,r,a),i}function s(t,e,r){try{return{type:"normal",arg:t.call(e,r)}}catch(t){return{type:"throw",arg:t}}}t.wrap=u;var l={};function d(){}function f(){}function h(){}var p={};p[o]=function(){return this};var y=Object.getPrototypeOf,m=y&&y(y(L([])));m&&m!==e&&r.call(m,o)&&(p=m);var v=h.prototype=d.prototype=Object.create(p);function g(t){["next","throw","return"].forEach((function(e){c(t,e,(function(t){return this._invoke(e,t)}))}))}function w(t,e){var n;this._invoke=function(o,i){function a(){return new e((function(n,a){!function n(o,i,a,c){var u=s(t[o],t,i);if("throw"!==u.type){var l=u.arg,d=l.value;return d&&"object"==typeof d&&r.call(d,"__await")?e.resolve(d.__await).then((function(t){n("next",t,a,c)}),(function(t){n("throw",t,a,c)})):e.resolve(d).then((function(t){l.value=t,a(l)}),(function(t){return n("throw",t,a,c)}))}c(u.arg)}(o,i,n,a)}))}return n=n?n.then(a,a):a()}}function b(t,e){var r=t.iterator[e.method];if(void 0===r){if(e.delegate=null,"throw"===e.method){if(t.iterator.return&&(e.method="return",e.arg=void 0,b(t,e),"throw"===e.method))return l;e.method="throw",e.arg=new TypeError("The iterator does not provide a 'throw' method")}return l}var n=s(r,t.iterator,e.arg);if("throw"===n.type)return e.method="throw",e.arg=n.arg,e.delegate=null,l;var o=n.arg;return o?o.done?(e[t.resultName]=o.value,e.next=t.nextLoc,"return"!==e.method&&(e.method="next",e.arg=void 0),e.delegate=null,l):o:(e.method="throw",e.arg=new TypeError("iterator result is not an object"),e.delegate=null,l)}function x(t){var e={tryLoc:t[0]};1 in t&&(e.catchLoc=t[1]),2 in t&&(e.finallyLoc=t[2],e.afterLoc=t[3]),this.tryEntries.push(e)}function E(t){var e=t.completion||{};e.type="normal",delete e.arg,t.completion=e}function k(t){this.tryEntries=[{tryLoc:"root"}],t.forEach(x,this),this.reset(!0)}function L(t){if(t){var e=t[o];if(e)return e.call(t);if("function"==typeof t.next)return t;if(!isNaN(t.length)){var n=-1,i=function e(){for(;++n<t.length;)if(r.call(t,n))return e.value=t[n],e.done=!1,e;return e.value=void 0,e.done=!0,e};return i.next=i}}return{next:I}}function I(){return{value:void 0,done:!0}}return f.prototype=v.constructor=h,h.constructor=f,f.displayName=c(h,a,"GeneratorFunction"),t.isGeneratorFunction=function(t){var e="function"==typeof t&&t.constructor;return!!e&&(e===f||"GeneratorFunction"===(e.displayName||e.name))},t.mark=function(t){return Object.setPrototypeOf?Object.setPrototypeOf(t,h):(t.__proto__=h,c(t,a,"GeneratorFunction")),t.prototype=Object.create(v),t},t.awrap=function(t){return{__await:t}},g(w.prototype),w.prototype[i]=function(){return this},t.AsyncIterator=w,t.async=function(e,r,n,o,i){void 0===i&&(i=Promise);var a=new w(u(e,r,n,o),i);return t.isGeneratorFunction(r)?a:a.next().then((function(t){return t.done?t.value:a.next()}))},g(v),c(v,a,"Generator"),v[o]=function(){return this},v.toString=function(){return"[object Generator]"},t.keys=function(t){var e=[];for(var r in t)e.push(r);return e.reverse(),function r(){for(;e.length;){var n=e.pop();if(n in t)return r.value=n,r.done=!1,r}return r.done=!0,r}},t.values=L,k.prototype={constructor:k,reset:function(t){if(this.prev=0,this.next=0,this.sent=this._sent=void 0,this.done=!1,this.delegate=null,this.method="next",this.arg=void 0,this.tryEntries.forEach(E),!t)for(var e in this)"t"===e.charAt(0)&&r.call(this,e)&&!isNaN(+e.slice(1))&&(this[e]=void 0)},stop:function(){this.done=!0;var t=this.tryEntries[0].completion;if("throw"===t.type)throw t.arg;return this.rval},dispatchException:function(t){if(this.done)throw t;var e=this;function n(r,n){return a.type="throw",a.arg=t,e.next=r,n&&(e.method="next",e.arg=void 0),!!n}for(var o=this.tryEntries.length-1;o>=0;--o){var i=this.tryEntries[o],a=i.completion;if("root"===i.tryLoc)return n("end");if(i.tryLoc<=this.prev){var c=r.call(i,"catchLoc"),u=r.call(i,"finallyLoc");if(c&&u){if(this.prev<i.catchLoc)return n(i.catchLoc,!0);if(this.prev<i.finallyLoc)return n(i.finallyLoc)}else if(c){if(this.prev<i.catchLoc)return n(i.catchLoc,!0)}else{if(!u)throw new Error("try statement without catch or finally");if(this.prev<i.finallyLoc)return n(i.finallyLoc)}}}},abrupt:function(t,e){for(var n=this.tryEntries.length-1;n>=0;--n){var o=this.tryEntries[n];if(o.tryLoc<=this.prev&&r.call(o,"finallyLoc")&&this.prev<o.finallyLoc){var i=o;break}}i&&("break"===t||"continue"===t)&&i.tryLoc<=e&&e<=i.finallyLoc&&(i=null);var a=i?i.completion:{};return a.type=t,a.arg=e,i?(this.method="next",this.next=i.finallyLoc,l):this.complete(a)},complete:function(t,e){if("throw"===t.type)throw t.arg;return"break"===t.type||"continue"===t.type?this.next=t.arg:"return"===t.type?(this.rval=this.arg=t.arg,this.method="return",this.next="end"):"normal"===t.type&&e&&(this.next=e),l},finish:function(t){for(var e=this.tryEntries.length-1;e>=0;--e){var r=this.tryEntries[e];if(r.finallyLoc===t)return this.complete(r.completion,r.afterLoc),E(r),l}},catch:function(t){for(var e=this.tryEntries.length-1;e>=0;--e){var r=this.tryEntries[e];if(r.tryLoc===t){var n=r.completion;if("throw"===n.type){var o=n.arg;E(r)}return o}}throw new Error("illegal catch attempt")},delegateYield:function(t,e,r){return this.delegate={iterator:L(t),resultName:e,nextLoc:r},"next"===this.method&&(this.arg=void 0),l}},t}(t.exports);try{regeneratorRuntime=n}catch(t){Function("r","regeneratorRuntime = r")(n)}},o0o1:function(t,e,r){t.exports=r("ls82")}});