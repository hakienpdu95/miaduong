import '../components/init.js';

$(document).ready(function() {
    $('.menu-btn').on('click', function() {
        $('.menu .dropdown-menu').fadeToggle('fast');
        $(this).toggleClass('active');
        $('body').toggleClass('backdrop');
    });

    $('.menu-heading .ic-chevron-down').click(function(e) {
        e.preventDefault();
        $(this).toggleClass('ic-chevron-up');
        const submenu = $(this).closest('.menu-heading').siblings('.submenu').first();
        submenu.slideToggle('fast');
    });

    if ($('.site-header').length) {
        const headerTop = $('.site-header').offset().top;
        $(window).scroll(function() {
            const header = $('.site-header');
            const scrollTop = $(window).scrollTop();
            if (headerTop <= scrollTop) {
                header.addClass('fixed');
            } else {
                header.removeClass('fixed');
            }
        });
    }
});