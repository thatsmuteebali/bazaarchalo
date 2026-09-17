(function ($) {
    "use strict";

    // Spinner
    var spinner = function () {
        setTimeout(function () {
            if ($('#spinner').length > 0) {
                $('#spinner').removeClass('show');
            }
        }, 1);
    };
    spinner(0);

    // Keep the original template pages intact while using the Bazaar Chalo home flow.
    var bazaarHome = document.getElementById('bazaar-home-content');
    if (bazaarHome) {
        var removeLegacy = false;
        var homeFooter = document.querySelector('.footer')?.closest('.container-fluid');
        Array.from(document.body.children).forEach(function (child) {
            if (child === bazaarHome) {
                removeLegacy = true;
                return;
            }

            if (removeLegacy && child !== homeFooter && child.tagName !== 'SCRIPT') {
                child.remove();
            }

            if (child === homeFooter) {
                removeLegacy = false;
            }
        });
    }

    // Wishlist buttons on product cards
    var addWishlistButton = function (card) {
        if (!card || card.querySelector('.wishlist-button')) {
            return;
        }

        var button = $('<button>', {
            type: 'button',
            class: 'wishlist-button',
            title: 'Add to wishlist',
            'aria-label': 'Add to wishlist'
        }).append('<i class="far fa-heart" aria-hidden="true"></i>');

        button.on('click', function () {
            var active = button.toggleClass('active').hasClass('active');
            button.attr('title', active ? 'Remove from wishlist' : 'Add to wishlist');
            button.attr('aria-label', active ? 'Remove from wishlist' : 'Add to wishlist');
            button.find('i').toggleClass('far fas');
        });

        $(card).append(button);
    };

    $('.fruite-item, .vesitable-item').each(function () {
        addWishlistButton(this);
    });

    $('img[src*="best-product-"]').each(function () {
        addWishlistButton($(this).closest('.p-4.rounded.bg-light')[0]);
    });

    $('img[src*="fruite-item-"]').filter(function () {
        return !$(this).closest('.fruite-item, .vesitable-item').length;
    }).each(function () {
        addWishlistButton($(this).closest('.text-center')[0]);
    });


    // Fixed Navbar
    $(window).scroll(function () {
        if ($(window).width() < 992) {
            if ($(this).scrollTop() > 55) {
                $('.fixed-top').addClass('shadow');
            } else {
                $('.fixed-top').removeClass('shadow');
            }
        } else {
            if ($(this).scrollTop() > 55) {
                $('.fixed-top').addClass('shadow').css('top', -55);
            } else {
                $('.fixed-top').removeClass('shadow').css('top', 0);
            }
        } 
    });
    
    
   // Back to top button
   $(window).scroll(function () {
    if ($(this).scrollTop() > 300) {
        $('.back-to-top').fadeIn('slow');
    } else {
        $('.back-to-top').fadeOut('slow');
    }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
        return false;
    });


    // Testimonial carousel
    $(".testimonial-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 2000,
        center: false,
        dots: true,
        loop: true,
        margin: 25,
        nav : true,
        navText : [
            '<i class="bi bi-arrow-left"></i>',
            '<i class="bi bi-arrow-right"></i>'
        ],
        responsiveClass: true,
        responsive: {
            0:{
                items:1
            },
            576:{
                items:1
            },
            768:{
                items:1
            },
            992:{
                items:2
            },
            1200:{
                items:2
            }
        }
    });


    // vegetable carousel
    $(".vegetable-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1500,
        center: false,
        dots: true,
        loop: true,
        margin: 25,
        nav : true,
        navText : [
            '<i class="bi bi-arrow-left"></i>',
            '<i class="bi bi-arrow-right"></i>'
        ],
        responsiveClass: true,
        responsive: {
            0:{
                items:1
            },
            576:{
                items:1
            },
            768:{
                items:2
            },
            992:{
                items:3
            },
            1200:{
                items:4
            }
        }
    });


    // Modal Video
    $(document).ready(function () {
        var $videoSrc;
        $('.btn-play').click(function () {
            $videoSrc = $(this).data("src");
        });
        console.log($videoSrc);

        $('#videoModal').on('shown.bs.modal', function (e) {
            $("#video").attr('src', $videoSrc + "?autoplay=1&amp;modestbranding=1&amp;showinfo=0");
        })

        $('#videoModal').on('hide.bs.modal', function (e) {
            $("#video").attr('src', $videoSrc);
        })
    });



    // Product Quantity
    $('.quantity button').on('click', function () {
        var button = $(this);
        var oldValue = button.parent().parent().find('input').val();
        if (button.hasClass('btn-plus')) {
            var newVal = parseFloat(oldValue) + 1;
        } else {
            if (oldValue > 0) {
                var newVal = parseFloat(oldValue) - 1;
            } else {
                newVal = 0;
            }
        }
        button.parent().parent().find('input').val(newVal);
    });

})(jQuery);

