/**
 * Certifications Plugin Scripts
 */
(function($) {
    'use strict';

    // Document ready
    $(document).ready(function() {
        // Smooth scroll for sticky menu links
        $('.sticky-menu a, .back-to-top a').on('click', function(e) {
            if (this.hash !== '') {
                e.preventDefault();

                const hash = this.hash;

                $('html, body').animate({
                    scrollTop: $(hash).offset().top - 100
                }, 800);
            }
        });

        // Highlight sticky menu items on scroll
        $(window).on('scroll', function() {
            const scrollPosition = $(window).scrollTop();

            $('.row[id]').each(function() {
                const currentSection = $(this);
                const sectionTop = currentSection.offset().top - 120;
                const sectionBottom = sectionTop + currentSection.outerHeight();

                if (scrollPosition >= sectionTop && scrollPosition < sectionBottom) {
                    $('.sticky-menu a').removeClass('active');
                    $('.sticky-menu a[href="#' + currentSection.attr('id') + '"]').addClass('active');
                }
            });
        });
    });

})(jQuery);