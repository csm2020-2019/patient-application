window.onload = function() {
    document.getElementById('scripts-enabled').style.cssText = 'display:block';
};

/**
 * Login Function
 */
$('#login').submit(function (e) {
  e.preventDefault();

  let form = $(this);
  let url = 'api.php';

  $.ajax({
    type: form.attr('method'),
    url: url,
    data: form.serialize(),
    success: function(data)
    {
      let response = jQuery.parseJSON(JSON.stringify(data));
      //document.cookie = `token=${response['response']['jwt']}`;
      alert(JSON.stringify(data));
      //console.log(`Success! Cookie registered: ${document.cookie}`)
    },
    error: function(data)
    {
      // TODO: Better handling of error
      alert('Error: ' + jQuery.parseJSON(JSON.stringify(data)));
    }
  })
});



