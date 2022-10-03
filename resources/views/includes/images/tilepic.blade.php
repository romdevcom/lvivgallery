@if(isset($result->representations))
    @php $count = 1 @endphp
    <div class="objectSlider mb30px">
        <div class="swiper-container objectSlider__main">
            <ul class="swiper-wrapper">
                @foreach($result->representations as $representation)
                    <li class="swiper-slide">
                        <img
                                @if($count == 1) itemprop="image" @endif
                        src="{{media_url($representation->media, 'large')}}"
                                class="{{!empty($gallery) ? 'img-zoom-gallery' : 'img-zoom'}}"
                                alt="{{$result->name.' '.$count}}"
                                @if(!empty($gallery))
                                data-id="{{$count++}}"
                                property="contentUrl"
                                data-src="{{str_replace(array('storage','https://lvivart-admin.sitegist.com'),array('media',''),media_url($representation->media, 'tilepic'))}}"
                                data-width="{{$representation->media['tilepic']['WIDTH']}}"
                                data-height="{{$representation->media['tilepic']['HEIGHT']}}"
                                data-layers="{{$representation->media['tilepic']['PROPERTIES']['layers']}}"
                                data-bitdepth="{{$representation->media['tilepic']['PROPERTIES']['bitdepth']}}"
                                data-quality="{{$representation->media['tilepic']['PROPERTIES']['quality']}}"
                                data-size="{{$representation->media['tilepic']['PROPERTIES']['tile_width']}}"
                                @endif
                        >
                    </li>
                @endforeach
                @if(!empty($gallery))
                    @php $count_tile = 1; $max = count($gallery); @endphp
                    @foreach($gallery as $image)
                        <li class="swiper-slide {{$count_tile == $max ? 'last-slide' : ''}}">
                            <img
                                    src="{{$image['image']}}"
                                    class="img-zoom-gallery" alt="{{$result->name.' '.$count}}"
                                    @if(!empty($gallery))
                                    data-id="{{$count++}}"
                                    data-src="{{str_replace(array('storage','https://uma.lvivcenter.org'),array('media',''),$image['tile'])}}"
                                    data-width="{{$image['tilepic']['WIDTH']}}"
                                    data-height="{{$image['tilepic']['HEIGHT']}}"
                                    data-layers="{{$image['tilepic']['PROPERTIES']['layers']}}"
                                    data-bitdepth="{{$image['tilepic']['PROPERTIES']['bitdepth']}}"
                                    data-quality="{{$image['tilepic']['PROPERTIES']['quality']}}"
                                    data-size="{{$image['tilepic']['PROPERTIES']['tile_width']}}"
                                    @endif
                            >
                        </li>
                    @endforeach
                @endif
            </ul>
        </div>
        @if(!empty($gallery))
            <input type="hidden" name="gallery_loaded" value="no">
            <div class="swiper-container objectSlider__thumbs">
                <ul class="swiper-wrapper">
                    @foreach($result->representations as $representation)
                        <li class="swiper-slide">
                            <img src="{{media_url($representation->media, 'medium')}}" alt="">
                        </li>
                    @endforeach
                    @if(!empty($gallery))
                        @foreach($gallery as $image)
                            <li class="swiper-slide">
                                <img src="{{$image['image']}}" alt="">
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>
        @endif
    </div>
@endif