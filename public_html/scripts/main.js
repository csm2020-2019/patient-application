"use strict";
(function ($) {
  let app = $.sammy('#main', function () {
    // Plugins
    this.use('Template');

    const API = 'api.php';
    let init = null;
    let token = null;

    /**
     * Print Success Helper Function
     * @author Oliver Earl <ole4@aber.ac.uk>
     * @param success
     *
     * Simply prints success information to the DOM using a Bootstrap success alert.
     */
    const printSuccess = function(success) {
      if (!success) {
        success = 'Changes saved successfully!';
      }
      let fSuccess = success.toString();
      $('#errors').append(
        `<div id="success" class="alert alert-success alert-dismissible fade show" role="success">
         <strong>Success:</strong> ${fSuccess} - <em>${new Date(Date.now()).toUTCString()}</em>
         <button type="button" class="close" data-dismiss="alert" aria-label="Close">
         <span aria-hidden="true">&times;</span>
         </button>
         </div>`);
      window.scrollTo(0,0);
    };

    /**
     * Print Error Helper Function
     * @author Oliver Earl <ole4@aber.ac.uk>
     * @param error
     *
     * Simply prints error information to the DOM using a Bootstrap error alert.
     */
    const printError = function(error) {
      let fError = error.responseJSON.message.toString();
      $('#errors').append(
        `<div id="error" class="alert alert-danger alert-dismissible fade show" role="alert">
         <strong>Error: </strong>${fError} - <em>${new Date(Date.now()).toUTCString()}</em>
         <button type="button" class="close" data-dismiss="alert" aria-label="Close">
         <span aria-hidden="true">&times;</span>
         </button>
         </div>`);
      window.scrollTo(0,0);
    };

    /**
     * Dialogue Middleware
     * @author Oliver Earl <ole4@aber.ac.uk>
     *
     * Deletes Bootstrap errors and success dialogues on each route change.
     */
    this.before('#/', function() {
      $('#errors').empty();
    });

    /**
     * Authentication Middleware
     * @author Oliver Earl <ole4@aber.ac.uk>
     *
     * This middleware serves two main functions - whilst ultimately the backend API will prohibit data from being
     * accessed or requests being made without a valid, or expired, token, for a smooth end-user experience it's a good
     * idea to expire the user session and delete cookies, forcing a logout, once two hours from the program initiating
     * has elapsed.
     *
     * This function also ensures the program can't be navigated to without a valid login cookie.
     */
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
        //expiry =    new Date(Cookies.get('expiry'));
        token =     Cookies.get('token');
      }
      // Expiry check
      if ((Date.now() > Cookies.get('expiry') || !Cookies.get('token') || !Cookies.get('expiry')) && init === true) {
        alert('Your session has timed out! Returning you to the login page.');
        window.location.href = 'index.html';
      }
    });

    /**
     * Root Route
     * @author Oliver Earl <ole4@aber.ac.uk>
     *
     * Loads the homepage of the web application.
     */
    this.get('#/', function(context) {
      context.partial('templates/home.template').swap();
    });

    /**
     * Patient Route
     * @author Oliver Earl <ole4@aber.ac.uk>
     *
     * Displays the patient information, both that which can be edited, and that which is read-only.
     */
    this.get('#/patient', function (context) {
      $.ajax({
        type: "POST",
        url: API,
        data: {
          token: Cookies.get('token'),
          request: 'patient'
        },
        success: function(data) {
          let formattedData = jQuery.parseJSON(JSON.stringify(data));
          context.partial('templates/patient.template', {patient: formattedData.patient}).swap();
        },
        error: function(data) {
          printError(jQuery.parseJSON(JSON.stringify(data)));
        }
      });
    });

    /**
     * Regimes Route
     * @author Oliver Earl <ole4@aber.ac.uk>
     *
     * Displays the current user's assigned exercise regimes.
     */
    this.get('#/regimes', function(context) {
      $.ajax ({
        type: "POST",
        url: API,
        data: {
          token: Cookies.get('token'),
          request: 'regimes'
        },
        success: function(data) {
          let formattedData = jQuery.parseJSON(JSON.stringify(data));
          context.partial('templates/regimes.template', {regimes: formattedData.regimes}).swap();
        },
        error: function(data) {
          printError(jQuery.parseJSON(JSON.stringify(data)));
        }
      });
    });

    /**
     * Specific Regime / Trials Route
     * @author Oliver Earl <ole4@aber.ac.uk>
     *
     * Displays specific information to a specific regime, identified by an ID (rid) parameter. The information
     * returned from the API will also contain a subarray of trials, if any, and the GP who assigned the patient to
     * this specific regime.
     */
    this.get('#/regimes/:id', function(context) {
      let id = this.params['id'];
      $.ajax({
        type: "POST",
        url: API,
        data: {
          token: Cookies.get('token'),
          request: 'regime',
          regime_id: id
        },
        success: function(data) {
          let formattedData = jQuery.parseJSON(JSON.stringify(data));
          context.partial('templates/regime.template',
            {regime: formattedData.regime, trials: formattedData.trials}).swap();
        },
        error: function(data) {
          printError(jQuery.parseJSON(JSON.stringify(data)));
        }
      });
    });

    /**
     * Sports Centres Route
     * @author Oliver Earl <ole4@aber.ac.uk>
     *
     * Displays all available sports centres, including the user's current preferred (assigned, or 'appointed') sports
     * centre.
     */
    this.get('#/sportscentres', function (context) {
      $.ajax({
        type: "POST",
        url: API,
        data: {
          token: Cookies.get('token'),
          request: 'sportscentres'
        },
        success: function(data) {
          let formattedData = jQuery.parseJSON(JSON.stringify(data));
          context.partial('templates/sportscentres.template', {sportscentres: formattedData.sportscentres,
            appointment: formattedData.appointment}).swap();
        },
        error: function(data) {
          printError(jQuery.parseJSON(JSON.stringify(data)));
        }
      });
    });

    /**
     * Patient Address Route
     * @author Oliver Earl <ole4@aber.ac.uk>
     *
     * Allows the user to update their address - taking two address strings, a town or city, and a postcode. They are
     * later concatenated together for storage as a single value.
     */
    this.post('#/patient/address', function() {
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
        success: function() {
          printSuccess();
          location.reload();
        },
        error: function(data) {
          printError(jQuery.parseJSON(JSON.stringify(data)));
        }
      });
    });

    /**
     * Patient Email Route
     * @author Oliver Earl <ole4@aber.ac.uk>
     *
     * Allows the user to replace their email address on record. It's important to note that this is their patient
     * email address - not their user one.
     */
    this.post('#/patient/email', function() {
      $.ajax({
        type: "POST",
        url: API,
        data: {
          token: Cookies.get('token'),
          request: 'patient-email',
          email: $('#email-address').val()
        },
        success: function() {
          app.refresh();
          printSuccess();
        },
        error: function(data) {
          printError(jQuery.parseJSON(JSON.stringify(data)))
        }
      });
    });

    /**
     * Patient Email Subscription Route
     * @author Oliver Earl <ole4@aber.ac.uk>
     *
     * Allows the patient to change their prescription status to the email subscription service. All this does, that
     * the web-side of things needs to be aware of, is change the database. The backend doesn't fire off any emails
     * itself.
     */
    this.post('#/patient/subscription', function() {
      $.ajax({
        type: "POST",
        url: API,
        data: {
          token: Cookies.get('token'),
          request: 'patient-subscription',
          subscription: $('#checkbox').is(":checked")
        },
        success: function() {
          printSuccess();
        },
        error: function(data) {
          printError(jQuery.parseJSON(JSON.stringify(data)))
        }
      });
    });

    /**
     * Sports Centre Assignment Route
     * @author Oliver Earl <ole4@aber.ac.uk>
     *
     * This route changes the patient's 'appointed' sports centre - referred to as their assigned or preferred sports
     * centre in different areas. By assigning a patient to a specific sports centre, it will un-assign them from any
     * other sports centre that they are formerly assigned to.
     *
     * Users can't be assigned to unavailable sports centres, but will in theory, continue to be assigned to a sports
     * centre even if it changes its status to unavailable, unless this is changed by the Java application.
     */
    this.post('#/appointment/:id', function(context) {
      let id = this.params['id'];
      $.ajax({
        type: "POST",
        url: API,
        data: {
          token: Cookies.get('token'),
          request: 'appointment',
          appointment: id
        },
        success: function() {
          context.redirect('#/sportscentres');
          location.reload();
          printSuccess('Sports Centre re-assigned!');
        },
        error: function(data) {
          printError(jQuery.parseJSON(JSON.stringify(data)))
        }
      });
    });

    /**
     * Feedback Route
     * @author Oliver Earl <ole4@aber.ac.uk>
     *
     * Sends feedback to the backend, with an optional email address if the user wants a response.
     */
    this.post('#/feedback', function() {
      $.ajax({
        type: "POST",
        url: API,
        data: {
          token: Cookies.get('token'),
          request: 'feedback',
          email: $('#email').val(),
          feedback: $('#feedback').val()
        },
        success: function() {
          printSuccess('Feedback submitted successfully!');
        },
        error: function(data) {
          printError(jQuery.parseJSON(JSON.stringify(data)))
        }
      });
    });

    /**
     * Logout Route
     * @author Oliver Earl <ole4@aber.ac.uk>
     *
     * Deletes cookies and refreshes the application, triggering the auth middleware, and forcing the user out of the
     * app. Rudimentary, but it works!
     */
    this.get('#/logout', function() {
      Cookies.remove('token');
      Cookies.remove('expiry');
      this.redirect('#/');
    })

  });
  $(function() {
    app.run('#/');
  });
})(jQuery);
