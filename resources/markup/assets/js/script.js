"use strict";

function _slicedToArray(arr, i) {
    return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest();
}

function _nonIterableRest() {
    throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}

function _unsupportedIterableToArray(o, minLen) {
    if (!o) return;
    if (typeof o === "string") return _arrayLikeToArray(o, minLen);
    var n = Object.prototype.toString.call(o).slice(8, -1);
    if (n === "Object" && o.constructor) n = o.constructor.name;
    if (n === "Map" || n === "Set") return Array.from(o);
    if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen);
}

function _arrayLikeToArray(arr, len) {
    if (len == null || len > arr.length) len = arr.length;
    for (var i = 0, arr2 = new Array(len); i < len; i++) {
        arr2[i] = arr[i];
    }
    return arr2;
}

function _iterableToArrayLimit(arr, i) {
    var _i = arr == null ? null : typeof Symbol !== "undefined" && arr[Symbol.iterator] || arr["@@iterator"];
    if (_i == null) return;
    var _arr = [];
    var _n = true;
    var _d = false;
    var _s, _e;
    try {
        for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) {
            _arr.push(_s.value);
            if (i && _arr.length === i) break;
        }
    } catch (err) {
        _d = true;
        _e = err;
    } finally {
        try {
            if (!_n && _i["return"] != null) _i["return"]();
        } finally {
            if (_d) throw _e;
        }
    }
    return _arr;
}

function _arrayWithHoles(arr) {
    if (Array.isArray(arr)) return arr;
}

(function($) {
    "use strict";
    $("img").on("dragstart", (function(event) {
        event.preventDefault();
    }));
    $(window).on("scroll touchmove", (function() {
        $("body").toggleClass("scroll", $(document).scrollTop() > 0);
    }));
    function windowWidth() {
        return window.innerWidth;
    }
    var debounce = function debounce(func, delay) {
        var inDebounce;
        return function() {
            var context = this;
            var args = arguments;
            clearTimeout(inDebounce);
            inDebounce = setTimeout((function() {
                return func.apply(context, args);
            }), delay);
        };
    };
 //** cardsGrid */ 
        $(function() {
        var gutter = windowWidth() <= 767 ? 15 : 30;
        var $cardsGrid = $(".cards-list");
        $cardsGrid.imagesLoaded((function() {
            $cardsGrid.masonry({
                itemSelector: "none",
                columnWidth: ".cards-list__card",
                gutter: 0,
                stagger: 100,
                horizontalOrder: true
            });
            $cardsGrid.addClass("is-visible-items");
            $cardsGrid.masonry("option", {
                itemSelector: ".cards-list__card"
            });
            var $items = $cardsGrid.find(".cards-list__card");
            $cardsGrid.masonry("appended", $items);
        }));
    }());
 //** Search part */ 
        $(function() {
        if ($(window).width() < 575) {
            $(".msf-query__inp").attr("placeholder", "Шукати за ...");
        }
        //** mainSearchFormSmbHandler demo */
                function mainSearchFormSmbHandler() {
            alert("mainSearchFormSmb handler demo");
        }
        function uncheckTag(id) {
            var target = document.getElementById(id);
            target.checked = false;
            removeTagFromTagList(id);
        }
 //** removeTag from tags list */
                function removeTagFromTagList(id) {
            return $("[data-filter-id=".concat(id, "]")).remove();
        }
        //** add tag to tag list */
                function addTagToTagList(id, text) {
            var tagsList = $(".tags-list");
            $("<li/>", {
                class: "tags-list__tag",
                "data-filter-id": id,
                on: {
                    click: function click() {
                        uncheckTag(id);
                    }
                },
                append: '<button class="tag">' + text + "</button>",
                appendTo: ".tags-list"
            });
        }
 //** change form chkb block + remove tag */
                $(".tags-list__tag").click((function(e) {
            var id = $(this).attr("data-filter-id");
            uncheckTag(id);
            $(this).remove();
        }));
 //** tags CHANGE evt */
                $(".chkb-block__ctrl").change((function(e) {
            var id = e.target.id;
            var val = $("#".concat(id)).next(".chkb-block__lbl").text();
            var status = $("#".concat(id)).is(":checked");
            status ? addTagToTagList(id, val) : removeTagFromTagList(id);
        }));
 //** smb evt */
                $("#mainSearchForm").submit((function(event) {
            event.preventDefault();
        }));
 //** page reload */
                $(".msf-reset").click((function(e) {
            location.reload();
        }));
        $(".msf-query__inp").focus((function(e) {
            $(".srch-sgs").addClass("active");
        }));
        $(document).on("mouseup touchend ", (function(e) {
            var block01 = $(".ms-ctrls__col--srch");
            if (block01.has(e.target).length === 0 && $(".srch-sgs").hasClass("active")) {
                $(".srch-sgs").removeClass("active");
            }
        }));
    }());
 //** filter -> on xs init - acc*/
        (function() {
        if (windowWidth() >= 576) return;
        $(".msf-filter").hide();
        $(".active > .msf-filter").show();
        $(".msf-list__name").click((function() {
            if (!$(this).siblings(".msf-filter").length) return;
            if ($(this).parent().hasClass("active")) {
                $(this).parent().removeClass("active").children(".msf-filter").slideUp(300);
                $(this).siblings(".msf-filter").slideUp(300);
            } else {
                $(this).parent().addClass("active").siblings(".active").removeClass("active").children(".msf-filter").slideUp(300);
                $(this).siblings(".msf-filter").slideDown(300);
            }
            return false;
        }));
    })();
    function toggleFullScreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    }
    function copyUrl() {
        var dummy = document.createElement("input"), text = window.location.href;
        document.body.appendChild(dummy);
        dummy.value = text;
        dummy.select();
        document.execCommand("copy");
        document.body.removeChild(dummy);
        showToast();
    }
    function showToast() {
        var x = document.getElementById("toast");
        x.className = "show";
        setTimeout((function() {
            x.className = x.className.replace("show", "");
        }), 3e3);
    }
 //** doubleSlider */
        $(function() {
        var doubleSlideThumbs = new Swiper(".objectSlider__thumbs", {
            spaceBetween: 8,
            slidesPerView: 4,
            freeMode: true,
            watchSlidesVisibility: true,
            watchSlidesProgress: true
        });
        var doubleSlideMain = new Swiper(".objectSlider__main", {
            spaceBetween: 15,
            slidesPerView: 1,
            centeredSlides: true,
            thumbs: {
                swiper: doubleSlideThumbs
            },
            keyboard: {
                enabled: true,
                onlyInViewport: true
            }
        });
    }());
    $(".object-actions__btn--fullscreen").click((function() {
        toggleFullScreen();
    }));
    $(".object-actions__btn--copy").click((function() {
        copyUrl();
    }));
    var body = document.querySelector("body"), filterOpener = document.querySelector(".ctrl-section__opener"), filterCloser = document.querySelector(".ctrl-section__closer"), btn = document.querySelector(".menu-opener");
    btn.addEventListener("click", (function() {
        body.classList.toggle("menu-opened");
    }));
    if ($(".ctrl-section").length > 0) {
        filterOpener.addEventListener("click", (function() {
            body.classList.add("filter-opened");
        }));
        filterCloser.addEventListener("click", (function() {
            body.classList.remove("filter-opened");
        }));
    }
    /**
   *  uni acc
   */    (function() {
        $(".acrd .acrd__content").hide();
        $(".acrd .acrd__option--opened .acrd__content").show();
        $(".acrd .acrd__opener").click((function() {
            if ($(this).parent().hasClass("acrd__option--opened")) {
                $(this).parent().removeClass("acrd__option--opened").children(".acrd__content").slideUp(300);
                $(this).siblings(".acrd__content").slideUp(300);
            } else {
                $(this).parent().addClass("acrd__option--opened");
                $(this).siblings(".acrd__content").slideDown(300);
            }
            return false;
        }));
    })();
    $((function() {
        $(".tabs-nav a").click((function() {
            $(".tabs-nav li").removeClass("active");
            $(this).parent().addClass("active");
            var currentTab = $(this).attr("href");
            $(".tabs-content li").hide();
            $(currentTab).show();
            return false;
        }));
    }));
    //** years Range Slider */
    //** Api: https://api.jqueryui.com/1.12/slider/  */
        $(function() {
        if (!$("body").find("#range-slider").length) return;
        var $min_input = $(".range-lbl__input--min"), $max_input = $(".range-lbl__input--max"), yearSliderInitialMin = -4e3, yearSliderInitialMax = 2e3, prevStart = 0, prevFinish = 0, $yearSlider = $("#range-slider").slider({
            animate: "slow",
            values: [ yearSliderInitialMin, yearSliderInitialMax ],
            range: true,
            min: yearSliderInitialMin,
            max: yearSliderInitialMax,
            step: 1,
            create: function create() {
                yearSliderAdjust($(this).slider("values", 0), $(this).slider("values", 1));
            },
            slide: function slide(event, ui) {
                yearSliderAdjust(ui.values[0], ui.values[1]);
            }
        });
        /**
     * Change min/max value
     * on input/range
     * @param {min} number - set startValue
     * @param {max} number - set finishValue
     */        function yearSliderAdjust(min, max) {
            var _yearsValidateScope = yearsValidateScope(min, max), _yearsValidateScope2 = _slicedToArray(_yearsValidateScope, 2), minVal = _yearsValidateScope2[0], maxVal = _yearsValidateScope2[1];
            prevStart = minVal;
            prevFinish = maxVal;
            $(".range-lbl__input--min").val(minVal);
            $(".range-lbl__input--max").val(maxVal);
            $(".filterRange__inp").val([ minVal, maxVal ]);
            if ($yearSlider) {
                $yearSlider.slider("values", [ minVal, maxVal ]);
            }
        }
        $min_input.change((function(e) {
            var minValueChanged = Number(e.target.value), maxValue = $yearSlider.slider("values", 1);
            yearSliderAdjust(minValueChanged, maxValue);
        }));
        $max_input.change((function(e) {
            var maxValueChanged = Number(e.target.value), minValue = $yearSlider.slider("values", 0);
            yearSliderAdjust(minValue, maxValueChanged);
        }));
        function yearsValidateScope(firstValue, secondValue) {
            var res = [];
            var minVal = Math.min(Math.max(firstValue, yearSliderInitialMin), yearSliderInitialMax);
            var maxVal = Math.min(Math.max(secondValue, yearSliderInitialMin), yearSliderInitialMax);
            if (minVal == prevStart) {
                maxVal = Math.max(minVal, maxVal);
            }
            if (maxVal == prevFinish) {
                minVal = Math.min(minVal, maxVal);
            }
            res.push(minVal, maxVal);
            return res;
        }
    }());
})(jQuery);

document.addEventListener("DOMContentLoaded", (function() {}));