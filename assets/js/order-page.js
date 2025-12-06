/**
 * Order/Add Page - Dynamic Service Management
 * This file handles all dynamic behavior for the order placement page
 * including platform filters, service selection, and price calculations
 */

(function($) {
    'use strict';

    var OrderPage = {
        // Configuration
        filterCache: {},
        currentPlatform: 'all',
        
        /**
         * Initialize the order page
         */
        init: function() {
            this.loadPlatformFilters();
            this.bindFilterEvents();
            this.initializeDatepicker();
            this.initializeSelectize();
            this.bindFormEvents();
            this.loadSavedOrderData();
        },

        /**
         * Load platform filters dynamically from database
         */
        loadPlatformFilters: function() {
            var self = this;
            
            // Check if PATH and token are defined globally
            if (typeof PATH === 'undefined' || typeof token === 'undefined') {
                console.warn('PATH or token not defined, cannot load platform filters');
                return;
            }
            
            // Get unique platforms from services
            $.ajax({
                url: PATH + 'order/get_platform_filters',
                type: 'POST',
                data: { token: token },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success' && response.filters) {
                        self.renderPlatformFilters(response.filters);
                        self.filterCache = response.filters;
                    }
                },
                error: function() {
                    console.warn('Failed to load platform filters, using defaults');
                }
            });
        },

        /**
         * Render platform filter buttons dynamically
         */
        renderPlatformFilters: function(filters) {
            var self = this; // Define self in the correct scope
            var $container = $('#category-icon-filters');
            if (!$container.length) return;

            // Clear existing filters except "All"
            $container.empty();

            // Always add "All" filter first
            $container.append(
                '<button type="button" class="catf-btn active" data-platform="all">' +
                '<i class="fas fa-bars"></i><span>All</span>' +
                '</button>'
            );

            // Sort filters by filter_order
            filters.sort(function(a, b) {
                return (a.filter_order || 999) - (b.filter_order || 999);
            });

            // Add dynamic filters
            filters.forEach(function(filter) {
                if (filter.filter_enabled && filter.filter_category !== 'all') {
                    var iconHtml = self.getIconHtml(filter.icon || filter.filter_category);
                    var displayName = filter.filter_name || self.capitalize(filter.filter_category);
                    
                    $container.append(
                        '<button type="button" class="catf-btn" data-platform="' + filter.filter_category + '">' +
                        iconHtml + '<span>' + displayName + '</span>' +
                        '</button>'
                    );
                }
            });
        },

        /**
         * Get icon HTML from icon string
         */
        getIconHtml: function(iconStr) {
            if (!iconStr) return '<i class="fas fa-plus"></i>';
            
            // Check if it's an image URL
            if (iconStr.indexOf('http') === 0 || iconStr.indexOf('img:') === 0) {
                var url = iconStr.replace('img:', '');
                return '<img src="' + url + '" class="platform-icon-img" style="width:18px;height:18px;vertical-align:middle;" alt="icon">';
            }
            
            // It's a Font Awesome class
            return '<i class="' + iconStr + '"></i>';
        },

        /**
         * Capitalize first letter
         */
        capitalize: function(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        },

        /**
         * Bind filter button events
         */
        bindFilterEvents: function() {
            var self = this;
            
            $(document).on('click', '.catf-btn', function() {
                var $btn = $(this);
                if ($btn.hasClass('active')) return;
                
                // Update active state
                $('.catf-btn').removeClass('active');
                $btn.addClass('active');
                
                // Get platform and filter categories
                var platform = $btn.data('platform');
                self.currentPlatform = platform;
                self.filterCategoriesByPlatform(platform);
            });
        },

        /**
         * Filter categories based on selected platform
         */
        filterCategoriesByPlatform: function(platform) {
            var self = this;
            var $cat = $('#dropdowncategories');
            var dataUrl = $cat.data('url');
            
            // Store original options if not already stored
            if (!self.originalCategoryOptions) {
                self.originalCategoryOptions = [];
                $cat.find('option').each(function() {
                    var $o = $(this);
                    var val = $o.attr('value');
                    if (val) {
                        self.originalCategoryOptions.push({
                            value: val,
                            text: $o.text(),
                            platform: self.detectPlatform($o.text())
                        });
                    }
                });
            }

            // Clear and rebuild dropdown
            var oldVal = $cat.val();
            $cat.empty();
            $cat.append('<option value="" disabled selected hidden>Choose a category</option>');

            var filtered = [];
            if (platform === 'all') {
                filtered = self.originalCategoryOptions;
            } else {
                filtered = self.originalCategoryOptions.filter(function(o) {
                    return o.platform === platform;
                });
            }

            filtered.forEach(function(o) {
                $cat.append('<option value="' + o.value + '">' + o.text + '</option>');
            });

            if (dataUrl) $cat.attr('data-url', dataUrl);

            // Reinitialize Select2
            if ($cat.hasClass('select2-hidden-accessible')) {
                $cat.select2('destroy');
            }
            
            if (typeof window.ServiceManager !== 'undefined') {
                window.ServiceManager.initCategorySelect();
            }

            // Auto-select first category if available
            if (filtered.length > 0) {
                $cat.val(filtered[0].value).trigger('change');
            }
        },

        /**
         * Detect platform from category/service name
         */
        detectPlatform: function(txt) {
            if (!txt) return 'other';
            var t = txt.toLowerCase();
            
            if (t.includes('tiktok')) return 'tiktok';
            if (t.includes('youtube') || t.includes('yt ')) return 'youtube';
            if (t.includes('insta')) return 'instagram';
            if (t.includes('telegram') || t.includes('tg ')) return 'telegram';
            if (t.includes('facebook') || t.includes('fb ')) return 'facebook';
            if (t.includes('twitter') || t.includes(' x ') || /\bx\b/.test(t)) return 'twitter';
            if (t.includes('whatsapp') || t.includes('wa ')) return 'whatsapp';
            if (t.includes('snap')) return 'snapchat';
            if (t.includes('linked')) return 'linkedin';
            
            return 'other';
        },

        /**
         * Initialize datepicker
         */
        initializeDatepicker: function() {
            if (typeof $.fn.datepicker === 'undefined') return;
            
            $('.datepicker').datepicker({
                format: "dd/mm/yyyy",
                autoclose: true,
                startDate: this.truncateDate(new Date())
            });
            $(".datepicker").datepicker().datepicker("setDate", new Date());
        },

        /**
         * Truncate date to midnight
         */
        truncateDate: function(date) {
            return new Date(date.getFullYear(), date.getMonth(), date.getDate());
        },

        /**
         * Initialize selectize for tags
         */
        initializeSelectize: function() {
            if (typeof $.fn.selectize === 'undefined') return;
            
            $('.input-tags').selectize({
                delimiter: ',',
                persist: false,
                create: function(input) {
                    return {
                        value: input,
                        text: input
                    };
                }
            });
        },

        /**
         * Bind form events
         */
        bindFormEvents: function() {
            var self = this;
            
            // Store order data on form submit
            $('.actionForm').on('submit', function() {
                var orderData = {
                    service_name: $('select[name="service_id"] option:selected').text(),
                    link: $('input[name="link"]').val(),
                    quantity: $('input[name="quantity"]').val(),
                    total_charge: $('input[name="total_charge"]').val()
                };
                localStorage.setItem('orderData', JSON.stringify(orderData));
            });
        },

        /**
         * Load saved order data from localStorage
         */
        loadSavedOrderData: function() {
            var savedOrderData = localStorage.getItem('orderData');
            if (!savedOrderData) return;

            savedOrderData = JSON.parse(savedOrderData);
            var shortServiceName = this.getShortServiceName(savedOrderData.service_name);
            var currencySymbol = $('input[name="currency_symbol"]').val() || '$';

            var summaryContent = `
                <div class="order-summary p-4" style="animation: fadeIn 1s ease-in-out;">
                    <div class="text-center mb-4">
                        <i class="fas fa-check-circle bounce-icon" style="font-size: 60px; color: #2ecc71; animation: bounce 1.5s infinite;"></i>
                    </div>
                    <h5 class="text-center mb-3" style="color: #000000; font-size: 24px;">Order Confirmation</h5>
                    <div class="order-details">
                        <p><i class="fas fa-bell" style="color: #2ecc71; margin-right: 8px;"></i><strong style="color: #000000;">Service Name:</strong> <span style="color: #000000;">${shortServiceName}</span></p>
                        <p><i class="fas fa-link" style="color: #2ecc71; margin-right: 8px;"></i><strong style="color: #000000;">Link:</strong> <span style="color: #000000;">${savedOrderData.link}</span></p>
                        <p><i class="fas fa-long-arrow-up" style="color: #2ecc71; margin-right: 8px;"></i><strong style="color: #000000;">Quantity:</strong> <span style="color: #000000;">${savedOrderData.quantity}</span></p>
                        <p><i class="fas fa-usd" style="color: #2ecc71; margin-right: 8px;"></i><strong style="color: #000000;">Total Charge:</strong> <span style="color: #000000;">${currencySymbol} ${savedOrderData.total_charge}</span></p>
                    </div>
                </div>
            `;

            $('#orderSummary').html(summaryContent);
            $('#orderConfirmationModal').modal('show');

            // Clear data when modal is closed
            $('#closeModalButton').on('click', function() {
                localStorage.removeItem('orderData');
            });
        },

        /**
         * Get short service name for display
         */
        getShortServiceName: function(serviceName) {
            var lowerServiceName = serviceName.toLowerCase();
            var platforms = ['tiktok', 'instagram', 'youtube', 'facebook', 'twitter'];
            var actions = ['views', 'likes', 'followers', 'subscribers', 'comments', 'shares'];

            var foundPlatform = platforms.find(function(platform) {
                return lowerServiceName.includes(platform);
            });
            
            var foundAction = actions.find(function(action) {
                return lowerServiceName.includes(action);
            });

            if (foundPlatform && foundAction) {
                return foundPlatform.charAt(0).toUpperCase() + foundPlatform.slice(1) + ' ' +
                       foundAction.charAt(0).toUpperCase() + foundAction.slice(1);
            }
            
            return serviceName;
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        OrderPage.init();
    });

    // Expose OrderPage globally
    window.OrderPage = OrderPage;

})(jQuery);
