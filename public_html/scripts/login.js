"use strict";
/**
 * Onload Function
 * @author Oliver Earl <ole4@aber.ac.uk>
 *
 * If the user's already logged in, then they can be safely relocated to the main page of the application without
 * risk of being sent back to the login screen. To ensure that the user can't login without the use of JavaScript, the
 * CSS is set to display none, which is reversed as the page is loaded by this function.
 *
 * Obviously it's not going to fool someone who knows how to 'Inspect Element', but it's enough for a layman.
 */
window.onload = function () {
    if (Cookies.get('token')) {
        window.location.href = 'app.html';
    }
    document.getElementById('scripts-enabled').style.cssText = 'display:block';
};
(function ($) {
    const TIMEOUT = 120; // 2 hours
    /**
     * Login Function
     * @author Oliver Earl <ole4@aber.ac.uk>
     *
     * This jQuery function is triggered when the login form is submitted. It uses AJAX to send form data, and using
     * a successful response will construct the necessary cookies containing a JWT token and an expiry date of 2 hours.
     *
     * Naturally this is all client-side and can't be depended upon - the API won't return anything to an unauthenticated
     * user, but this just adds some protection to the client-side application itself. Once the user is logged in, they
     * are redirected to the main page. If anything goes wrong, a simple error is displayed.
     *
     * TODO: In future, passing back specific validation issues would be good, rather than just *Unauthorised*.
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
                let exp = new Date(new Date().getTime() + TIMEOUT * 60 * 1000);

                //document.cookie = `token=${jwt};expires=${exp}`;
                Cookies.set('token', jwt, {expires: exp});
                Cookies.set('expiry', exp, {expires: exp});
                console.log(`Authentication successful!`);
                window.location.href = 'app.html';
            },
            error: function(data)
            {
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
            let loginForm = $('#login-form');
            let danger = `<div id="error" class="alert alert-danger" role="alert"><strong>Error: </strong>${error}</div>`;
            loginForm.prepend(danger);
        };
    });
})(jQuery);


