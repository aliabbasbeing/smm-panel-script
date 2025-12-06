/**
 * Bootstrap 5 Toast Notification System
 */
(function() {
    'use strict';
    
    // Create toast container if it doesn't exist
    function createToastContainer() {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }
        return container;
    }
    
    // Create a toast element
    function createToast(options) {
        const defaults = {
            title: 'Notification',
            message: '',
            type: 'primary', // primary, success, danger, warning, info
            delay: 5000,
            autohide: true
        };
        
        const settings = Object.assign({}, defaults, options);
        
        const toastId = 'toast-' + Date.now();
        
        const iconMap = {
            'primary': 'fas fa-info-circle',
            'success': 'fas fa-check-circle',
            'danger': 'fas fa-exclamation-circle',
            'warning': 'fas fa-exclamation-triangle',
            'info': 'fas fa-info-circle'
        };
        
        const toastHtml = `
            <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="${settings.delay}" data-bs-autohide="${settings.autohide}">
                <div class="toast-header bg-${settings.type} text-white">
                    <i class="${iconMap[settings.type] || iconMap.primary} me-2"></i>
                    <strong class="me-auto">${settings.title}</strong>
                    <small>Just now</small>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    ${settings.message}
                </div>
            </div>
        `;
        
        return toastHtml;
    }
    
    // Show toast notification
    window.showToast = function(options) {
        const container = createToastContainer();
        const toastHtml = createToast(options);
        container.insertAdjacentHTML('beforeend', toastHtml);
        
        const toastElement = container.lastElementChild;
        const toast = new bootstrap.Toast(toastElement);
        
        // Remove toast element after it's hidden
        toastElement.addEventListener('hidden.bs.toast', function() {
            toastElement.remove();
        });
        
        toast.show();
        return toast;
    };
    
    // Convenience methods
    window.toastSuccess = function(message, title) {
        return showToast({
            type: 'success',
            title: title || 'Success',
            message: message
        });
    };
    
    window.toastError = function(message, title) {
        return showToast({
            type: 'danger',
            title: title || 'Error',
            message: message
        });
    };
    
    window.toastWarning = function(message, title) {
        return showToast({
            type: 'warning',
            title: title || 'Warning',
            message: message
        });
    };
    
    window.toastInfo = function(message, title) {
        return showToast({
            type: 'info',
            title: title || 'Info',
            message: message
        });
    };
})();
