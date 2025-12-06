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
            activeClass: 'active',
            showClass: 'show'
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
            
            // Remove active and show classes from all tabs
            $(self.config.tabSelector).removeClass(self.config.activeClass);
            $(self.config.tabContentSelector).removeClass(self.config.activeClass).removeClass(self.config.showClass);
            
            // Add active class to clicked tab
            $tabLink.addClass(self.config.activeClass);
            
            // Show corresponding content
            $(targetHash).addClass(self.config.activeClass).addClass(self.config.showClass);
            
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
            
            // Reset all tab styles by removing active state
            $(self.config.tabSelector).removeClass('active');
            
            // Add active class to the current tab
            $activeTab.addClass('active');
            
            // Remove any existing badges
            $(self.config.tabSelector).find('.editor-loaded-badge').remove();
            
            // Add loaded indicator to tabs that have initialized editors
            for (var tabId in self.initializedEditors) {
                if (self.initializedEditors[tabId]) {
                    var $tab = $(self.config.tabSelector + '[href="' + tabId + '"]');
                    if (!$tab.find('.editor-loaded-badge').length) {
                        $tab.append(' <span class="editor-loaded-badge" style="font-size:8px; color:#28a745;">●</span>');
                    }
                }
            }
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
                // Add loading indicator
                var $container = $editor.closest('.form-group');
                var $loadingIndicator = $('<div class="editor-loading-indicator">' +
                                          '<i class="fas fa-spinner fa-spin"></i> Loading editor...</div>');
                
                $container.prepend($loadingIndicator);
                
                // Small delay to ensure tab is visible
                setTimeout(function() {
                    plugin_editor($editor, {height: 200});
                    self.initializedEditors[tabId] = true;
                    
                    // Remove loading indicator
                    $container.find('.editor-loading-indicator').fadeOut(300, function() {
                        $(this).remove();
                    });
                    
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
    
    // Add form submit handler for code parts - needs to be at document level to run first
    if ($('.code-parts-container').length > 0) {
        // Use capture phase to ensure this runs before other handlers
        document.addEventListener('submit', function(e) {
            var $target = $(e.target);
            
            // Only handle actionForm submissions on code parts page
            if ($target.hasClass('actionForm') && $('.code-parts-container').length > 0) {
                // Find the active tab's editor
                var activeTabId = $('.code-parts-tab .nav-link.active').attr('href');
                var $editor = $(activeTabId + ' .plugin_editor');
                
                if ($editor.length > 0) {
                    var editorId = $editor.attr('id');
                    
                    // Save TinyMCE content to textarea before form serialization
                    if (typeof tinymce !== 'undefined' && editorId) {
                        var editor = tinymce.get(editorId);
                        if (editor) {
                            // Force save the content
                            editor.save();
                            console.log('Code Parts: TinyMCE content force saved for:', editorId);
                            console.log('Code Parts: Content length:', $editor.val().length);
                        } else {
                            console.warn('Code Parts: TinyMCE editor not found for:', editorId);
                            console.warn('Code Parts: Available editors:', Object.keys(window.tinymceEditors || {}));
                        }
                    } else {
                        console.warn('Code Parts: TinyMCE not available or no editor ID');
                    }
                }
            }
        }, true); // Use capture phase
    }
    
    // Expose to global scope for debugging
    window.CodePartsTabs = CodePartsTabs;
    
})(jQuery);
