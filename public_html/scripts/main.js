"use strict";
(function ($) {
  let app = $.sammy('#main', function () {
    this.use('Template');
    const API = 'api.php';

    let init = null;
    let expiry = null;
    let token = null;

    const printError = function (error) {
      let fError = error.responseJSON.message.toString();
      $('#errors').append(`<div id="error" class="alert alert-danger" role="alert"><strong>Error: </strong>${fError}</div>`);
      window.scrollTo(0,0);
    };

    const printSuccess = function (success) {
      if (!success) {
        success = 'Changes saved successfully!';
      }
      let fSuccess = success.toString();
      $('#errors').append(`<div id="success" class="success alert-success" role="success"><strong>Success: </strong>${fSuccess}</div>`);
      window.scrollTo(0,0);
    };

    this.before('#/', function () {
      // if (document.contains(document.getElementById('success'))) {
      //   document.getElementById('success').remove();
      // }
      // if (document.contains(document.getElementById('error'))) {
      //   document.getElementById('error').remove();
      // }
      $('#errors').empty();
    });

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

    // Routes!

    this.get('#/', function (context) {
      context.app.swap('');
      context.render('templates/home.template')
        .appendTo(context.$element());
    });

    this.get('#/patient', function (context) {
      context.app.swap('');
      $.ajax({
        type: "POST",
        url: API,
        data: {
          token: Cookies.get('token'),
          request: 'patient'
        },
        success: function (data) {
          let formattedData = jQuery.parseJSON(JSON.stringify(data));
          //context.log(formattedData);
          context.render('templates/patient.template', {patient: formattedData.patient})
            .appendTo(context.$element());
        },
        error: function (data) {
          printError(jQuery.parseJSON(JSON.stringify(data)));
        }
      });
    });

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
          context.log(data);
          printSuccess();
        },
        error: function (data) {
          printError(jQuery.parseJSON(JSON.stringify(data)));
        }
      });
    });

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

    this.get('#/details', function (context) {

    });

    this.get('#/sportscentres', function (context) {
    });

    this.get('#/logout', function (context) {
      //document.cookie = "token=;expires= Thu, 01 Jan 1970 00:00:00 GMT";
      Cookies.remove('token');
      Cookies.remove('expiry');
      this.redirect('#/');
    })

  });
  $(function () {
    app.run('#/');
  });
})(jQuery);
