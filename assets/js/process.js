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

/*----------  Configure Quill Editor  ----------*/
// Store Quill instances globally for access
window.quillEditors = window.quillEditors || {};

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
  
  // Check if Quill is available
  if (typeof Quill === 'undefined') {
    console.warn('Quill is not loaded. Waiting for it to load...');
    // Retry after a short delay
    setTimeout(function() {
      plugin_editor(selector, settings);
    }, 500);
    return $elements;
  }
  
  var _height = 300;
  
  if (typeof(settings) != 'undefined' && settings.height) {
    _height = settings.height;
  }
  
  $elements.each(function(index) {
    var $textarea = $(this);
    
    // Skip if already initialized
    if ($textarea.data('quill-initialized')) {
      return;
    }
    
    // Create unique ID for the editor
    var editorId = 'quill-editor-' + Date.now() + '-' + index;
    
    // Create wrapper and editor container
    var $wrapper = $('<div class="quill-wrapper mb-3"></div>');
    var $editorDiv = $('<div id="' + editorId + '"></div>');
    
    // Hide the original textarea and insert the editor
    $textarea.hide().after($wrapper);
    $wrapper.append($editorDiv);
    
    // Set the initial content from textarea
    var initialContent = $textarea.val();
    
    // Initialize Quill
    var quill = new Quill('#' + editorId, {
      theme: 'snow',
      placeholder: $textarea.attr('placeholder') || 'Write something...',
      modules: {
        toolbar: [
          [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
          [{ 'font': [] }],
          [{ 'size': ['small', false, 'large', 'huge'] }],
          ['bold', 'italic', 'underline', 'strike'],
          [{ 'color': [] }, { 'background': [] }],
          [{ 'script': 'sub'}, { 'script': 'super' }],
          [{ 'list': 'ordered'}, { 'list': 'bullet' }],
          [{ 'indent': '-1'}, { 'indent': '+1' }],
          [{ 'direction': 'rtl' }],
          [{ 'align': [] }],
          ['blockquote', 'code-block'],
          ['link', 'image', 'video'],
          ['clean']
        ]
      }
    });
    
    // Set editor height
    $editorDiv.find('.ql-editor').css('min-height', _height + 'px');
    
    // Set initial content
    if (initialContent) {
      quill.root.innerHTML = initialContent;
    }
    
    // Sync content back to textarea on text change
    quill.on('text-change', function() {
      $textarea.val(quill.root.innerHTML);
    });
    
    // Store the quill instance
    window.quillEditors[editorId] = quill;
    $textarea.data('quill-instance', quill);
    $textarea.data('quill-initialized', true);
    
    // Handle form submission - ensure textarea is synced
    $textarea.closest('form').on('submit', function() {
      $textarea.val(quill.root.innerHTML);
    });
  });
  
  return $elements;
}

function elFinderBrowser (field_name, url, type, win) {
  // Legacy function - no longer using TinyMCE file browser
  // Quill handles image upload via toolbar
  console.log('elFinderBrowser is deprecated - using Quill image upload instead');
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
