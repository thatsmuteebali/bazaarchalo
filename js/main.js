(function ($) {
    "use strict";

    /*** Spinner Start ***/
    var spinner = function () {
        setTimeout(function () {
            if ($("#spinner").length > 0) {
                $("#spinner").removeClass("show");
            }
        }, 1);
    };
    spinner(0);
    /*** Spinner End ***/

    /*** AOS Init Start ***/
    // Runs on every page load/reload so sections animate in as soon as the
    // page (or the part of it currently in view) is ready and as the user
    // scrolls further down.
    if (typeof AOS !== "undefined") {
        AOS.init({
            duration: 800,
            easing: "ease-out-cubic",
            once: true,
            offset: 80,
            mirror: false,
            anchorPlacement: "top-bottom",
        });

        // Re-measure trigger points once carousels/images have settled,
        // so AOS offsets stay accurate after layout shifts.
        $(window).on("load", function () {
            AOS.refreshHard();
        });
    }
    /*** AOS Init End ***/

    /*** Dotted Carousel Helper Start ***/
    // Reusable: turns any .owl-carousel into a dot-navigated carousel.
    // Used by both the category carousel and the product carousel.
    function initDottedCarousel($carousel, owlOptions) {
        if (!$carousel.length) {
            return;
        }

        var itemCount = $carousel.children().length;
        var $dots = $('<div class="category-dots"></div>');

        for (var i = 0; i < itemCount; i++) {
            $dots.append(
                $("<button>", {
                    type: "button",
                    class: "category-dot",
                    "aria-label": "Go to item " + (i + 1),
                }),
            );
        }
        $carousel.after($dots);

        $carousel.owlCarousel(owlOptions);

        var owlApi = $carousel.data("owl.carousel");

        var setActiveDot = function (realIndex) {
            $dots.find(".category-dot").removeClass("active").eq(realIndex).addClass("active");
        };

        $carousel.on("translated.owl.carousel", function () {
            if (owlApi) {
                setActiveDot(owlApi.relative(owlApi.current()));
            }
        });

        $dots.on("click", ".category-dot", function () {
            $carousel.trigger("to.owl.carousel", [$(this).index(), 300]);
        });

        setActiveDot(0);
    }
    /*** Dotted Carousel Helper End ***/

    /*** Category Carousel Start ***/
    initDottedCarousel($(".category-carousel"), {
        autoplay: true,
        autoplayTimeout: 2500,
        smartSpeed: 1000,
        center: false,
        loop: true,
        margin: 25,
        dots: false,
        nav: false,
        responsiveClass: true,
        responsive: {
            0: { items: 2 },
            576: { items: 3 },
            768: { items: 4 },
            992: { items: 4 },
            1200: { items: 5 },
        },
    });
    /*** Category Carousel End ***/

    /*** Product Carousel Start ***/
    initDottedCarousel($(".product-carousel"), {
        autoplay: true,
        autoplayTimeout: 3000,
        smartSpeed: 1000,
        center: false,
        loop: true,
        margin: 25,
        dots: false,
        nav: false,
        responsiveClass: true,
        responsive: {
            0: { items: 1 },
            576: { items: 2 },
            768: { items: 3 },
            992: { items: 3 },
            1200: { items: 4 },
        },
    });
    /*** Product Carousel End ***/

    /*** Mega Menu Start ***/
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
    /*** Mega Menu End ***/

    /*** Wishlist Buttons Start ***/
    var addWishlistButton = function (card) {
        if (!card || card.querySelector(".wishlist-button")) {
            return;
        }

        $("<button>", {
            type: "button",
            class: "wishlist-button",
            title: "Add to wishlist",
            "aria-label": "Add to wishlist",
        })
            .append('<i class="far fa-heart" aria-hidden="true"></i>')
            .appendTo(card);
    };

    $(".fruite-item, .vesitable-item").each(function () {
        addWishlistButton(this);
    });

    $('img[src*="best-product-"]').each(function () {
        addWishlistButton($(this).closest(".p-4.rounded.bg-light")[0]);
    });

    $('img[src*="fruite-item-"]')
        .filter(function () {
            return !$(this).closest(".fruite-item, .vesitable-item, .product-card").length;
        })
        .each(function () {
            addWishlistButton($(this).closest(".text-center")[0]);
        });

    $(document).on("click", ".wishlist-button", function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $button = $(this);
        var active = $button.toggleClass("active").hasClass("active");
        $button.attr("title", active ? "Remove from wishlist" : "Add to wishlist");
        $button.attr(
            "aria-label",
            active ? "Remove from wishlist" : "Add to wishlist",
        );
        $button.find("i").toggleClass("far fas");
    });
    /*** Wishlist Buttons End ***/

    /*** Cart Drawer Start ***/
    var cart = [];

    function formatCurrency(amount) {
        return "$" + amount.toFixed(2);
    }

    function slugify(text) {
        return text
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9]+/g, "-")
            .replace(/(^-|-$)/g, "");
    }

    function getCartTotalQty() {
        return cart.reduce(function (sum, item) {
            return sum + item.qty;
        }, 0);
    }

    function updateCartBadge() {
        $(".nav-cart-count").text(getCartTotalQty());
    }

    function renderCartDrawer() {
        var $items = $("#cartDrawerItems");
        var $footer = $("#cartDrawerFooter");

        if (!cart.length) {
            $items.html(
                '<div class="cart-drawer-empty"><i class="fas fa-shopping-bag"></i><p>Your cart is empty</p></div>',
            );
            $footer.hide();
            updateCartBadge();
            return;
        }

        var subtotal = 0;
        var html = "";

        cart.forEach(function (item) {
            subtotal += item.price * item.qty;
            html +=
                '<div class="cart-drawer-item" data-id="' + item.id + '">' +
                '<img src="' + item.image + '" alt="' + item.name + '" />' +
                '<div class="cart-drawer-item-info">' +
                '<div class="cart-drawer-item-title">' + item.name + "</div>" +
                '<div class="cart-drawer-item-price">' + formatCurrency(item.price) + "</div>" +
                '<div class="cart-drawer-qty">' +
                '<button type="button" data-action="decrease" aria-label="Decrease quantity">-</button>' +
                "<span>" + item.qty + "</span>" +
                '<button type="button" data-action="increase" aria-label="Increase quantity">+</button>' +
                "</div>" +
                "</div>" +
                '<button type="button" class="cart-drawer-item-remove" data-action="remove" aria-label="Remove item"><i class="fas fa-trash-alt"></i></button>' +
                "</div>";
        });

        $items.html(html);
        $footer.show();
        $("#cartDrawerSubtotal").text(formatCurrency(subtotal));
        updateCartBadge();
    }

    function openCartDrawer() {
        $("#cartDrawerBackdrop").addClass("show");
        $("#cartDrawer").addClass("show").attr("aria-hidden", "false");
        $("body").addClass("cart-drawer-open");
    }

    function closeCartDrawer() {
        $("#cartDrawerBackdrop").removeClass("show");
        $("#cartDrawer").removeClass("show").attr("aria-hidden", "true");
        $("body").removeClass("cart-drawer-open");
    }

    function addToCart(item) {
        var existing = cart.find(function (c) {
            return c.id === item.id;
        });

        if (existing) {
            existing.qty += 1;
        } else {
            cart.push(item);
        }

        renderCartDrawer();
        openCartDrawer();
    }

    $(document).on("click", ".product-cart-cta", function (e) {
        e.preventDefault();
        e.stopPropagation();

        var $card = $(this).closest(".product-card");
        var name = $card.find(".product-title").first().text().trim();
        var priceText = $card.find(".product-price-current").first().text().trim();
        var price = parseFloat(priceText.replace(/[^0-9.]/g, "")) || 0;
        var image = $card.find(".product-image-primary").first().attr("src") || "";

        addToCart({
            id: slugify(name),
            name: name,
            price: price,
            image: image,
            qty: 1,
        });
    });

    $(document).on("click", "#cartDrawerItems [data-action]", function () {
        var action = $(this).data("action");
        var id = $(this).closest(".cart-drawer-item").data("id");
        var item = cart.find(function (c) {
            return c.id === id;
        });

        if (!item) {
            return;
        }

        if (action === "increase") {
            item.qty += 1;
        } else if (action === "decrease") {
            item.qty -= 1;
            if (item.qty <= 0) {
                cart = cart.filter(function (c) {
                    return c.id !== id;
                });
            }
        } else if (action === "remove") {
            cart = cart.filter(function (c) {
                return c.id !== id;
            });
        }

        renderCartDrawer();
    });

    $("#cartDrawerClose, #cartDrawerBackdrop, #cartDrawerContinue").on("click", function () {
        closeCartDrawer();
    });

    $(document).on("keydown", function (e) {
        if (e.key === "Escape") {
            closeCartDrawer();
        }
    });

    $(".nav-cart-link").on("click", function (e) {
        if (cart.length) {
            e.preventDefault();
            openCartDrawer();
        }
    });

    renderCartDrawer();
    /*** Cart Drawer End ***/

    /*** Fixed Navbar Start ***/
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
    /*** Fixed Navbar End ***/

    /*** Back To Top Start ***/
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
    /*** Back To Top End ***/

    /*** Testimonial Carousel Start ***/
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
            0: { items: 1 },
            576: { items: 1 },
            768: { items: 1 },
            992: { items: 2 },
            1200: { items: 2 },
        },
    });
    /*** Testimonial Carousel End ***/

    /*** Vegetable Carousel Start ***/
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
            0: { items: 1 },
            576: { items: 1 },
            768: { items: 2 },
            992: { items: 3 },
            1200: { items: 4 },
        },
    });
    /*** Vegetable Carousel End ***/

    /*** Modal Video Start ***/
    $(document).ready(function () {
        var $videoSrc;
        $(".btn-play").click(function () {
            $videoSrc = $(this).data("src");
        });

        $("#videoModal").on("shown.bs.modal", function () {
            $("#video").attr(
                "src",
                $videoSrc + "?autoplay=1&amp;modestbranding=1&amp;showinfo=0",
            );
        });

        $("#videoModal").on("hide.bs.modal", function () {
            $("#video").attr("src", $videoSrc);
        });
    });
    /*** Modal Video End ***/

    /*** Product Quantity Start ***/
    $(".quantity button").on("click", function () {
        var button = $(this);
        var oldValue = button.parent().parent().find("input").val();
        var newVal;
        if (button.hasClass("btn-plus")) {
            newVal = parseFloat(oldValue) + 1;
        } else {
            newVal = oldValue > 0 ? parseFloat(oldValue) - 1 : 0;
        }
        button.parent().parent().find("input").val(newVal);
    });
    /*** Product Quantity End ***/

    /*** Nav Search Start ***/
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
    /*** Nav Search End ***/

})(jQuery);