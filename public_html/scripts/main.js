"use strict";
(function ($) {
  let app = $.sammy('#main', function () {
    // Plugins
    this.use('Template');

    const API = 'api.php';
    let init = null;
    let expiry = null;
    let token = null;

    // Success Flash Helper Function
    const printSuccess = function (success) {
      if (!success) {
        success = 'Changes saved successfully!';
      }
      let fSuccess = success.toString();
      $('#errors').append(`<div id="success" class="alert alert-success" role="success"><strong>Success: </strong>${fSuccess}</div>`);
      window.scrollTo(0,0);
    };

    // Error Flash Helper Function
    const printError = function (error) {
      let fError = error.responseJSON.message.toString();
      $('#errors').append(`<div id="error" class="alert alert-danger" role="alert"><strong>Error: </strong>${fError}</div>`);
      window.scrollTo(0,0);
    };

    // Error Clearing Middleware
    this.before('#/', function () {
      $('#errors').empty();
    });

    // Authentication Middleware
    this.before('#/', function() {
      // The server will enforce token expiry. This just helps keep things clean.
      if (!init) {
        if (!Cookies.get('token' && !Cookies.get('expiry'))) {
          // Not logged in
          alert('You have to be logged in to do that.');
          window.location.href = 'index.html';
          return;
        }
        // Logged in
        init =      true;
        expiry =    new Date(Cookies.get('expiry'));
        token =     Cookies.get('token');
      }
      // Expiry check
      if ((Date.now() > expiry || !Cookies.get('token') || !Cookies.get('expiry')) && init === true) {
        alert('Your session has timed out! Returning you to the login page.');
        window.location.href = 'index.html';
      }
    });

    // Root
    this.get('#/', function (context) {
      context.partial('templates/home.template');
    });

    // Patient Page
    this.get('#/patient', function (context) {
      $.ajax({
        type: "POST",
        url: API,
        data: {
          token: Cookies.get('token'),
          request: 'patient'
        },
        success: function (data) {
          let formattedData = jQuery.parseJSON(JSON.stringify(data));
          context.partial('templates/patient.template', {patient: formattedData.patient});
        },
        error: function (data) {
          printError(jQuery.parseJSON(JSON.stringify(data)));
        }
      });
    });

    // Regimes Page
    this.get('#/regimes', function (context) {
      $.ajax ({
        type: "POST",
        url: API,
        data: {
          token: Cookies.get('token'),
          request: 'regimes'
        },
        success: function (data) {
          let formattedData = jQuery.parseJSON(JSON.stringify(data));
          context.partial('templates/regimes.template', {regimes: formattedData.regimes});
        },
        error: function (data) {
          printError(jQuery.parseJSON(JSON.stringify(data)));
        }
      })
    });

    // Sports Centres Page
    this.get('#/sportscentres', function (context) {
    });

    // Patient Address Form
    this.post('#/patient/address', function (context) {
      $.ajax({
        type: "POST",
        url: API,
        data: {
          token: Cookies.get('token'),
          request: 'patient-address',
          address1: $('#address-1').val(),
          address2: $('#address-2').val(),
          town: $('#town').val(),
          postcode: $('#postcode').val()
        },
        success: function (data) {
          printSuccess();
        },
        error: function (data) {
          printError(jQuery.parseJSON(JSON.stringify(data)));
        }
      });
    });

    // Patient Email Form
    this.post('#/patient/email', function (context) {
      $.ajax({
        type: "POST",
        url: API,
        data: {
          token: Cookies.get('token'),
          request: 'patient-email',
          email: $('#email-address').val()
        },
        success: function (data) {
          context.log(data);
          printSuccess();
        },
        error: function (data) {
          printError(jQuery.parseJSON(JSON.stringify(data)))
        }
      });
    });

    // Patient Email Subscription Form
    this.post('#/patient/subscription', function (context) {
      $.ajax({
        type: "POST",
        url: API,
        data: {
          token: Cookies.get('token'),
          request: 'patient-subscription',
          subscription: $('#checkbox').is(":checked")
        },
        success: function (data) {
          context.log(data);
          printSuccess();
        },
        error: function (data) {
          printError(jQuery.parseJSON(JSON.stringify(data)))
        }
      });
    });

    // Log Out
    this.get('#/logout', function (context) {
      Cookies.remove('token');
      Cookies.remove('expiry');
      this.redirect('#/');
    })

  });
  $(function () {
    app.run('#/');
  });
})(jQuery);
