"use strict";
(function ($) {
  let app = $.sammy('#main', function () {
    let auth = false;

    const checkAuth = function (callback) {
      if (!auth) {
        // Check cookie

      }
    };
    // Main route
    this.get('#/', function (context) {
      context.log('Hey this works!');
    });

  });
  $(function () {
    app.run('#/');
  });
})(jQuery);
