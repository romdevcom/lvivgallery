<div id="zoomify-container" class="zoomify-container @if(isset($zoomify['gallery'])) zoomify-slider @endif">
    <div class="zoomify-close">
        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 30" style="enable-background:new 0 0 46 30;" xml:space="preserve">
                <polygon class="st0" points="32.19,22.78 24.41,15 32.19,7.22 30.78,5.81 23,13.58 15.22,5.81 13.81,7.22 21.59,15 13.81,22.78 15.22,24.19 23,16.41 30.78,24.19 "/>
            </svg>
    </div>
    <div id="zoomify" class="zoomify-item"></div>
    @if(isset($zoomify['gallery']) && count($zoomify['gallery']))
        <input type="hidden" name="zoomify-active" value="1">
        <input type="hidden" name="zoomify-sum" value="{{count($zoomify['gallery']) + 1}}">
        <div class="zoomify-arrows">
            <div class="zoomify-prev">
                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 20" style="enable-background:new 0 0 46 20;" xml:space="preserve">
                    <polygon class="st0" points="34,9 18,9 18,7 12,10.01 18,13 18,11 34,11 "/>
                </svg>
            </div>
            <div class="zoomify-next">
                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 20" style="enable-background:new 0 0 46 20;" xml:space="preserve">
                    <polygon class="st0" points="12,11 28,11 28,13 34,9.99 28,7 28,9 12,9 "/>
                </svg>
            </div>
        </div>
    @endif
</div>
@php $count = 1 @endphp
<div class="objectSlider mb30px">
    <div class="swiper-container objectSlider__main">
        <ul class="swiper-wrapper">
            @if(isset($zoomify['main']))
                <li class="swiper-slide">
                    <img
                        data-id="{{$count}}"
                        @if($count++ == 1) itemprop="image" @endif
                        src="{{$zoomify['main']['image']}}"
                        data-src="{{$zoomify['main']['src']}}"
                        data-width="{{$zoomify['main']['width']}}"
                        data-height="{{$zoomify['main']['height']}}"
                        class="img-zoomify"
                        alt="{{$result->name.' '.$count}}"
                    >
                </li>
            @endif
            @if(isset($zoomify['gallery']) && count($zoomify['gallery']))
                @foreach($zoomify['gallery'] as $image)
                    <li class="swiper-slide">
                        <img
                            data-id="{{$count++}}"
                            src="{{$image['image']}}"
                            data-src="{{$image['src']}}"
                            data-width="{{$image['width']}}"
                            data-height="{{$image['height']}}"
                            class="img-zoomify"
                            alt="{{$result->name.' '.$count}}"
                        >
                    </li>
                @endforeach
            @endif
        </ul>
    </div>
    @if(isset($zoomify['gallery']) && count($zoomify['gallery']))
        @php $count = 1 @endphp
        <div class="swiper-container objectSlider__thumbs">
            <ul class="swiper-wrapper">
                @if(isset($zoomify['main']))
                    <li class="swiper-slide">
                        <img src="{{$zoomify['main']['image']}}" data-id="{{$count++}}" alt="">
                    </li>
                @endif
                @foreach($zoomify['gallery'] as $image)
                    <li class="swiper-slide">
                        <img src="{{$image['image']}}" data-id="{{$count++}}" alt="">
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>