/**
 * Service Management - Select2 and Category/Service handling
 * Enhanced to load icons dynamically from database
 */

(function($){
    /* ========= Icon helper - Now supports database icons ========= */
    function pickPlatformIcon(text, dbIcon){
        // Prioritize database icon if available
        if (dbIcon && dbIcon !== '') {
            return dbIcon;
        }
        
        // Fallback to text-based detection
        if (!text) return '';
        var t = text.toLowerCase();
        
        // Popular Video Platforms
        if (t.includes('tiktok'))    return 'fa-brands fa-tiktok';
        if (t.includes('youtube'))   return 'fa-brands fa-youtube';
        if (t.includes('twitch'))    return 'fa-brands fa-twitch';
        if (t.includes('vimeo'))     return 'fa-brands fa-vimeo';
        
        // Social Networks
        if (t.includes('instagram')) return 'fa-brands fa-instagram';
        if (t.includes('facebook'))  return 'fa-brands fa-facebook-f';
        if (t.includes('twitter') || t.match(/\bx\b/) ) return 'fa-brands fa-x-twitter';
        if (t.includes('linkedin'))  return 'fa-brands fa-linkedin';
        if (t.includes('snapchat'))  return 'fa-brands fa-snapchat';
        if (t.includes('pinterest')) return 'fa-brands fa-pinterest';
        if (t.includes('reddit'))    return 'fa-brands fa-reddit';
        if (t.includes('tumblr'))    return 'fa-brands fa-tumblr';
        if (t.includes('discord'))   return 'fa-brands fa-discord';
        if (t.includes('telegram'))  return 'fa-brands fa-telegram';
        
        // Messaging Apps
        if (t.includes('whatsapp'))  return 'fa-brands fa-whatsapp';
        if (t.includes('messenger')) return 'fa-brands fa-facebook-messenger';
        if (t.includes('skype'))     return 'fa-brands fa-skype';
        if (t.includes('viber'))     return 'fa-brands fa-viber';
        if (t.includes('line'))      return 'fa-solid fa-comment';
        
        // Professional & Business
        if (t.includes('slack'))     return 'fa-brands fa-slack';
        if (t.includes('teams'))     return 'fa-brands fa-microsoft';
        if (t.includes('zoom'))      return 'fa-solid fa-video';
        
        // Chinese Platforms
        if (t.includes('wechat'))    return 'fa-brands fa-weixin';
        if (t.includes('weibo'))     return 'fa-brands fa-weibo';
        if (t.includes('qq'))        return 'fa-brands fa-qq';
        
        // Other Platforms
        if (t.includes('spotify'))   return 'fa-brands fa-spotify';
        if (t.includes('soundcloud')) return 'fa-brands fa-soundcloud';
        if (t.includes('github'))    return 'fa-brands fa-github';
        if (t.includes('behance'))   return 'fa-brands fa-behance';
        if (t.includes('dribbble'))  return 'fa-brands fa-dribbble';
        if (t.includes('medium'))    return 'fa-brands fa-medium';
        if (t.includes('quora'))     return 'fa-brands fa-quora';
        if (t.includes('flickr'))    return 'fa-brands fa-flickr';
        if (t.includes('foursquare')) return 'fa-brands fa-foursquare';
        
        // Dating Apps
        if (t.includes('tinder'))    return 'fa-solid fa-heart';
        if (t.includes('bumble'))    return 'fa-solid fa-heart';
        
        // Regional Platforms
        if (t.includes('vk') || t.includes('vkontakte')) return 'fa-brands fa-vk';
        if (t.includes('odnoklassniki')) return 'fa-brands fa-odnoklassniki';
        if (t.includes('xing'))      return 'fa-brands fa-xing';
        
        // Generic fallbacks for common terms
        if (t.includes('live') || t.includes('stream')) return 'fa-solid fa-broadcast-tower';
        if (t.includes('video'))     return 'fa-solid fa-video';
        if (t.includes('photo') || t.includes('image')) return 'fa-solid fa-image';
        if (t.includes('music') || t.includes('audio')) return 'fa-solid fa-music';
        if (t.includes('podcast'))   return 'fa-solid fa-podcast';
        if (t.includes('blog'))      return 'fa-solid fa-blog';
        if (t.includes('news'))      return 'fa-solid fa-newspaper';
        if (t.includes('shopping') || t.includes('store')) return 'fa-solid fa-shopping-cart';
        if (t.includes('game') || t.includes('gaming')) return 'fa-solid fa-gamepad';
        
        return '';
    }

    /* ========= Render icon HTML (supports both Font Awesome and image URLs) ========= */
    function renderIconHtml(iconToken){
        if (!iconToken) return '';
        
        // Check if it's an image URL
        if (iconToken.indexOf && (iconToken.indexOf('http') === 0 || iconToken.indexOf('img:') === 0)) {
            var url = iconToken.replace('img:', '');
            return '<img src="' + url + '" alt="icon" class="cat-icon-img" style="width:18px;height:18px;vertical-align:middle;margin-right:8px;border-radius:3px;">';
        }
        
        // It's a Font Awesome class
        return '<i class="' + iconToken + ' cat-icon" aria-hidden="true"></i> ';
    }

    /* ========= Service templates (with icons from database) ========= */
    function formatService(option) {
        if (!option.id) return option.text;
        var $opt = $(option.element);
        var name = $opt.data('name') || option.text;
        var rate = $opt.data('rate');
        var min  = $opt.data('min');
        var max  = $opt.data('max');
        var drip = ($opt.data('dripfeed') == 1);
        var dbIcon = $opt.data('icon'); // Get icon from data attribute
        
        var meta = [];
        if (rate) meta.push('PKR: ' + rate);
        if (min)  meta.push('Min: ' + min);
        if (max)  meta.push('Max: ' + max);
        if (drip) meta.push('Drip');

        var icon = pickPlatformIcon(name, dbIcon);
        var iconHtml = renderIconHtml(icon);
        
        return $(
            '<div class="svc-item">'+
            iconHtml +
            '<strong>'+ $('<span>').text(name).html() +'</strong><br>'+
            '<span class="svc-meta">'+ meta.join(' | ') +'</span>'+
            '</div>'
        );
    }

    function formatServiceSelection (option) {
        if (!option.id) return option.text;
        var $opt = $(option.element);
        var name = $opt.data('name') || option.text;
        var rate = $opt.data('rate');
        var dbIcon = $opt.data('icon'); // Get icon from data attribute
        
        var icon = pickPlatformIcon(name, dbIcon);
        var iconHtml = renderIconHtml(icon);
        var label = rate ? name + ' (' + rate + ')' : name;
        
        return $(
            '<span class="svc-sel">'+
            iconHtml +
            $('<span>').text(label).html()+
            '</span>'
        );
    }

    /* ========= Category templates (with icons) ========= */
    function categoryTemplate(option){
        if (!option.id) return option.text;
        var txt = option.text || '';
        var icon = pickPlatformIcon(txt);
        var iconHtml = renderIconHtml(icon);
        
        return $(
            '<span class="cat-opt">'+
            iconHtml +
            $('<span>').text(txt).html()+
            '</span>'
        );
    }

    /* ========= Init Category Select ========= */
    function initCategorySelect() {
        var $cat = $('#dropdowncategories');
        if (!$cat.length) return;
        if ($cat.hasClass('select2-hidden-accessible')) $cat.select2('destroy');

        $cat.select2({
            width: '100%',
            templateResult: categoryTemplate,
            templateSelection: categoryTemplate,
            escapeMarkup: function(markup){ return markup; },
            dropdownParent: $cat.closest('.category-select-wrapper').length ?
                          $cat.closest('.category-select-wrapper') : $cat.parent(),
            dropdownCssClass: 'custom-dropdown-height',
            dropdownAutoWidth: false
        }).on('change', function(){
            loadServicesForCategory($(this).val());
        });
    }

    /* ========= Init Service Select ========= */
    function initServiceSelect(ctx) {
        var $svc = (ctx) ? $(ctx).find('#dropdownservices') : $('#dropdownservices');
        if (!$svc.length) return;
        if ($svc.hasClass('select2-hidden-accessible')) $svc.select2('destroy');

        $svc.select2({
            width: '100%',
            dropdownParent: $svc.closest('.service-select-wrapper').length ?
                          $svc.closest('.service-select-wrapper') : $svc.parent(),
            placeholder: 'Choose a service',
            templateResult: formatService,
            templateSelection: formatServiceSelection,
            escapeMarkup: function(markup){ return markup; },
            allowClear: true,
            dropdownCssClass: 'custom-dropdown-height',
            dropdownAutoWidth: false,
            minimumResultsForSearch: 5
        }).on('change', function(){
            fetchServiceDetails($(this).val(), $(this).data('url'), $(this));
        });

        requestAnimationFrame(function(){
            $svc.next('.select2').css('width','100%');
        });
    }

    /* ========= Load Services by Category (Optimized for large datasets) ========= */
    function loadServicesForCategory(categoryId){
        if (!categoryId) {
            $('#result_onChange').html(
                '<div class="service-select-wrapper">'+
                '<select id="dropdownservices" name="service_id" class="form-control square" data-url="' + window.serviceUrl + '">'+
                '<option value="">Choose a service</option>'+
                '</select>'+
                '</div>'
            );
            initServiceSelect('#result_onChange');
            resetServiceResume();
            return;
        }
        
        var url = $('#dropdowncategories').data('url');
        
        // Show loading state
        $('#result_onChange').html('<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
        
        $.ajax({
            type: 'POST',
            url: url + categoryId,
            data: { token: (typeof window.csrfToken !== 'undefined') ? window.csrfToken : '' },
            cache: true, // Enable caching for better performance
            success: function(html){
                $('#result_onChange').html(html);
                initServiceSelect('#result_onChange');
                resetServiceResume();
                
                // Auto-select first service and fetch its details
                setTimeout(function() {
                    var $serviceDropdown = $('#result_onChange').find('#dropdownservices');
                    if ($serviceDropdown.length) {
                        var firstServiceOption = $serviceDropdown.find('option[value!=""]').first();
                        if (firstServiceOption.length && firstServiceOption.val()) {
                            $serviceDropdown.val(firstServiceOption.val()).trigger('change');
                            
                            var serviceId = firstServiceOption.val();
                            var baseUrl = $serviceDropdown.data('url');
                            if (serviceId && baseUrl) {
                                fetchServiceDetails(serviceId, baseUrl, $serviceDropdown);
                            }
                        }
                    }
                }, 100);
            },
            error: function(xhr){
                console.error('Failed to load services', xhr.status, xhr.responseText);
                $('#result_onChange').html('<div class="alert alert-danger">Could not load services for this category.</div>');
            }
        });
    }

    /* ========= Fetch service detail fragment ========= */
    function fetchServiceDetails(serviceId, baseUrl, $select){
        if (!serviceId) { resetServiceResume(); return; }
        
        $.ajax({
            type: 'POST',
            url: baseUrl + serviceId,
            data: { token: (typeof window.csrfToken !== 'undefined') ? window.csrfToken : '' },
            cache: true, // Enable caching
            success: function(fragment){
                $('#result_onChangeService').html(fragment);
                var price = $('#order_resume input[name=service_price]').val();
                var min   = $('#order_resume input[name=service_min]').val();
                var max   = $('#order_resume input[name=service_max]').val();
                if (price) $('#new_order input[name=service_price]').val(price);
                if (min)   $('#new_order input[name=service_min]').val(min);
                if (max)   $('#new_order input[name=service_max]').val(max);
                $('#service_id').val(serviceId);
                var $opt = $select.find('option:selected');
                applyServiceTypeUI($opt.data('type'), $opt.data('dripfeed'));
            },
            error: function(xhr){
                console.error('Failed to fetch service details', xhr.status, xhr.responseText);
                alert('Failed to fetch service details.');
            }
        });
    }

    /* ========= Reset summary panel ========= */
    function resetServiceResume(){
        $('#order_resume input[name=service_name]').val('');
        $('#order_resume input[name=service_min]').val('');
        $('#order_resume input[name=service_max]').val('');
        $('#order_resume input[name=service_price]').val('');
        $('#order_resume textarea[name=service_desc]').val('');
        $('#service_id').val('');
        $('#result_onChangeService').html('');
    }

    /* ========= UI toggles ========= */
    function applyServiceTypeUI(type, drip){
        if (drip == 1) {
            $("#new_order .drip-feed-option").removeClass("d-none");
        } else {
            $("#new_order .drip-feed-option").addClass("d-none");
        }
    }

    /* ========= Boot ========= */
    $(function(){
        initCategorySelect();
        initServiceSelect();
        
        // If there's already a pre-selected service, fetch its details
        var pre = $('#dropdownservices').val();
        if (pre) {
            fetchServiceDetails(pre, $('#dropdownservices').data('url'), $('#dropdownservices'));
        }
    });

    // Expose functions globally if needed
    window.ServiceManager = {
        initCategorySelect: initCategorySelect,
        initServiceSelect: initServiceSelect,
        loadServicesForCategory: loadServicesForCategory,
        fetchServiceDetails: fetchServiceDetails,
        resetServiceResume: resetServiceResume,
        pickPlatformIcon: pickPlatformIcon,
        renderIconHtml: renderIconHtml
    };

})(jQuery);