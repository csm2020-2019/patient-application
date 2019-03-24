"use strict";
(function ($) {
  let app = $.sammy('#main', function () {
    this.use('Template');

    let init = null;
    let token = null;

    this.before('#/', function () {
      // The server will enforce token expiry. This just helps keep things clean.

      // Has this program just started?
      if (init === null && token === null) {
        // Then we need to check if there's a cookie or not
        if (document.cookie) {
          // Set internal values to cookie values
          init =    new Date(document.cookie.indexOf('expires='));
          token =   document.cookie.indexOf('token=');
        } else {
          // No cookie
          alert('You have to be logged in to do that.');
          window.location.href = 'index.html';
        }
      } else {
        // Check for expiry either by time or cookie being removed by browser
        if (Date.now() > init || !document.cookie) {
          alert('Your session has timed out! Returning you to the login page.');
          window.location.href = 'index.html';
        }
      }
    });

    // Main route
    this.get('#/', function (context) {
      context.render('templates/home.template')
        .appendTo(context.$element());
    });

    this.get('#/programme', function (context) {
      // Retrieve programme information

    });

    this.get('#/logout', function (context) {
      document.cookie = "token=;expires= Thu, 01 Jan 1970 00:00:00 GMT";
      this.redirect('#/');
    })

  });
  $(function () {
    app.run('#/');
  });
})(jQuery);
