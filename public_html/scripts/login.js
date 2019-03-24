"use strict";
window.onload = function () {
  if (document.cookie) {
    if (Date.now() > new Date(document.cookie.indexOf('expires='))) {
      window.location.href = 'app.html';
    }
  }
  document.getElementById('scripts-enabled').style.cssText = 'display:block';
};
(function ($) {
  const TIMEOUT = 120; // 2 hours
  /**
   * Login Function
   */
  $('#login').submit(function (e) {
    e.preventDefault();

    // Writing this in vanilla for now because I've had trouble with the jQuery
    if (document.contains(document.getElementById('error'))) {
      document.getElementById('error').remove();
    }

    let form = $(this);
    let url = 'api.php';

    $.ajax({
      type: form.attr('method'),
      url: url,
      data: form.serialize(),
      success: function(data)
      {
        let response = jQuery.parseJSON(JSON.stringify(data));
        let jwt = response['response']['jwt'];

        // I really don't want to have to hard-code this in, but I can't figure out any other way
        let exp = new Date(new Date().getTime() + TIMEOUT * 60 * 1000).toUTCString();

        document.cookie = `token=${jwt};expires=${exp}`;
        console.log(`Authentication successful!`);
        window.location.href = 'app.html';
      },
      error: function(data)
      {
        authError(data.responseJSON.message.toString());
      }
    });
    const authError = function(error) {
      let loginForm = $('#login-form');
      let danger = `<div id="error" class="alert alert-danger" role="alert"><strong>Error: </strong>${error}</div>`;
      loginForm.prepend(danger);
    };
  });
})(jQuery);


