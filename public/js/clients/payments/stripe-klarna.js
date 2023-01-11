/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!********************************************************!*\
  !*** ./resources/js/clients/payments/stripe-klarna.js ***!
  \********************************************************/
var _document$querySelect, _document$querySelect2, _document$querySelect3, _document$querySelect4;

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

/**
 * Invoice Ninja (https://invoiceninja.com)
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2021. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */
var ProcessKlarna = /*#__PURE__*/_createClass(function ProcessKlarna(key, stripeConnect) {
  var _this = this;

  _classCallCheck(this, ProcessKlarna);

  _defineProperty(this, "setupStripe", function () {
    if (_this.stripeConnect) {
      // this.stripe.stripeAccount = this.stripeConnect;
      _this.stripe = Stripe(_this.key, {
        stripeAccount: _this.stripeConnect
      });
    } else {
      _this.stripe = Stripe(_this.key);
    }

    return _this;
  });

  _defineProperty(this, "handleError", function (message) {
    document.getElementById('pay-now').disabled = false;
    document.querySelector('#pay-now > svg').classList.add('hidden');
    document.querySelector('#pay-now > span').classList.remove('hidden');
    _this.errors.textContent = '';
    _this.errors.textContent = message;
    _this.errors.hidden = false;
  });

  _defineProperty(this, "handle", function () {
    document.getElementById('pay-now').addEventListener('click', function (e) {
      var errors = document.getElementById('errors');
      var name = document.getElementById("klarna-name").value;

      if (!/^[A-Za-z\s]*$/.test(name)) {
        document.getElementById('klarna-name-correction').hidden = false;
        document.getElementById('klarna-name').textContent = name.replace(/^[A-Za-z\s]*$/, "");
        document.getElementById('klarna-name').focus();
        errors.textContent = document.querySelector('meta[name=translation-name-without-special-characters]').content;
        errors.hidden = false;
      } else {
        document.getElementById('pay-now').disabled = true;
        document.querySelector('#pay-now > svg').classList.remove('hidden');
        document.querySelector('#pay-now > span').classList.add('hidden');

        _this.stripe.confirmKlarnaPayment(document.querySelector('meta[name=pi-client-secret').content, {
          payment_method: {
            billing_details: {
              name: name,
              email: document.querySelector('meta[name=email]').content,
              address: {
                line1: document.querySelector('meta[name=address-1]').content,
                line2: document.querySelector('meta[name=address-2]').content,
                city: document.querySelector('meta[name=city]').content,
                postal_code: document.querySelector('meta[name=postal_code]').content,
                state: document.querySelector('meta[name=state]').content,
                country: document.querySelector('meta[name=country]').content
              }
            }
          },
          return_url: document.querySelector('meta[name="return-url"]').content
        }).then(function (result) {
          if (result.hasOwnProperty('error')) {
            return _this.handleError(result.error.message);
          }
        });
      }
    });
  });

  this.key = key;
  this.errors = document.getElementById('errors');
  this.stripeConnect = stripeConnect;
});

var publishableKey = (_document$querySelect = (_document$querySelect2 = document.querySelector('meta[name="stripe-publishable-key"]')) === null || _document$querySelect2 === void 0 ? void 0 : _document$querySelect2.content) !== null && _document$querySelect !== void 0 ? _document$querySelect : '';
var stripeConnect = (_document$querySelect3 = (_document$querySelect4 = document.querySelector('meta[name="stripe-account-id"]')) === null || _document$querySelect4 === void 0 ? void 0 : _document$querySelect4.content) !== null && _document$querySelect3 !== void 0 ? _document$querySelect3 : '';
new ProcessKlarna(publishableKey, stripeConnect).setupStripe().handle();
/******/ })()
;