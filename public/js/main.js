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

    var responsiveCounts = Object.keys(owlOptions.responsive || {}).map(function (breakpoint) {
        return owlOptions.responsive[breakpoint].items;
    });
    var maxVisibleItems = Math.max.apply(null, responsiveCounts.concat([1]));

    // Not enough items to scroll: turn off looping/autoplay,
    // but DO NOT change `items`, so card size stays consistent.
    var needsScroll = itemCount > maxVisibleItems;
    if (!needsScroll) {
        owlOptions.loop = false;
        owlOptions.autoplay = false;
        owlOptions.mouseDrag = false;
        owlOptions.touchDrag = false;
    }

    // Dots only make sense if the carousel can actually move
    if (needsScroll) {
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
    }

    $carousel.owlCarousel(owlOptions);

    if (!needsScroll) {
        return;
    }

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
    // The cart lives in the PHP session. The browser never stores it:
    // it only shows what the server sends back (window.BAZAAR_CART on page load,
    // then the JSON returned by /cart/add, /cart/items/{key} ...).
    var cartBase = String(window.BAZAAR_CART_URL || "/cart").replace(/\/$/, "");
    var csrfToken = $('meta[name="csrf-token"]').attr("content") || "";
    var emptyCart = { count: 0, subtotal: 0, items: [], notices: [] };
    var cart = emptyCart;

    // the old localStorage cart is not used any more
    try {
        localStorage.removeItem("bazaar-chalo-cart");
    } catch (error) {
        // storage may be blocked; nothing to clean
    }

    function escapeHtml(value) {
        return String(value == null ? "" : value).replace(/[&<>"']/g, function (character) {
            return {
                "&": "&amp;",
                "<": "&lt;",
                ">": "&gt;",
                '"': "&quot;",
                "'": "&#39;",
            }[character];
        });
    }

    // Same look as the server's Money::format(): "Rs. 2,500" (decimals only when needed)
    var currencySymbol = (window.BAZAAR_CURRENCY && window.BAZAAR_CURRENCY.symbol) || "Rs.";

    function formatCurrency(amount) {
        var value = Math.round(Number(amount || 0) * 100) / 100;
        var decimals = Math.abs(value - Math.round(value)) < 0.005 ? 0 : 2;

        return currencySymbol + " " + value.toLocaleString("en-US", {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals,
        });
    }

    window.BazaarMoney = formatCurrency;

    /* ----- small message toast ----- */
    function showCartToast(message, type) {
        if (!message) {
            return;
        }

        var $container = $("#bcToastContainer");
        if (!$container.length) {
            $container = $('<div id="bcToastContainer" class="bc-toast-container" aria-live="polite"></div>').appendTo("body");
        }

        var $toast = $('<div class="bc-toast"></div>')
            .addClass(type === "error" ? "bc-toast-error" : "bc-toast-info")
            .text(message);

        $container.append($toast);

        setTimeout(function () {
            $toast.addClass("hide");
            setTimeout(function () {
                $toast.remove();
            }, 300);
        }, 3800);
    }

    /* ----- rendering ----- */
    function updateCartBadge() {
        $(".nav-cart-count").text(cart.count);
    }

    function renderCartDrawer() {
        var $items = $("#cartDrawerItems");
        var $footer = $("#cartDrawerFooter");

        updateCartBadge();

        if (!cart.items.length) {
            $items.html(
                '<div class="cart-drawer-empty"><i class="fas fa-shopping-bag"></i><p>Your cart is empty</p></div>',
            );
            $footer.hide();
            return;
        }

        var html = "";
        cart.items.forEach(function (item) {
            html +=
                '<div class="cart-drawer-item" data-id="' + escapeHtml(item.key) + '">' +
                '<img src="' + escapeHtml(item.image) + '" alt="' + escapeHtml(item.name) + '" />' +
                '<div class="cart-drawer-item-info">' +
                '<a class="cart-drawer-item-title d-block" href="' + escapeHtml(item.url) + '">' + escapeHtml(item.name) + "</a>" +
                (item.variant_title ? '<small class="text-muted">' + escapeHtml(item.variant_title) + "</small>" : "") +
                '<div class="cart-drawer-item-price">' + formatCurrency(item.price) + "</div>" +
                '<div class="cart-drawer-qty">' +
                '<button type="button" data-action="decrease" aria-label="Decrease quantity">-</button>' +
                "<span>" + item.qty + "</span>" +
                '<button type="button" data-action="increase" aria-label="Increase quantity"' + (item.qty >= item.stock ? " disabled" : "") + ">+</button>" +
                "</div>" +
                "</div>" +
                '<button type="button" class="cart-drawer-item-remove" data-action="remove" aria-label="Remove item"><i class="fas fa-trash-alt"></i></button>' +
                "</div>";
        });

        $items.html(html);
        $footer.show();
        $("#cartDrawerSubtotal").text(formatCurrency(cart.subtotal));
    }

    function setCart(next) {
        cart = next && Array.isArray(next.items) ? next : emptyCart;
        renderCartDrawer();

        (cart.notices || []).forEach(function (notice) {
            showCartToast(notice, "info");
        });
    }

    /* ----- talking to the server ----- */
    function cartRequest(method, path, payload) {
        var headers = {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-CSRF-TOKEN": csrfToken,
            "X-Requested-With": "XMLHttpRequest",
        };

        // on the cart page / checkout page the server also sends that part of the page as HTML
        if (document.getElementById("cartPageContent")) {
            headers["X-Cart-View"] = "page";
        } else if (document.getElementById("checkoutSummary")) {
            headers["X-Cart-View"] = "checkout";
        }

        return fetch(cartBase + path, {
            method: method,
            credentials: "same-origin",
            headers: headers,
            body: payload ? JSON.stringify(payload) : undefined,
        }).then(function (response) {
            return response
                .json()
                .catch(function () {
                    return {};
                })
                .then(function (data) {
                    if (data && data.cart) {
                        setCart(data.cart); // the screen always shows the real cart
                    }

                    if (data && data.page_html) {
                        $("#cartPageContent, #checkoutSummary").html(data.page_html);
                        $(document).trigger("cart:page-updated");
                    }

                    if (!response.ok || (data && data.ok === false)) {
                        var message = (data && data.message) || "Something went wrong. Please try again.";
                        if (response.status === 419) {
                            message = "Your session has expired. Please refresh the page and try again.";
                        }

                        var error = new Error(message);
                        error.redirect = (data && data.redirect) || null;
                        throw error;
                    }

                    return data;
                });
        });
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

    // Public API used by the product page and any other script
    window.BazaarCart = {
        add: function (payload, options) {
            return cartRequest("POST", "/add", payload).then(function (data) {
                if (!options || options.open !== false) {
                    openCartDrawer();
                }
                return data;
            });
        },
        update: function (key, quantity) {
            return cartRequest("PATCH", "/items/" + encodeURIComponent(key), { quantity: quantity });
        },
        remove: function (key) {
            return cartRequest("DELETE", "/items/" + encodeURIComponent(key));
        },
        clear: function () {
            return cartRequest("DELETE", "/items");
        },
        refresh: function () {
            return cartRequest("GET", "/summary");
        },
        open: openCartDrawer,
        toast: showCartToast,
        get: function () {
            return cart;
        },
    };

    /* ----- events ----- */

    // "Add to cart" buttons on product cards (shop, home, related products)
    $(document).on("click", "[data-cart-add]", function (e) {
        e.preventDefault();
        e.stopPropagation();

        var button = this;
        var $button = $(button);

        // products with options must be chosen on the product page
        if (button.dataset.hasVariants === "1" && button.dataset.productUrl) {
            window.location.href = button.dataset.productUrl;
            return;
        }

        if ($button.data("busy")) {
            return;
        }
        $button.data("busy", true).addClass("disabled");

        window.BazaarCart.add({
            product_id: button.dataset.productId,
            variant_id: button.dataset.variantId || null,
            quantity: 1,
        })
            .catch(function (error) {
                showCartToast(error.message, "error");
                if (error.redirect) {
                    setTimeout(function () {
                        window.location.href = error.redirect;
                    }, 1200);
                }
            })
            .then(function () {
                $button.data("busy", false).removeClass("disabled");
            });
    });

    // + / - / remove inside the drawer and on the cart page
    $(document).on("click", "#cartDrawerItems [data-action], #cartPageItems [data-action]", function () {
        var $button = $(this);
        var $row = $button.closest("[data-id]");
        var key = String($row.attr("data-id"));
        var action = $button.attr("data-action");

        var item = cart.items.find(function (c) {
            return c.key === key;
        });

        if (!item || $row.data("busy")) {
            return;
        }
        $row.data("busy", true).find("button").prop("disabled", true);

        var request;
        if (action === "increase") {
            request = window.BazaarCart.update(key, item.qty + 1);
        } else if (action === "decrease") {
            request = item.qty <= 1 ? window.BazaarCart.remove(key) : window.BazaarCart.update(key, item.qty - 1);
        } else {
            request = window.BazaarCart.remove(key);
        }

        request.catch(function (error) {
            showCartToast(error.message, "error");
            $row.data("busy", false).find("button").prop("disabled", false);
        });
    });

    $("#cartDrawerClose, #cartDrawerBackdrop, #cartDrawerContinue").on("click", function () {
        closeCartDrawer();
    });

    // "Clear cart" button on the cart page
    $(document).on("click", "[data-cart-clear]", function () {
        if (!window.confirm("Remove all items from your cart?")) {
            return;
        }

        window.BazaarCart.clear().catch(function (error) {
            showCartToast(error.message, "error");
        });
    });

    $(document).on("keydown", function (e) {
        if (e.key === "Escape") {
            closeCartDrawer();
            closeShopFilters();
        }
    });

    $(".nav-cart-link").on("click", function (e) {
        if (cart.items.length) {
            e.preventDefault();
            openCartDrawer();
        }
    });

    // coming back with the browser's back button: ask the server for the current cart
    window.addEventListener("pageshow", function (event) {
        if (event.persisted) {
            window.BazaarCart.refresh().catch(function () {});
        }
    });

    setCart(window.BAZAAR_CART);
    /*** Cart Drawer End ***/

    /*** Shop Filters Start ***/
    var shopFilterPanel = document.getElementById("shopFilterPanel");
    var shopFilterBackdrop = document.getElementById("shopFilterBackdrop");
    var shopFiltersToggle = document.getElementById("shopFiltersToggle");
    var shopFiltersClose = document.getElementById("shopFiltersClose");
    var shopFilterForm = document.getElementById("shopFilterForm");

    function closeShopFilters() {
        if (!shopFilterPanel || !shopFilterBackdrop || !shopFiltersToggle) {
            return;
        }

        shopFilterPanel.classList.remove("is-open");
        shopFilterBackdrop.classList.remove("is-open");
        shopFiltersToggle.setAttribute("aria-expanded", "false");
        document.body.classList.remove("shop-filter-open");
    }

    if (shopFilterPanel && shopFilterBackdrop && shopFiltersToggle) {
        shopFiltersToggle.addEventListener("click", function () {
            var isOpen = shopFilterPanel.classList.toggle("is-open");
            shopFilterBackdrop.classList.toggle("is-open", isOpen);
            shopFiltersToggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
            document.body.classList.toggle("shop-filter-open", isOpen);
        });

        shopFilterBackdrop.addEventListener("click", closeShopFilters);
        if (shopFiltersClose) {
            shopFiltersClose.addEventListener("click", closeShopFilters);
        }
        if (shopFilterForm) {
            shopFilterForm.addEventListener("submit", closeShopFilters);
        }
    }

    var priceSlider = document.getElementById("shopPriceSlider");
    var minPriceInput = document.getElementById("minPrice");
    var maxPriceInput = document.getElementById("maxPrice");

    if (priceSlider && minPriceInput && maxPriceInput) {
        var priceLimit = Number(priceSlider.dataset.priceLimit) || 1;

        var updatePriceRange = function (activeInput) {
            var minPrice = Number(minPriceInput.value);
            var maxPrice = Number(maxPriceInput.value);

            if (activeInput === minPriceInput && minPrice >= maxPrice) {
                minPrice = Math.max(0, maxPrice - 1);
                minPriceInput.value = minPrice;
            } else if (activeInput === maxPriceInput && maxPrice <= minPrice) {
                maxPrice = Math.min(priceLimit, minPrice + 1);
                maxPriceInput.value = maxPrice;
            }

            $("#minPriceOutput").text("$" + minPrice.toLocaleString());
            $("#maxPriceOutput").text("$" + maxPrice.toLocaleString());
            priceSlider.style.setProperty("--price-min-position", (minPrice / priceLimit) * 100 + "%");
            priceSlider.style.setProperty("--price-max-position", (maxPrice / priceLimit) * 100 + "%");
        };

        [minPriceInput, maxPriceInput].forEach(function (input) {
            input.addEventListener("input", function () {
                minPriceInput.style.zIndex = input === minPriceInput ? "3" : "2";
                maxPriceInput.style.zIndex = input === maxPriceInput ? "3" : "2";
                updatePriceRange(input);
            });
        });

        updatePriceRange();
    }
    /*** Shop Filters End ***/

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
