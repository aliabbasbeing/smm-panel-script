document.addEventListener("DOMContentLoaded", () => {
    const timerElement = document.getElementById("timer");
    const resendButton = document.getElementById("resend-btn");
    let countdown = 60;

    if (resendButton && timerElement) {
        // Disable the button initially
        resendButton.disabled = true;

        const interval = setInterval(() => {
            countdown--;
            timerElement.textContent = countdown;

            if (countdown <= 0) {
                clearInterval(interval);
                resendButton.disabled = false;
                resendButton.textContent = "Resend"; 
            }
        }, 1000);
    }
});
function handleCredentialResponse(response) {
    const idToken = response.credential;

    if (!idToken) {
        console.error('ID Token is missing from the response.');
        return;
    }

    console.log('ID Token:', idToken); // Log the token to verify it's generated

    // Send the ID token to the backend
    fetch("/login/google_login_callback", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ id_token: idToken })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Backend Response:', data);
        if (data.status === "success") {
            window.location.href = "/new_order"; // Redirect on success
        } else {
            alert(data.message); // Show the error message
        }
    })
    .catch(error => console.error('Error in fetch request:', error));
}

// Attach to global scope
window.handleCredentialResponse = handleCredentialResponse;


  var pageOverlay = pageOverlay || (function ($) {
    return {
      show: function (message, options) {
        if(!$('#page-overlay').hasClass('active')){
          $('#page-overlay').addClass('active');
          $('#page-overlay .page-loading-image').removeClass('d-none');
        }
      },

      hide: function () {
        if($('#page-overlay').hasClass('active')){
          $('#page-overlay').removeClass('active');
          $('#page-overlay .page-loading-image').addClass('d-none');
        }
      }
    };

  })(jQuery);

  
  var alertMessage = alertMessage || (function ($) {
    var $html = $('<div class="alert alert-icon content d-none" role="alert">' +
                  '<i class="fe icon-symbol" aria-hidden="true"></i>' +
                  '<span class="message">Message is in here</span>' +
                '</div>');
    return {
      show: function (_message, _type) {
        switch(_type) {
          case 'error':
            var _type = 'alert-warning',
                _icon = 'fe-alert-triangle';
            break; 
          case 'success':
            var _type = 'alert-success',
                _icon = 'fe-check';
            break;
          default:
            var _type = 'alert-warning',
                _icon = 'fe-bell';
        }
        $('.alert-message-reponse').html($html);
        $('.alert-message-reponse .content').addClass(_type);
        $('.alert-message-reponse .icon-symbol').addClass(_icon);
        $('.alert-message-reponse .content').removeClass('d-none');
        $('.alert-message-reponse .content .message').html(_message);
      },

      hide: function () {
        $('.alert-message-reponse').html('');
      }
    };

  })(jQuery);

  // Confirm notice
  function confirm_notice(_ms) {
      switch(_ms) {
        case 'deleteItem':
            return confirm(deleteItem);
            break;
        case 'deleteItems':
            return confirm(deleteItems);
            break;
        default:
            return confirm(_ms);
    }
    return confirm(_ms);
  }

  function is_json(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
  }

  // Reload page
  function reloadPage(_url){
    if(_url != ''){
      setTimeout(function(){window.location = _url;}, 2500);
    }else{
      setTimeout(function(){location.reload()}, 2500);
    }
  }

 function notify(_ms, _type){
  switch(_type) {
      
    case 'error':
      _text = _ms;
      _icon = 'warning';
      break; 

    case 'success':
      _text = _ms;
      _icon = 'success';
      break;

    default:
      // code block
  }

  $.toast({
      text: _text, 
      icon: _icon,
      showHideTransition: 'fade',
      allowToastClose: true,
      hideAfter: 3000,
      stack: 5,
      position: 'bottom-center', 
      textAlign: 'left',
      loader: true,
      loaderBg: '#0ef1f1',
      beforeShow: function () {},
      afterShown: function () {},
      beforeHide: function () {}, 
      afterHidden: function () {} 
  });
}

    document.addEventListener("DOMContentLoaded", function() {
        const copyButtons = document.querySelectorAll('.copyOrderIDBtn');

        copyButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                const orderId = this.getAttribute('data-orderid');
                const notification = document.createElement('div');
                notification.classList.add('copyNotification');
                notification.textContent = + orderId + ' copied';

                const rect = button.getBoundingClientRect();
                notification.style.top = (rect.top + window.scrollY) + 'px';
                notification.style.left = (rect.right + window.scrollX) + 'px';

                document.body.appendChild(notification);

                navigator.clipboard.writeText(orderId)
                    .then(() => {
                        notification.style.display = 'block';
                        setTimeout(() => {
                            notification.style.display = 'none';
                            document.body.removeChild(notification);
                        }, 3000); // Hide notification after 3 seconds
                    })
                    .catch(err => console.error('Failed to copy: ', err));
            });
        });
    });

/*----------  Configure tinymce editor  ----------*/


function sendXMLPostRequest($url, $params){
  var Url      = $url;
  var params   = $params;
  var xhr      = new XMLHttpRequest();
  xhr.open('POST', Url, true);
  xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhr.onreadystatechange = processRequest;
  function processRequest(e) {
    console.log(xhr);
    if (xhr.readyState == 4 && xhr.status == 200) {
      var response = JSON.parse(xhr.responseText);
      console.log(response.status);
    }
  }
  xhr.send(params);
}








