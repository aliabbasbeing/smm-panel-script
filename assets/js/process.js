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

/*----------  Configure TinyMCE Editor  ----------*/
// Store TinyMCE instances globally for access
window.tinymceEditors = window.tinymceEditors || {};

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
  
  // Check if TinyMCE is available
  if (typeof tinymce === 'undefined') {
    console.warn('TinyMCE is not loaded. Waiting for it to load...');
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
    if ($textarea.data('tinymce-initialized')) {
      return;
    }
    
    // Make sure textarea has an ID
    var textareaId = $textarea.attr('id');
    if (!textareaId) {
      textareaId = 'tinymce-editor-' + Date.now() + '-' + index;
      $textarea.attr('id', textareaId);
    }
    
    // Mark as initialized to prevent double init
    $textarea.data('tinymce-initialized', true);
    
    // Initialize TinyMCE with code view first in toolbar
    tinymce.init({
      selector: '#' + textareaId,
      height: _height,
      base_url: 'https://cdn.jsdelivr.net/npm/tinymce@6.8.2',
      suffix: '.min',
      menubar: true,
      promotion: false,
      branding: false,
      plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount', 'codesample',
        'emoticons', 'quickbars', 'directionality'
      ],
      // Code view (source code) is FIRST in toolbar as requested
      toolbar: 'code | undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | link image media table codesample | charmap emoticons | ltr rtl | fullscreen preview | help',
      toolbar_mode: 'sliding',
      content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; }',
      quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote',
      quickbars_insert_toolbar: 'quickimage quicktable',
      contextmenu: 'link image table',
      image_advtab: true,
      image_caption: true,
      link_default_target: '_blank',
      directionality: 'ltr',
      codesample_languages: [
        { text: 'HTML/XML', value: 'markup' },
        { text: 'JavaScript', value: 'javascript' },
        { text: 'CSS', value: 'css' },
        { text: 'PHP', value: 'php' },
        { text: 'Python', value: 'python' },
        { text: 'Java', value: 'java' },
        { text: 'C', value: 'c' },
        { text: 'C++', value: 'cpp' },
        { text: 'Ruby', value: 'ruby' },
        { text: 'SQL', value: 'sql' },
        { text: 'Bash', value: 'bash' }
      ],
      setup: function(editor) {
        // Store the editor instance
        window.tinymceEditors[textareaId] = editor;
        $textarea.data('tinymce-instance', editor);
        
        // Sync content on change
        editor.on('change', function() {
          editor.save();
        });
        
        // Sync on blur
        editor.on('blur', function() {
          editor.save();
        });
      },
      init_instance_callback: function(editor) {
        // Ensure content is synced on form submit
        $textarea.closest('form').on('submit', function() {
          editor.save();
        });
      }
    });
  });
  
  return $elements;
}

function elFinderBrowser (field_name, url, type, win) {
  // Legacy function - TinyMCE handles file browser internally
  console.log('elFinderBrowser is deprecated - using TinyMCE internal file browser');
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
