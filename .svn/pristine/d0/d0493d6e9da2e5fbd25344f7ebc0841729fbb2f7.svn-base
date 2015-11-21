/*
* Licensed to the Apache Software Foundation (ASF) under one
* or more contributor license agreements.  See the NOTICE file
* distributed with this work for additional information
* regarding copyright ownership.  The ASF licenses this file
* to you under the Apache License, Version 2.0 (the
* "License"); you may not use this file except in compliance
* with the License.  You may obtain a copy of the License at
*
* http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing,
* software distributed under the License is distributed on an
* "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
* KIND, either express or implied.  See the License for the
* specific language governing permissions and limitations
* under the License.
*/
var app = {
   // Application Constructor
   initialize: function() {
       this.initPaymentUI();
   },
   initPaymentUI : function () {
     var clientIDs = {
       "PayPalEnvironmentProduction": "YOUR_PRODUCTION_CLIENT_ID",
       "PayPalEnvironmentSandbox": "AZAi0RFtvUH1cM2fIg1FJYbmDcM1WIMmnTm8y14j8h0l3kKF9mfehHXUo2CQQi7uqZc3V7_r-FBwKu_9"
     };
     PayPalMobile.init(clientIDs, app.onPayPalMobileInit);

   },
   onSuccesfulPayment : function(payment) {
     console.log("payment success: " + JSON.stringify(payment, null, 4));
     window.localStorage.setItem("paypal_status",payment);
     alert(payment);
   //  window.location.replace('myapp://#/tab/cart');
   },
   onAuthorizationCallback : function(authorization) {
     console.log("authorization: " + JSON.stringify(authorization, null, 4));
   },
   createPayment : function (t) {
     // for simplicity use predefined amount
     // optional payment details for more information check [helper js file](https://github.com/paypal/PayPal-Cordova-Plugin/blob/master/www/paypal-mobile-js-helper.js)
    
     var paymentDetails = new PayPalPaymentDetails(t, "0.00", "0.00");
     var payment = new PayPalPayment(t, "GBP", "Total amount", "Sale", paymentDetails);
     return payment;
   },
   configuration : function () {
     // for more options see `paypal-mobile-js-helper.js`
     var config = new PayPalConfiguration({merchantName: "My test shop", merchantPrivacyPolicyURL: "https://mytestshop.com/policy", merchantUserAgreementURL: "https://mytestshop.com/agreement"});
     return config;
   },
   onPrepareRender : function() {
     var buyNowBtn = document.getElementById("buyNowBtn");
     var buyInFutureBtn = document.getElementById("buyInFutureBtn");
     var profileSharingBtn = document.getElementById("profileSharingBtn");
     var totalAmount=document.getElementById("grndamount").value;
     buyNowBtn.onclick = function(e) {
       // single payment
      PayPalMobile.renderSinglePaymentUI(app.createPayment(totalAmount), app.onSuccesfulPayment, app.onUserCanceled);

     };

/*     buyInFutureBtn.onclick = function(e) {
       // future payment
       PayPalMobile.renderFuturePaymentUI(app.onAuthorizationCallback, app.onUserCanceled);
     };

     profileSharingBtn.onclick = function(e) {
       // profile sharing
       PayPalMobile.renderProfileSharingUI(["profile", "email", "phone", "address", "futurepayments", "paypalattributes"], app.onAuthorizationCallback, app.onUserCanceled);
     };*/
   },
   onPayPalMobileInit : function() {
     // must be called
     // use PayPalEnvironmentNoNetwork mode to get look and feel of the flow
     PayPalMobile.prepareToRender("PayPalEnvironmentSandbox", app.configuration(), app.onPrepareRender);
   },
   onUserCanceled : function(result) {
    window.localStorage.setItem("paypal_status",result);
      /*window.location.replace('myapp://#/tab/cart');*/
   }
};

