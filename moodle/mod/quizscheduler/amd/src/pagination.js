define(['jquery'], function($) {
    return {
        init: function() {
            // Sincronizar mudanças no seletor
            $('#perpage-select').on('change', function() {
                $('#perpage-form').submit();
            });

            // Smooth scroll ao mudar de página
            $('.pagination a').on('click', function(e) {
                $('html, body').animate({
                    scrollTop: $('.scheduling-controls-wrapper').offset().top - 100
                }, 300);
            });

            // Destacar linha ao passar o mouse
            $('.timeslots-table tbody tr').hover(
                function() {
                    $(this).css('background-color', '#f0f7ff');
                },
                function() {
                    $(this).css('background-color', '');
                }
            );
        }
    };
});