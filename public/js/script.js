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

$(document).ready(function () {

    let tileContainer = $('.tile-img');
    $('.img-zoom').click(function () {
        tileContainer.addClass('active');
        let tileSrc = tileContainer.attr('data-src');
        let tileWidth = tileContainer.attr('data-width');
        let tileHeight = tileContainer.attr('data-height');
        let tileSize = tileContainer.attr('data-size');
        let tileLevels = tileContainer.attr('data-layers');
        let tileBitdepth = tileContainer.attr('data-bitdepth');
        let tileQuality = tileContainer.attr('data-quality');

        tileContainer.tileviewer({
            "src": "https://lvivart-admin.sitegist.com/viewers/apps/tilepic.php?p="+tileSrc+"&t=",
            "toolbar": ['zoomIn', 'zoomOut', 'expand'],
            "showAnnotationTools" : false,
            "info": {"width": tileWidth, "height": tileHeight, "tilesize": tileSize, "levels": tileLevels}
        });
    });

    $('.tile-close').click(function () {
        $('.embed3d-popup').removeClass('active');
        let type = $(this).attr('data-type');
        if(type == 'gallery'){
            tileContainer.find('canvas').remove();
            tileContainer.removeClass('tileviewer');
        }
        tileContainer.removeClass('active');
    });

    $('.img-zoom-gallery').click(function () {
        let id = $(this).attr('data-id');
        let thisTileContainer = $('.tile-img-' + id);
        thisTileContainer.addClass('active');
        let tileSrc = thisTileContainer.attr('data-src');
        let tileWidth = thisTileContainer.attr('data-width');
        let tileHeight = thisTileContainer.attr('data-height');
        let tileSize = thisTileContainer.attr('data-size');
        let tileLevels = thisTileContainer.attr('data-layers');
        let tileBitdepth = thisTileContainer.attr('data-bitdepth');
        let tileQuality = thisTileContainer.attr('data-quality');

        thisTileContainer.tileviewer({
            "src": "https://lvivart-admin.sitegist.com/viewers/apps/tilepic.php?p="+tileSrc+"&t=",
            "toolbar": ['zoomIn', 'zoomOut', 'expand'],
            "showAnnotationTools" : false,
            "info": {"width": tileWidth, "height": tileHeight, "tilesize": tileSize, "levels": tileLevels}
        });
    });

    loadTileGallery();

    function loadTileGallery(){
        let loaded = $('[name=gallery_loaded]');
        if(loaded.attr('value') == 'no'){
            $('body').addClass('gallery-loaded');
            let gallery_last_slide = $('.img-zoom-gallery.last-slide-image');
            if(gallery_last_slide.length > 0){
                gallery_last_slide.trigger('click');
                let count = parseInt(gallery_last_slide.attr('data-id'));
                setTimeout(function(){
                    $('.tile-img-' + count + ' .tile-prev').trigger('click');
                    clickToTileButton(count);
                    // $('.tile-img-' + gallery_last_slide.attr('data-id') + ' .tile-close').trigger('click');
                }, 100);
            }
            loaded.attr('value', 'yes');
        }
    }

    function clickToTileButton(count){
        if(count == 1){
            $('body').removeClass('gallery-loaded');
            return false;
        }
        setTimeout(function(){
            $('.tile-img-' + (count - 1) + ' .tile-prev').trigger('click');
            clickToTileButton(count - 1);
        }, 100);
    }

    $('.tile-prev').click(function () {
        $('.tile-img').removeClass('active');
        // $('.tile-img').removeClass('tileviewer');
        // $('.tile-img').each(function(index) {
        //     $(this).find('canvas').remove();
        // });
        let id = parseInt($(this).attr('data-id'));
        let thisTileContainer = $('.tile-img-' + (id - 1));
        thisTileContainer.addClass('active');
        let tileSrc = thisTileContainer.attr('data-src');
        let tileWidth = thisTileContainer.attr('data-width');
        let tileHeight = thisTileContainer.attr('data-height');
        let tileSize = thisTileContainer.attr('data-size');
        let tileLevels = thisTileContainer.attr('data-layers');
        let tileBitdepth = thisTileContainer.attr('data-bitdepth');
        let tileQuality = thisTileContainer.attr('data-quality');

        thisTileContainer.tileviewer({
            "src": "https://lvivart-admin.sitegist.com/viewers/apps/tilepic.php?p="+tileSrc+"&t=",
            "toolbar": ['zoomIn', 'zoomOut', 'expand'],
            "showAnnotationTools" : false,
            "info": {"width": tileWidth, "height": tileHeight, "tilesize": tileSize, "levels": tileLevels}
        });
        $('.swiper-button-prev').trigger('click');
    });

    $('.tile-next').click(function () {
        $('.tile-img').removeClass('active');
        let id = parseInt($(this).attr('data-id'));
        let thisTileContainer = $('.tile-img-' + (id + 1));
        thisTileContainer.addClass('active');
        let tileSrc = thisTileContainer.attr('data-src');
        let tileWidth = thisTileContainer.attr('data-width');
        let tileHeight = thisTileContainer.attr('data-height');
        let tileSize = thisTileContainer.attr('data-size');
        let tileLevels = thisTileContainer.attr('data-layers');
        let tileBitdepth = thisTileContainer.attr('data-bitdepth');
        let tileQuality = thisTileContainer.attr('data-quality');

        thisTileContainer.tileviewer({
            "src": "https://lvivart-admin.sitegist.com/viewers/apps/tilepic.php?p="+tileSrc+"&t=",
            "toolbar": ['zoomIn', 'zoomOut', 'expand'],
            "showAnnotationTools" : false,
            "info": {"width": tileWidth, "height": tileHeight, "tilesize": tileSize, "levels": tileLevels}
        });
        $('.swiper-button-next').trigger('click');
    });

    $('.object-visual__mark,.embed3d-open').click(function (e) {
        $('.embed3d-popup').addClass('active');
        e.preventDefault();
    });

    $('.popup-close').click(function () {
        $('.embed3d-popup').removeClass('active');
    });

    $('.msf-list__name').click(function () {
        $(this).parent('.msf-list__el').toggleClass('mobile');
    });

    let doubleSlideMain = false;
    if($('.objectSlider__thumbs').length) {
        var doubleSlideThumbs = new Swiper(".objectSlider__thumbs", {
            spaceBetween: 8,
            // slidesPerView: 6,
            slidesPerView: 'auto',
            // centeredSlides: true,
            centerInsufficientSlides: 1,
            // centeredSlidesBounds: 1,
            watchSlidesVisibility: true,
            watchSlidesProgress: true,
            // centerInsufficientSlides: true,
            slideToClickedSlide: true,
            observer: true,
            observeParents: true,
            // loop: 1,
            // centerInsufficientSlides: 1,
            // centeredSlidesBounds: 1,
            // freeMode: true,
            // watchSlidesVisibility: true,
            // watchSlidesProgress: true
        });
        doubleSlideMain = new Swiper(".objectSlider__main", {
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
    }

    /* zoomify */
    let viewer = false;

    $('.zoomify-close').click(function(){
        $('.zoomify-container').removeClass('active');
        viewer.close();
    });

    $('.objectSlider__thumbs .swiper-slide').click(function(){
        let id = $(this).find('img').attr('data-id');
        if(id.length){
            $('[name=zoomify-active]').val(id);
        }
    });

    $('.zoomify-prev').click(function(){
        let id = parseInt($('[name=zoomify-active]').val());
        let prev = id == 1 ? parseInt($('[name=zoomify-sum]').val()) : (id - 1);
        $('[name=zoomify-active]').val(prev);
        $('.objectSlider__main img').each(function(){
            if(parseInt($(this).attr('data-id')) == prev){
                let src = $(this).attr('data-src');
                let width = parseInt($(this).attr('data-width'));
                let height = parseInt($(this).attr('data-height'));
                init_zoomify(src, width, height);
                doubleSlideMain.slidePrev();
            }
        });
    });

    $('.zoomify-next').click(function(){
        let id = parseInt($('[name=zoomify-active]').val());
        let next = id == parseInt($('[name=zoomify-sum]').val()) ? 1 : (id + 1);
        $('[name=zoomify-active]').val(next);
        $('.objectSlider__main img').each(function(){
            if(parseInt($(this).attr('data-id')) == next){
                let src = $(this).attr('data-src');
                let width = parseInt($(this).attr('data-width'));
                let height = parseInt($(this).attr('data-height'));
                init_zoomify(src, width, height);
                doubleSlideMain.slideNext();
            }
        });
    });

    $('.img-zoomify').click(function(){
        let src = $(this).attr('data-src');
        let width = parseInt($(this).attr('data-width'));
        let height = parseInt($(this).attr('data-height'));
        if(src.length){
            $('.zoomify-container').addClass('active');
            init_zoomify(src, width, height);
        }
    });

    function init_zoomify(src, width, height){
        if(viewer){
            viewer.close();
            viewer.open([
                {
                    type: "zoomifytileservice",
                    width: width,
                    height: height,
                    tilesUrl: src
                }
            ]);
        }else{
            viewer = OpenSeadragon({
                debugMode: false,
                id: 'zoomify',
                prefixUrl: "../../img/zoomify/",
                tileSources: [
                    {
                        type: "zoomifytileservice",
                        width: width,
                        height: height,
                        tilesUrl: src
                    }
                ],
                showNavigator: false
            });
        }
    }

    $('.zoomify-open').click(function(){
        let src = $(this).attr('data-src');
        //document.getElementById('zoomify-container').querySelector('.openseadragon-canvas').focus();
        $('.zoomify-container').addClass('active');
        if(src.length){
            if(viewer){
                viewer.open([
                    {
                        type: "zoomifytileservice",
                        width: 5000,
                        height: 8000,
                        tilesUrl: src
                    }
                ]);
            }else{
                viewer = OpenSeadragon({
                    debugMode: false,
                    id: 'zoomify',
                    prefixUrl: "../../img/zoomify/",
                    tileSources: [
                        {
                            type: "zoomifytileservice",
                            width: 5000,
                            height: 8000,
                            tilesUrl: src
                        }
                    ],
                    showNavigator: false
                });
            }
        }
    });
    /* end of zoomify */

    // if($('.zoomify-item').length) {
    //     $('.zoomify-item').each(function(){
    //         let viewer = OpenSeadragon({
    //             debugMode: false,
    //             id: $(this).attr('id'),
    //             prefixUrl: "../../img/zoomify/",
    //             tileSources: [
    //                 {
    //                     type: "zoomifytileservice",
    //                     width: 5000,
    //                     height: 5000,
    //                     //tilesUrl: "https://lvivart-admin.sitegist.com/media/collectiveaccess/images/0/65753_ca_object_representations_media_7_original_zdata/"
    //                     tilesUrl: $(this).attr('data-src')
    //                 }
    //             ],
    //             //navigatorId: 'zoomify-toolbar',
    //             showNavigator: false
    //         });
    //     });
    // }
});

$(window).on('load', function (){
    let url = location.protocol + '//' + location.host + location.pathname;
    let lang = location.pathname.split('/')[1];
    let domain = location.protocol + '//' + location.host + '/' + document.documentElement.lang;

    let qs_typing_timer;
    let qs_done_typing_interval = 200;

    $('[name=quick-search]').on('keyup paste', function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        clearTimeout(qs_typing_timer);
        let qs_lang = $('html').attr('lang');
        let qs_this_item = $(this);
        //тут куди вписувати результат чи повідомлення
        let qs_results_wrapper = $('.srch-sgs');
        let qs_value = qs_this_item.val().toLowerCase();
        let qs_string_more = qs_lang == 'uk' ? 'Введіть більше для результату' : 'Enter more for results';
        let qs_string_no_results = qs_lang == 'uk' ? 'Нічого не знайдено' : 'No results';
        if(qs_value.length > 1){
            qs_typing_timer = setTimeout(function () {
                let action = domain + '/search-objects';
                $.ajax({
                    type: 'POST',
                    data: {value: qs_value, lang: qs_lang},
                    url: action,
                    success: function (data) {
                        if(data.length > 1) {
                            qs_results_wrapper.html(data);
                        }else{
                            qs_results_wrapper.html(qs_string_no_results);
                        }
                    },
                    error: function (errors) {
                        console.log(errors);
                    }
                });
            }, qs_done_typing_interval);
        }else{
            qs_results_wrapper.html(qs_string_more);
        }
    });
});

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
    function createElementFromHTML(htmlString) {
        let div = document.createElement('div');
        div.innerHTML = htmlString.trim();
        return div.firstChild;
    }

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

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let url = location.protocol + '//' + location.host + location.pathname;
        let lang = location.pathname.split('/')[1];
        let domain = location.protocol + '//' + location.host + '/' + document.documentElement.lang;

        function show_more_objects(count = 15){
            event.preventDefault();
            $('.cards-list').addClass('loading');
            let page_input = $('[name=load_page]');
            let pages_query = $('[name=pages_query]').val();
            let page = page_input.val();
            let pages = parseInt($('[name=pages_count]').val());

            let more = $('.more-post-item');
            let html = '';
            let action = domain + '/get-more-objects';
            let url_object = domain + '/object';

            if (page < pages) {
                $.ajax({
                    type: 'POST',
                    data: {
                        page: page,
                        count: count,
                        query_list: $('[name=query_list]').val(),
                        query_years: $('[name=query_years]').val(),
                        query_author: $('[name=query_author]').val(),
                        query_string: $('[name=query_string]').val(),
                        lang: lang,
                        query: pages_query
                    },
                    url: action,
                    success: function (data) {
                        let objects = JSON.parse(data);
                        let elements = [];
                        for (let i = (objects.length - 1); i >= 0; i--) {
                            html = createElementFromHTML('<li class="cards-list__card mb45px">' +
                                '       <a href="' + url_object + '/' + objects[i]['id'] + '-' + objects[i]['url'] + '" class="card">' +
                                '           <figure class="card__wrapper">' +
                                '               <div class="card__img mb15px">' +
                                '                   <img src="' + objects[i]['image'] + '" alt="">' +
                                '               </div>' +
                                '               <figcaption>' +
                                '                   <p class="card__ttl">' + objects[i]['name'] + '</p>' +
                                '                   <p class="card__subttl">' + objects[i]['author'] + '</p>' +
                                '               </figcaption>' +
                                '           </figure>' +
                                '       </a>' +
                                '    </li>');
                            elements.push(html);
                        }

                        page = parseInt(page) + 1;
                        page_input.val(page);
                        $('.pg-nav').html(rebuild_pagination(page, pages));

                        if(elements.length == 15) {
                            elements.push(
                                createElementFromHTML(
                                    '<div class="cards-list__card more-post-item">' + more.html() + '</div>'
                                )
                            );
                        }
                        more.remove();

                        let $elements = $(elements);

                        $cardsGrid.append($elements).masonry('appended', $elements, true);
                        setTimeout(function () {
                            $cardsGrid.masonry('reloadItems');
                            $cardsGrid.masonry('layout');
                        }, 600);
                    },
                    error: function (errors) {
                        console.log(errors);
                    }
                });

            } else {
                more.remove();
            }
            setTimeout(function () {
                $('.cards-list').removeClass('loading');
            }, 5000);
        }

        window.show_more_objects = show_more_objects;

        function rebuild_pagination(page = 1, pages){

            let first_link = page > 2 ? page - 2 : 1;
            let last_link = page > 2 ? page + 2 : 5;
            last_link = pages < last_link ? pages : last_link;

            let pages_query = $('[name=pages_query]').val();
            console.log(pages_query);

            let html = '<ul class="lisn pagination">';
            if(page > 1){
                html += '<li class="prev icon-arr-l"><a href="' + '?' + pages_query + '&page=' + (page - 1) + '"></a></li>';
            }
            if (page > 3) {
                html += '<li>';
                html += '   <a href="' + url + '?' + pages_query + '">1</a>';
                html += '</li>';
                html += '<li><span>...</span></li>';
            }
            for (let i = first_link; i <= last_link; i++) {
                html += i == page ? '<li class="active">' : '<li>';
                html += i == 1 ? '<a href="' + url + '?' + pages_query + '">1</a>' : '<a href="' + url + '?' + pages_query + '&page=' + i + '">' + i + '</a>';
                html += '</li>';
            }
            if ((page < pages - 2) && pages != 5) {
                html += '<li class="more">';
                html += '   <span>...</span>';
                html += '   <a href="' + url + '?' + pages_query + '&page=' + pages + '">' + pages + '</a>';
                html += '</li>';
            }
            if(page < pages){
                html += '<li class="next icon-arr-r"><a href="' + '?' + pages_query + '&page=' + (page + 1) + '"></a></li>';
            }
            html += '</ul>';

            let new_url = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + pages_query + '&page=' + page;
            window.history.pushState({path: new_url}, '', new_url);

            return html;
        }
    }());
 //** Search part */ 
        $(function() {
        if ($(window).width() < 575) {
            let lang_search = $('html').attr('lang') == 'uk' ? 'Шукати за ...' : 'Search by keywords...';
            $(".msf-query__inp").attr("placeholder", lang_search);
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
            $('.msf-reset').show();
        }
 //** change form chkb block + remove tag */
        $(".tags-list__tag").click((function(e) {
            var id = $(this).attr("data-filter-id");
            uncheckTag(id);
            $(this).remove();
        }));
 //** tags CHANGE evt */
        let timer;
        $(".chkb-block__ctrl").change((function(e) {
            var id = e.target.id;
            var val = $("#".concat(id)).next(".chkb-block__lbl").text();
            var attr = $("#".concat(id)).next(".chkb-block__lbl").attr('for');
            var status = $("#".concat(id)).is(":checked");
            let present = false;
            clearTimeout(timer);
            timer = setTimeout(function(){
                $('#mainSearchForm').submit();
            }, 1000);
            // $('.tags-list li span').each(function(){
            //     if($(this).attr('data-attr') == attr){
            //         present = true;
            //     }
            // });
            status ? addTagToTagList(id, val) : removeTagFromTagList(id);
            // console.log(present);
            // if(!present) {
            //     let li = '<li class="tags-list__tag"><span class="tag" data-attr="' + attr + '">' + val + '</span></li>';
            //     $('.tags-list').html($('.tags-list').html() + li);
            // }
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
    $(".object-actions__btn--fullscreen").click((function() {
        // toggleFullScreen();
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
        let year_min = $('#range-slider').attr('data-min');
        let year_current_min = $('#range-slider').attr('data-current-min');
            year_current_min = year_current_min != undefined ? year_current_min : year_min;
        let year_max = $('#range-slider').attr('data-max');
        let year_current_max = $('#range-slider').attr('data-current-max');
            year_current_max = year_current_max != undefined ? year_current_max : year_max;
        var $min_input = $(".range-lbl__input--min"), $max_input = $(".range-lbl__input--max"), yearSliderInitialMin = parseInt(year_min), yearSliderInitialMax = parseInt(year_max), prevStart = 0, prevFinish = 0, $yearSlider = $("#range-slider").slider({
            animate: "slow",
            values: [ year_current_min, year_current_max ],
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