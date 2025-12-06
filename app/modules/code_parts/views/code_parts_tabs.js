/**
 * Code Parts Tab Navigation and Performance Optimization
 * Handles tab switching, hash navigation, and lazy loading of TinyMCE editors
 */
(function($) {
    'use strict';
    
    var CodePartsTabs = {
        // Configuration
        config: {
            tabSelector: '.code-parts-tab a',
            tabContentSelector: '.tab-content .tab-pane',
            editorClass: 'plugin_editor',
            navTabsSelector: '.nav-tabs',
            activeClass: 'active show'
        },
        
        // Store initialized editors
        initializedEditors: {},
        
        /**
         * Initialize the module
         */
        init: function() {
            this.setupTabNavigation();
            this.handleHashNavigation();
            this.setupLazyEditorLoading();
            this.optimizeTabActivation();
        },
        
        /**
         * Setup tab navigation with proper Bootstrap 5 handling
         */
        setupTabNavigation: function() {
            var self = this;
            
            // Handle tab clicks
            $(document).on('click', self.config.tabSelector, function(e) {
                e.preventDefault();
                var $this = $(this);
                var targetHash = $this.attr('href');
                
                // Update URL hash without scrolling
                if (history.pushState) {
                    history.pushState(null, null, targetHash);
                } else {
                    window.location.hash = targetHash;
                }
                
                // Activate the tab
                self.activateTab($this, targetHash);
            });
            
            // Handle browser back/forward buttons
            $(window).on('hashchange', function() {
                self.handleHashNavigation();
            });
        },
        
        /**
         * Activate a specific tab
         */
        activateTab: function($tabLink, targetHash) {
            var self = this;
            
            if (!targetHash) {
                targetHash = $tabLink.attr('href');
            }
            
            // Remove active class from all tabs
            $(self.config.tabSelector).removeClass(self.config.activeClass);
            $(self.config.tabContentSelector).removeClass(self.config.activeClass);
            
            // Add active class to clicked tab
            $tabLink.addClass(self.config.activeClass);
            
            // Show corresponding content
            $(targetHash).addClass(self.config.activeClass);
            
            // Update tab link styles
            self.updateTabStyles($tabLink);
            
            // Initialize editor for this tab if not already done
            self.initializeTabEditor(targetHash);
        },
        
        /**
         * Update tab visual styles
         */
        updateTabStyles: function($activeTab) {
            var self = this;
            
            // Reset all tab styles
            $(self.config.tabSelector).css({
                'background': '#fff',
                'color': '#333',
                'border-bottom': '1px solid #ddd'
            });
            
            // Style active tab
            $activeTab.css({
                'background': '#f8f8f8',
                'color': '#1B78FC',
                'border-bottom': 'none'
            });
        },
        
        /**
         * Handle URL hash navigation
         */
        handleHashNavigation: function() {
            var self = this;
            var hash = window.location.hash;
            
            if (hash && hash.length > 1) {
                // Find the tab link with this hash
                var $tabLink = $(self.config.tabSelector + '[href="' + hash + '"]');
                
                if ($tabLink.length > 0) {
                    // Activate the tab
                    self.activateTab($tabLink, hash);
                } else {
                    // Default to first tab if hash not found
                    self.activateDefaultTab();
                }
            } else {
                // No hash, activate default tab
                self.activateDefaultTab();
            }
        },
        
        /**
         * Activate the default (first) tab
         */
        activateDefaultTab: function() {
            var self = this;
            var $firstTab = $(self.config.tabSelector).first();
            var firstHash = $firstTab.attr('href');
            
            self.activateTab($firstTab, firstHash);
        },
        
        /**
         * Setup lazy loading for TinyMCE editors
         * Only initialize editors when their tab becomes active
         */
        setupLazyEditorLoading: function() {
            var self = this;
            
            // Don't initialize any editors on page load
            // They will be initialized when tabs are activated
            console.log('Code Parts: Lazy editor loading enabled');
        },
        
        /**
         * Initialize TinyMCE editor for a specific tab
         */
        initializeTabEditor: function(tabId) {
            var self = this;
            
            // Check if editor is already initialized for this tab
            if (self.initializedEditors[tabId]) {
                console.log('Code Parts: Editor already initialized for ' + tabId);
                return;
            }
            
            // Find the editor textarea in this tab
            var $editor = $(tabId + ' .' + self.config.editorClass);
            
            if ($editor.length > 0 && typeof plugin_editor === 'function') {
                // Small delay to ensure tab is visible
                setTimeout(function() {
                    plugin_editor($editor, {height: 200});
                    self.initializedEditors[tabId] = true;
                    console.log('Code Parts: Editor initialized for ' + tabId);
                }, 100);
            }
        },
        
        /**
         * Optimize tab activation for better performance
         */
        optimizeTabActivation: function() {
            var self = this;
            
            // Add loading state class
            $(self.config.tabContentSelector).each(function() {
                var $pane = $(this);
                if (!$pane.hasClass(self.config.activeClass)) {
                    // Mark non-active tabs as not loaded
                    $pane.data('loaded', false);
                }
            });
        },
        
        /**
         * Destroy all TinyMCE editors (cleanup)
         */
        destroyEditors: function() {
            var self = this;
            
            if (typeof tinymce !== 'undefined') {
                tinymce.remove();
                self.initializedEditors = {};
            }
        }
    };
    
    // Initialize when document is ready
    $(document).ready(function() {
        // Check if we're on the code parts page
        if ($('.code-parts-container').length > 0) {
            CodePartsTabs.init();
        }
    });
    
    // Expose to global scope for debugging
    window.CodePartsTabs = CodePartsTabs;
    
})(jQuery);
