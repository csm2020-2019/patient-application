"use strict";
window.onload = function() {
  if (Cookies.get('token')) {
    window.location.href = "app.html";
  }
  let invite = new URLSearchParams(window.location.search);
  if (invite.has('id')) {
    document.getElementById('scripts-enabled').style.cssText = 'display:block';
    $('#invite').remove();
    let $pid = invite.get('id');
    document.getElementById('pid').value = $pid;
  }
};
(function ($) {

  $('#register').submit(function(e) {
    e.preventDefault();

    if (document.contains(document.getElementById('error'))) {
      document.getElementById('error').remove();
    }

    let form = $(this);
    let url = 'api.php';

    $.ajax({
      type: form.attr('method'),
      url: url,
      data: {
        username: $('#username').val(),
        password: $('#password').val(),
        email: $('#email').val(),
        firstName: $('#firstName').val(),
        lastName: $('#lastName').val(),
        pid: $('#pid').val()
      },
      success: function() {
        alert('Registration successful! Redirecting you to the login screen.');
        window.location.href = 'index.html';
    },
      error: function(data) {
        authError(data.responseJSON.message.toString());
      }
    });
    const authError = function(error) {
      let form = $('#register-form');
      let danger = `<div id="error" class="alert alert-danger" role="alert"><strong>Error: </strong>${error}</div>`;
      form.prepend(danger);
    };
  });
})(jQuery);
