$(document).ready(function() {
    // Initialize tooltips if bootstrap supports them
    if (typeof $().tooltip === 'function') {
        $('[data-toggle="tooltip"]').tooltip();
    }
    
    // Highlight active filter button
    $('.filter-tabs .btn-primary').addClass('active-filter');
    
    // Add loading state to generate button
    $('form').on('submit', function() {
        var btn = $(this).find('button[type="submit"]');
        btn.html('<i class="fas fa-spinner fa-spin"></i> Generating...').prop('disabled', true);
    });
    
    // Auto-submit form when date changes for better UX (optional)
    $('input[type="date"], input[type="month"], select[name="year"]').on('change', function() {
        if (confirm('Generate report for this period?')) {
            $(this).closest('form').submit();
        }
    });
    
    // Add export confirmation
    $('.dt-button').on('click', function() {
        var format = $(this).text().trim();
        console.log('Exporting report as: ' + format);
    });
    
    // Format currency values on page load
    function formatCurrency(amount) {
        return 'ugx ' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }
    
    // Add print styles
    var printStyles = `
        @media print {
            .filter-section, .navbar, .dt-buttons, .filter-tabs {
                display: none !important;
            }
            .panel-heading {
                background-color: #f5f5f5 !important;
                -webkit-print-color-adjust: exact;
            }
            table {
                font-size: 10px !important;
            }
        }
    `;
    
    $('head').append('<style>' + printStyles + '</style>');
    
    // Show summary on hover
    $('.summary-card').hover(
        function() {
            $(this).css('transform', 'scale(1.05)');
            $(this).css('transition', 'transform 0.2s');
        },
        function() {
            $(this).css('transform', 'scale(1)');
        }
    );
    
    // Add confirmation before clearing filters
    $('.btn-default').on('click', function(e) {
        // Optional: Add any pre-filter change logic here
    });
    
    console.log('Reports page initialized successfully');
});


