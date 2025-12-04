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

/*----------  Configure Summernote editor  ----------*/
function plugin_editor(selector, settings){
  // Handle both string selectors and jQuery objects
  if (typeof(selector) == 'undefined') {
    selector = '.plugin_editor';
  }
  
  var $elements = (selector instanceof jQuery) ? selector : $(selector);
  
  // Return early if no elements found
  if ($elements.length === 0) {
    return $elements;
  }
  
  var _height = 300;
  
  if (typeof(settings) != 'undefined' && settings.height) {
    _height = settings.height;
  }
  
  $elements.summernote({
    height: _height,
    minHeight: 100,
    maxHeight: 600,
    focus: false,
    toolbar: [
      ['style', ['style']],
      ['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
      ['fontname', ['fontname']],
      ['fontsize', ['fontsize']],
      ['color', ['forecolor', 'backcolor']],
      ['para', ['ul', 'ol', 'paragraph']],
      ['height', ['height']],
      ['table', ['table']],
      ['insert', ['link', 'picture', 'video', 'hr']],
      ['view', ['fullscreen', 'codeview', 'help']]
    ],
    fontNames: ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Georgia', 'Helvetica', 'Impact', 'Lucida Console', 'Lucida Sans Unicode', 'Palatino Linotype', 'Tahoma', 'Times New Roman', 'Trebuchet MS', 'Verdana'],
    fontNamesIgnoreCheck: ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Georgia', 'Helvetica', 'Impact', 'Lucida Console', 'Lucida Sans Unicode', 'Palatino Linotype', 'Tahoma', 'Times New Roman', 'Trebuchet MS', 'Verdana'],
    styleTags: ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote', 'pre'],
    lineHeights: ['0.2', '0.3', '0.4', '0.5', '0.6', '0.8', '1.0', '1.2', '1.4', '1.5', '2.0', '3.0'],
    callbacks: {
      onImageUpload: function(files) {
        // Handle image upload - convert to base64
        var editor = $(this);
        for (var i = 0; i < files.length; i++) {
          (function(file) {
            var reader = new FileReader();
            reader.onload = function(e) {
              editor.summernote('insertImage', e.target.result);
            };
            reader.readAsDataURL(file);
          })(files[i]);
        }
      }
    },
    // Don't clean or strip HTML - preserve full content
    codeviewFilter: false,
    codeviewIframeFilter: false
  });
  
  return $elements;
}

function elFinderBrowser (field_name, url, type, win) {
  // Legacy function - no longer using TinyMCE file browser
  // Summernote handles image upload via callbacks
  console.log('elFinderBrowser is deprecated - using Summernote image upload instead');
  return false;
}

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

/*----------  Upload media and return path to input selector  ----------*/
function getPathMediaByelFinderBrowser(_this, default_selector){
  // Legacy function - no longer using TinyMCE file browser
  // Summernote handles image upload via callbacks
  console.log('getPathMediaByelFinderBrowser is deprecated - using Summernote image upload instead');
  return false;
}
