(function ($) {
    "use strict";

    // Spinner
    var spinner = function () {
        setTimeout(function () {
            if ($("#spinner").length > 0) {
                $("#spinner").removeClass("show");
            }
        }, 1);
    };
    spinner(0);

    // Keep the original template pages intact while using the Bazaar Chalo home flow.
    var bazaarHome = document.getElementById("bazaar-home-content");
    if (bazaarHome) {
        var removeLegacy = false;
        var homeFooter = document
            .querySelector(".footer")
            ?.closest(".container-fluid");
        Array.from(document.body.children).forEach(function (child) {
            if (child === bazaarHome) {
                removeLegacy = true;
                return;
            }

            if (removeLegacy && child !== homeFooter && child.tagName !== "SCRIPT") {
                child.remove();
            }

            if (child === homeFooter) {
                removeLegacy = false;
            }
        });
    }

    var cartLink = document.querySelector(".fa-shopping-bag")?.closest("a");
    var accountLink = document.querySelector(".fa-user")?.closest("a");
    if (cartLink) cartLink.href = "cart.html";
    if (accountLink) accountLink.href = "account.html";

    // Build one consistent mega menu for every template page.
    $(".mega-menu-wrapper").each(function () {
        var wrapper = $(this);
        var trigger = wrapper.children(".nav-link");
        var menu = wrapper.children(".mega-menu");

        if (!trigger.length || !menu.length) {
            return;
        }

        trigger
            .attr({
                href: "#",
                role: "button",
                "aria-haspopup": "true",
                "aria-expanded": "false",
            })
            .removeAttr("data-bs-toggle");

        menu.html(
            '<div class="mega-menu-content">' +
            '<div class="mega-menu-columns">' +
            '<div class="mega-menu-column">' +
            '<p class="mega-menu-heading">Fresh produce</p>' +
            '<a href="shop.html">Fresh fruits</a>' +
            '<a href="shop.html">Vegetables</a>' +
            '<a href="shop.html">Bananas</a>' +
            '<a href="shop.html">Grapes</a>' +
            "</div>" +
            '<div class="mega-menu-column">' +
            '<p class="mega-menu-heading">Everyday essentials</p>' +
            '<a href="shop.html">Bell peppers</a>' +
            '<a href="shop.html">Potatoes</a>' +
            '<a href="shop.html">Bakery and pantry</a>' +
            '<a href="shop.html">Dairy and eggs</a>' +
            "</div>" +
            '<div class="mega-menu-column">' +
            '<p class="mega-menu-heading">Shop by occasion</p>' +
            '<a href="shop.html">Seasonal offers</a>' +
            '<a href="shop.html">Organic picks</a>' +
            '<a href="shop.html">Family favourites</a>' +
            '<a href="shop.html">New arrivals</a>' +
            "</div>" +
            "</div>" +
            '<a href="category-listing.html" class="mega-menu-feature">' +
            '<img src="img/featur-2.jpg" alt="Fresh groceries from local sellers">' +
            '<span><strong>Fresh picks, close to home</strong><small>Explore all categories <i class="fas fa-arrow-right"></i></small></span>' +
            "</a>" +
            "</div>",
        );

        trigger.on("click", function (event) {
            if (window.innerWidth < 1200) {
                event.preventDefault();
                var isOpen = menu.toggleClass("show").hasClass("show");
                trigger.attr("aria-expanded", isOpen ? "true" : "false");
            } else {
                event.preventDefault();
            }
        });

        $("#navbarCollapse").on("hide.bs.collapse", function () {
            menu.removeClass("show");
            trigger.attr("aria-expanded", "false");
        });

        wrapper.on("focusout", function (event) {
            if (
                !wrapper[0].contains(event.relatedTarget) &&
                window.innerWidth >= 1200
            ) {
                menu.removeClass("show");
                trigger.attr("aria-expanded", "false");
            }
        });
    });

    // Wishlist buttons on product cards
    var addWishlistButton = function (card) {
        if (!card || card.querySelector(".wishlist-button")) {
            return;
        }

        var button = $("<button>", {
            type: "button",
            class: "wishlist-button",
            title: "Add to wishlist",
            "aria-label": "Add to wishlist",
        }).append('<i class="far fa-heart" aria-hidden="true"></i>');

        button.on("click", function () {
            var active = button.toggleClass("active").hasClass("active");
            button.attr("title", active ? "Remove from wishlist" : "Add to wishlist");
            button.attr(
                "aria-label",
                active ? "Remove from wishlist" : "Add to wishlist",
            );
            button.find("i").toggleClass("far fas");
        });

        $(card).append(button);
    };

    $(".product-card, .fruite-item, .vesitable-item").each(function () {
        addWishlistButton(this);
    });

    $(".fruite-item a.btn, .vesitable-item a.btn").each(function () {
        $(this).attr("href", "cart.html").attr("aria-label", "Add product to cart");
    });

    $('img[src*="best-product-"]').each(function () {
        addWishlistButton($(this).closest(".p-4.rounded.bg-light")[0]);
    });

    $('img[src*="fruite-item-"]')
        .filter(function () {
            return !$(this).closest(".fruite-item, .vesitable-item").length;
        })
        .each(function () {
            addWishlistButton($(this).closest(".text-center")[0]);
        });

    // Fixed Navbar
    $(window).scroll(function () {
        if ($(window).width() < 992) {
            if ($(this).scrollTop() > 55) {
                $(".fixed-top").addClass("shadow");
            } else {
                $(".fixed-top").removeClass("shadow");
            }
        } else {
            if ($(this).scrollTop() > 55) {
                $(".fixed-top").addClass("shadow").css("top", -55);
            } else {
                $(".fixed-top").removeClass("shadow").css("top", 0);
            }
        }
    });

    // Back to top button
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $(".back-to-top").fadeIn("slow");
        } else {
            $(".back-to-top").fadeOut("slow");
        }
    });
    $(".back-to-top").click(function () {
        $("html, body").animate({ scrollTop: 0 }, 1500, "easeInOutExpo");
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
        nav: true,
        navText: [
            '<i class="bi bi-arrow-left"></i>',
            '<i class="bi bi-arrow-right"></i>',
        ],
        responsiveClass: true,
        responsive: {
            0: {
                items: 1,
            },
            576: {
                items: 1,
            },
            768: {
                items: 1,
            },
            992: {
                items: 2,
            },
            1200: {
                items: 2,
            },
        },
    });

    // vegetable carousel
    $(".vegetable-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1500,
        center: false,
        dots: true,
        loop: true,
        margin: 25,
        nav: true,
        navText: [
            '<i class="bi bi-arrow-left"></i>',
            '<i class="bi bi-arrow-right"></i>',
        ],
        responsiveClass: true,
        responsive: {
            0: {
                items: 1,
            },
            576: {
                items: 1,
            },
            768: {
                items: 2,
            },
            992: {
                items: 3,
            },
            1200: {
                items: 4,
            },
        },
    });

    // Modal Video
    $(document).ready(function () {
        var $videoSrc;
        $(".btn-play").click(function () {
            $videoSrc = $(this).data("src");
        });
        console.log($videoSrc);

        $("#videoModal").on("shown.bs.modal", function (e) {
            $("#video").attr(
                "src",
                $videoSrc + "?autoplay=1&amp;modestbranding=1&amp;showinfo=0",
            );
        });

        $("#videoModal").on("hide.bs.modal", function (e) {
            $("#video").attr("src", $videoSrc);
        });
    });

    // Product Quantity
    $(".quantity button").on("click", function () {
        var button = $(this);
        var oldValue = button.parent().parent().find("input").val();
        if (button.hasClass("btn-plus")) {
            var newVal = parseFloat(oldValue) + 1;
        } else {
            if (oldValue > 0) {
                var newVal = parseFloat(oldValue) - 1;
            } else {
                newVal = 0;
            }
        }
        button.parent().parent().find("input").val(newVal);
    });

    // Nav search: smooth expanding search box (replaces the old modal)
    var searchToggle = document.getElementById("navSearchToggle");
    var searchBox = document.getElementById("navSearchBox");
    var searchInput = document.getElementById("navSearchInput");
    var searchClose = document.getElementById("navSearchClose");

    if (searchToggle && searchBox) {
        var openSearch = function () {
            searchBox.classList.add("active");
            searchToggle.setAttribute("aria-expanded", "true");
            setTimeout(function () {
                searchInput.focus();
            }, 250);
        };
        var closeSearch = function () {
            searchBox.classList.remove("active");
            searchToggle.setAttribute("aria-expanded", "false");
            searchInput.value = "";
        };

        searchToggle.addEventListener("click", function (e) {
            e.preventDefault();
            searchBox.classList.contains("active") ? closeSearch() : openSearch();
        });

        if (searchClose) {
            searchClose.addEventListener("click", closeSearch);
        }

        document.addEventListener("click", function (e) {
            if (
                !searchBox.contains(e.target) &&
                e.target !== searchToggle &&
                !searchToggle.contains(e.target)
            ) {
                closeSearch();
            }
        });

        document.addEventListener("keydown", function (e) {
            if (e.key === "Escape") closeSearch();
        });
    }
})(jQuery);
