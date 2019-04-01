"use strict";
/**
 * Onload Function
 * @author Oliver Earl <ole4@aber.ac.uk>
 *
 * This function first checks for an patient ID (pid) parameter in the URL - this would usually be provided by the Java
 * application when it sends out invitations for a user to sign up on the Diabetes Assistant. It then injects this into
 * a hidden value in the registration form, that is used by the backend.
 *
 * If this value isn't present in the URL, it hides the rest of the content to stop someone from signing up without an
 * 'invitation'. Obviously, someone can falsify a value in the URL if they wanted to - but this is protected by the
 * backend anyway, and they should just get an error if they tried to create a user for a patient that already has a
 * user assigned to it.
 *
 * The function also takes care of redirecting the user back to the application if they happen to be logged in.
 */
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

  /**
   * Register Function
   * @author Oliver Earl <ole4@aber.ac.uk>
   *
   * Sends the user's provided information, along with the Patient ID (PID) value injected into the form to the backend.
   *
   * If it fails validation, or the user attempts to sign up with an already registered patient ID, it will display
   * an error dialogue. Otherwise, it'll redirect the user back to the login screen.
   */
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

    /**
     * authError Helper Function
     * @author Oliver Earl <ole4@aber.ac.uk>
     * @param error - Error from AJAX response
     *
     * This function simply prints out a Bootstrap danger alert to the DOM, containing some error information.
     */
    const authError = function(error) {
      let form = $('#register-form');
      let danger = `<div id="error" class="alert alert-danger" role="alert"><strong>Error: </strong>${error}</div>`;
      form.prepend(danger);
    };
  });
})(jQuery);
