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
    var cartStorageKey = "bazaar-chalo-cart";
    var cart = [];

    try {
        var savedCart = JSON.parse(localStorage.getItem(cartStorageKey) || "[]");
        cart = Array.isArray(savedCart) ? savedCart : [];
    } catch (error) {
        cart = [];
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

    function formatCurrency(amount) {
        return "$" + amount.toFixed(2);
    }

    function getCartTotalQty() {
        return cart.reduce(function (sum, item) {
            return sum + item.qty;
        }, 0);
    }

    function updateCartBadge() {
        $(".nav-cart-count").text(getCartTotalQty());
    }

    function renderCartPage() {
        var $items = $("#cartPageItems");
        if (!$items.length) {
            return;
        }

        if (!cart.length) {
            $items.html('<tr><td colspan="6" class="text-center py-5">Your cart is empty. <a href="' + escapeHtml($("#shopPageLink").attr("href") || "/shop") + '">Browse products</a>.</td></tr>');
            $("#cartPageSubtotal, #cartPageTotal").text(formatCurrency(0));
            $("#cartPageShipping").text(formatCurrency(0));
            $("#cartPageCheckout").addClass("disabled").attr("aria-disabled", "true");
            return;
        }

        var subtotal = 0;
        var html = "";
        cart.forEach(function (item) {
            var price = Number(item.price) || 0;
            var quantity = Number(item.qty) || 1;
            subtotal += price * quantity;
            html +=
                '<tr data-id="' + escapeHtml(item.id) + '">' +
                '<td><img src="' + escapeHtml(item.image) + '" alt="' + escapeHtml(item.name) + '" class="rounded" style="width: 72px; height: 72px; object-fit: cover" /></td>' +
                '<td><span class="d-block fw-semibold">' + escapeHtml(item.name) + '</span>' +
                (item.variantTitle ? '<small class="text-muted">' + escapeHtml(item.variantTitle) + '</small>' : "") + "</td>" +
                '<td>' + formatCurrency(price) + "</td>" +
                '<td><div class="d-inline-flex align-items-center gap-2">' +
                '<button type="button" class="btn btn-sm btn-light" data-action="decrease" aria-label="Decrease quantity">-</button>' +
                '<span>' + quantity + "</span>" +
                '<button type="button" class="btn btn-sm btn-light" data-action="increase" aria-label="Increase quantity">+</button>' +
                "</div></td>" +
                '<td>' + formatCurrency(price * quantity) + "</td>" +
                '<td><button type="button" class="btn btn-sm btn-outline-danger" data-action="remove" aria-label="Remove item"><i class="fas fa-trash-alt" aria-hidden="true"></i></button></td>' +
                "</tr>";
        });

        var shipping = 3;
        $items.html(html);
        $("#cartPageSubtotal").text(formatCurrency(subtotal));
        $("#cartPageShipping").text(formatCurrency(shipping));
        $("#cartPageTotal").text(formatCurrency(subtotal + shipping));
        $("#cartPageCheckout").removeClass("disabled").removeAttr("aria-disabled");
    }

    function persistCart() {
        try {
            localStorage.setItem(cartStorageKey, JSON.stringify(cart));
        } catch (error) {
            // The current page can still use the in-memory cart if storage is unavailable.
        }
        renderCartDrawer();
        renderCartPage();
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
            var price = Number(item.price) || 0;
            subtotal += price * item.qty;
            html +=
                '<div class="cart-drawer-item" data-id="' + escapeHtml(item.id) + '">' +
                '<img src="' + escapeHtml(item.image) + '" alt="' + escapeHtml(item.name) + '" />' +
                '<div class="cart-drawer-item-info">' +
                '<div class="cart-drawer-item-title">' + escapeHtml(item.name) + "</div>" +
                (item.variantTitle ? '<small class="text-muted">' + escapeHtml(item.variantTitle) + '</small>' : "") +
                '<div class="cart-drawer-item-price">' + formatCurrency(price) + "</div>" +
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
            existing.qty = Math.min(existing.qty + item.qty, item.stock || Infinity);
        } else {
            cart.push(item);
        }

        persistCart();
        openCartDrawer();
    }

    $(document).on("click", "[data-cart-add]", function (e) {
        e.preventDefault();
        e.stopPropagation();

        addToCart({
            id: "product-" + this.dataset.productId,
            productId: this.dataset.productId,
            name: this.dataset.productName,
            price: Number(this.dataset.productPrice) || 0,
            image: this.dataset.productImage || "",
            stock: Number(this.dataset.productStock) || 1,
            qty: 1,
        });
    });

    $(document).on("change", "#productVariant", function () {
        var option = this.options[this.selectedIndex];
        var stock = Number(option.dataset.stock) || 0;
        var quantity = document.getElementById("detailQuantity");
        var addButton = document.getElementById("detailAddToCart");
        var price = document.getElementById("detailPrice");

        addButton.disabled = !this.value || stock < 1;
        quantity.max = Math.max(stock, 1);
        quantity.value = 1;
        price.textContent = option.dataset.price ? formatCurrency(Number(option.dataset.price)) : "";
        $("#variantStockMessage").text(this.value ? stock + " in stock" : "");
    });

    $(document).on("submit", "#productDetailCartForm", function (event) {
        event.preventDefault();
        var form = this;
        var variant = document.getElementById("productVariant");
        var selected = variant && variant.options[variant.selectedIndex];
        var quantity = Math.max(1, Number(document.getElementById("detailQuantity").value) || 1);
        var price = Number(form.dataset.productPrice) || 0;
        var variantId = null;
        var variantTitle = "";
        var stock = Number(form.dataset.productStock) || 0;

        if (form.dataset.hasVariants === "1") {
            if (!selected || !variant.value) {
                variant.focus();
                return;
            }
            price = Number(selected.dataset.price) || 0;
            stock = Number(selected.dataset.stock) || 0;
            variantId = variant.value;
            variantTitle = selected.dataset.title || selected.textContent.trim();
        }

        if (quantity > stock) {
            document.getElementById("variantStockMessage").textContent = "Only " + stock + " available.";
            return;
        }

        addToCart({
            id: "product-" + form.dataset.productId + (variantId ? "-variant-" + variantId : ""),
            productId: form.dataset.productId,
            variantId: variantId,
            variantTitle: variantTitle,
            name: form.dataset.productName,
            price: price,
            image: form.dataset.productImage,
            stock: stock,
            qty: quantity,
        });
    });

    $(document).on("click", "[data-product-image]", function () {
        $("#productDetailImage").attr("src", this.dataset.productImage);
    });

    $(document).on("click", "#cartDrawerItems [data-action], #cartPageItems [data-action]", function () {
        var action = $(this).data("action");
        var id = $(this).closest("[data-id]").data("id");
        var item = cart.find(function (c) {
            return c.id === id;
        });

        if (!item) {
            return;
        }

        if (action === "increase") {
            if (!item.stock || item.qty < item.stock) {
                item.qty += 1;
            }
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

        persistCart();
    });

    $("#cartDrawerClose, #cartDrawerBackdrop, #cartDrawerContinue").on("click", function () {
        closeCartDrawer();
    });

    $(document).on("click", "#cartPageCheckout[aria-disabled='true']", function (event) {
        event.preventDefault();
    });

    $(document).on("keydown", function (e) {
        if (e.key === "Escape") {
            closeCartDrawer();
            closeShopFilters();
        }
    });

    $(".nav-cart-link").on("click", function (e) {
        if (cart.length) {
            e.preventDefault();
            openCartDrawer();
        }
    });

    renderCartDrawer();
    renderCartPage();
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
