// External Scripts - Main JavaScript File

// jQuery conflict resolution
var $jq = jQuery.noConflict();

$(function() {
    // Date picker initialization
    $('.datepicker').datepicker({
        format: "dd/mm/yyyy",
        autoclose: true,
        startDate: truncateDate(new Date())
    });
    $(".datepicker").datepicker().datepicker("setDate", new Date());

    function truncateDate(date) {
        return new Date(date.getFullYear(), date.getMonth(), date.getDate());
    }

    // Input tags initialization
    $('.input-tags').selectize({
        delimiter: ',',
        persist: false,
        create: function (input) {
            return {
                value: input,
                text: input
            }
        }
    });
});

// Order confirmation modal functionality
$(document).ready(function() {
    // Store data on form submission
    $('.actionForm').on('submit', function() {
        var orderData = {
            service_name: $('select[name="service_id"] option:selected').text(),
            link: $('input[name="link"]').val(),
            quantity: $('input[name="quantity"]').val(),
            total_charge: $('input[name="total_charge"]').val()
        };
        
        localStorage.setItem('orderData', JSON.stringify(orderData));
    });

    // Function to dynamically shorten the service name
    function getShortServiceName(serviceName) {
        const lowerServiceName = serviceName.toLowerCase();
        const platforms = ['tiktok', 'instagram', 'youtube', 'facebook', 'twitter'];
        const actions = ['views', 'likes', 'followers', 'subscribers', 'comments', 'shares'];
        
        let shortServiceName = '';
        
        const foundPlatform = platforms.find(platform => lowerServiceName.includes(platform));
        const foundAction = actions.find(action => lowerServiceName.includes(action));

        if (foundPlatform && foundAction) {
            shortServiceName = `${foundPlatform.charAt(0).toUpperCase() + foundPlatform.slice(1)} ${foundAction.charAt(0).toUpperCase() + foundAction.slice(1)}`;
        } else {
            shortServiceName = serviceName;
        }

        return shortServiceName;
    }
    
    // Retrieve and show data in the modal on page load
    var savedOrderData = localStorage.getItem('orderData');
    if (savedOrderData) {
        savedOrderData = JSON.parse(savedOrderData);
        var shortServiceName = getShortServiceName(savedOrderData.service_name);
        
        var summaryContent = `
            <div class="order-summary p-4" style="background-color: ; color: #ecf0f1; animation: fadeIn 1s ease-in-out;">
                <div class="text-center mb-4">
                    <i class="fa fa-check-circle bounce-icon" style="font-size: 60px; color: #2ecc71; animation: bounce 1.5s infinite;"></i>
                </div>
                <h5 class="text-center mb-3" style="color: #ecf0f1; font-size: 24px;">Order Confirmation</h5>
                <div class="order-details">
                    <p><i class="fa fa-bell" style="color: #2ecc71; margin-right: 8px;"></i><strong>Service Name:</strong> <span style="color: #bdc3c7;">${shortServiceName}</span></p>
                    <p><i class="fa fa-link" style="color: #2ecc71; margin-right: 8px;"></i><strong>Link:</strong> <span style="color: #bdc3c7;">${savedOrderData.link}</span></p>
                    <p><i class="fa fa-long-arrow-up" aria-hidden="true" style="color: #2ecc71; margin-right: 8px;"></i><strong>Quantity:</strong> <span style="color: #bdc3c7;">${savedOrderData.quantity}</span></p>
                    <p><i class="fa fa-usd" style="color: #2ecc71; margin-right: 8px;"></i><strong>Total Charge:</strong> <span style="color: #bdc3c7;">${savedOrderData.total_charge} PKR</span></p>
                </div>
            </div>
        `;

        $('#orderSummary').html(summaryContent);
        $('#orderConfirmationModal').modal('show'); 

        $('#closeModalButton').on('click', function() {
            localStorage.removeItem('orderData');
        });
    }
});

// Vertical image modal functionality
$(document).ready(function() {
    // Check if vertical image modal should be shown (this would need to be set via PHP or data attributes)
    if (typeof window.showVerticalImageModal !== 'undefined' && window.showVerticalImageModal) {
        $('#verticalImageModal').modal('show');
    }
});