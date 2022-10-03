@extends('layouts.main')
@section('css')
    @if(!empty($gallery))
        <link rel="stylesheet" href="{{asset('css/swiper.min.css')}}">
    @endif
@endsection
@section('content')
<div vocab="http://schema.org/" typeof="ImageObject">
    @if(!empty($gallery))
        <input type="hidden" name="gallery_loaded" value="no">
        <section class="visual-carousel">
            <div class="container-fluid visual-carousel__cont">
                <div class="swiper-container">
                    <div class="swiper-wrapper">
                        @if(isset($result->representations))
                            @php $count = 1 @endphp
                            @foreach($result->representations as $representation)
                                <div class="swiper-slide">
                                    <img
                                            src="{{media_url($representation->media, 'large')}}"
                                            alt="{{$result->name.' '.$count}}" class="img-zoom-gallery"
                                            data-id="{{$count++}}"
                                            property="contentUrl"
                                            data-src="{{str_replace(array('storage','https://uma.lvivcenter.org'),array('media',''),media_url($representation->media, 'tilepic'))}}"
                                            data-width="{{$representation->media['tilepic']['WIDTH']}}"
                                            data-height="{{$representation->media['tilepic']['HEIGHT']}}"
                                            data-layers="{{$representation->media['tilepic']['PROPERTIES']['layers']}}"
                                            data-bitdepth="{{$representation->media['tilepic']['PROPERTIES']['bitdepth']}}"
                                            data-quality="{{$representation->media['tilepic']['PROPERTIES']['quality']}}"
                                            data-size="{{$representation->media['tilepic']['PROPERTIES']['tile_width']}}"
                                    >
                                </div>
                                @break
                            @endforeach
                        @endif
                        @php $count_tile = 1; $max = count($gallery); @endphp
                        @foreach($gallery as $image)
                            <div class="swiper-slide gallery-slider">
                                <img
                                        id="gallery-image-{{$count_tile}}"
                                        src="{{$image['image']}}"
                                        alt="{{$result->name.' '.$count}}"
                                        class="img-zoom-gallery {{$count_tile++ == $max ? 'last-slide-image' : ''}}"
                                        data-id="{{$count++}}"
                                        data-src="{{str_replace(array('storage','https://uma.lvivcenter.org'),array('media',''),$image['tile'])}}"
                                        data-width="{{$image['tilepic']['WIDTH']}}"
                                        data-height="{{$image['tilepic']['HEIGHT']}}"
                                        data-layers="{{$image['tilepic']['PROPERTIES']['layers']}}"
                                        data-bitdepth="{{$image['tilepic']['PROPERTIES']['bitdepth']}}"
                                        data-quality="{{$image['tilepic']['PROPERTIES']['quality']}}"
                                        data-size="{{$image['tilepic']['PROPERTIES']['tile_width']}}"
                                >
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-button-prev visual-carousel__btn icon-arrow-r2"></div>
                    <div class="swiper-button-next visual-carousel__btn icon-arrow-r2"></div>
                </div>
{{--                <div class="tile-img">--}}
{{--                    <div class="tile-bar">--}}
{{--                        <div id="tileviewerControlZoomOut" class="tile-minus tileviewerControlZoomOut">--}}
{{--                            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 20" style="enable-background:new 0 0 46 20;" xml:space="preserve">--}}
{{--                                                <rect x="13" y="9" class="st0" width="20" height="2"/>--}}
{{--                                            </svg>--}}
{{--                        </div>--}}
{{--                        <div id="tileviewerControlZoomIn" class="tile-plus tileviewerControlZoomIn">--}}
{{--                            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 30" style="enable-background:new 0 0 46 30;" xml:space="preserve">--}}
{{--                                                <polygon class="st0" points="33,14 24,14 24,5 22,5 22,14 13,14 13,16 22,16 22,25 24,25 24,16 33,16 "/>--}}
{{--                                            </svg>--}}
{{--                        </div>--}}
{{--                        <div class="tile-close" data-type="gallery">--}}
{{--                            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 30" style="enable-background:new 0 0 46 30;" xml:space="preserve">--}}
{{--                                                <polygon class="st0" points="32.19,22.78 24.41,15 32.19,7.22 30.78,5.81 23,13.58 15.22,5.81 13.81,7.22 21.59,15 13.81,22.78 15.22,24.19 23,16.41 30.78,24.19 "/>--}}
{{--                                            </svg>--}}
{{--                        </div>--}}
{{--                        <div class="tile-prev" data-id="{{$count}}">--}}
{{--                            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 20" style="enable-background:new 0 0 46 20;" xml:space="preserve">--}}
{{--                                <polygon class="st0" points="34,9 18,9 18,7 12,10.01 18,13 18,11 34,11 "/>--}}
{{--                            </svg>--}}
{{--                        </div>--}}
{{--                        <div class="tile-next" data-id="{{$count}}">--}}
{{--                            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 20" style="enable-background:new 0 0 46 20;" xml:space="preserve">--}}
{{--                                                <polygon class="st0" points="12,11 28,11 28,13 34,9.99 28,7 28,9 12,9 "/>--}}
{{--                                            </svg>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
                <div class="tiles-list">
                    @php $count = 1 @endphp
                    @if(isset($result->representations))
                        @foreach($result->representations as $representation)
                            @if(!empty($representation->media['tilepic']))
                                <div class="tile-img tile-img-{{$count}}"
                                     data-src="{{str_replace(array('storage','https://uma.lvivcenter.org'),array('media',''),media_url($representation->media, 'tilepic'))}}"
                                     data-width="{{$representation->media['tilepic']['WIDTH']}}"
                                     data-height="{{$representation->media['tilepic']['HEIGHT']}}"
                                     data-layers="{{$representation->media['tilepic']['PROPERTIES']['layers']}}"
                                     data-bitdepth="{{$representation->media['tilepic']['PROPERTIES']['bitdepth']}}"
                                     data-quality="{{$representation->media['tilepic']['PROPERTIES']['quality']}}"
                                     data-size="{{$representation->media['tilepic']['PROPERTIES']['tile_width']}}"
                                >
                                    <div class="tile-bar">
                                        <div id="tileviewerControlZoomOut" class="tile-minus tileviewerControlZoomOut">
                                            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 20" style="enable-background:new 0 0 46 20;" xml:space="preserve">
                                                <rect x="13" y="9" class="st0" width="20" height="2"/>
                                            </svg>
                                        </div>
                                        <div id="tileviewerControlZoomIn" class="tile-plus tileviewerControlZoomIn">
                                            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 30" style="enable-background:new 0 0 46 30;" xml:space="preserve">
                                                <polygon class="st0" points="33,14 24,14 24,5 22,5 22,14 13,14 13,16 22,16 22,25 24,25 24,16 33,16 "/>
                                            </svg>
                                        </div>
                                        <div class="tile-close">
                                            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 30" style="enable-background:new 0 0 46 30;" xml:space="preserve">
                                                <polygon class="st0" points="32.19,22.78 24.41,15 32.19,7.22 30.78,5.81 23,13.58 15.22,5.81 13.81,7.22 21.59,15 13.81,22.78 15.22,24.19 23,16.41 30.78,24.19 "/>
                                            </svg>
                                        </div>
                                        <div class="tile-prev" data-id="{{$count}}">
                                            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 20" style="enable-background:new 0 0 46 20;" xml:space="preserve">
                                                <polygon class="st0" points="34,9 18,9 18,7 12,10.01 18,13 18,11 34,11 "/>
                                            </svg>
                                        </div>
                                        <div class="tile-next" data-id="{{$count}}">
                                            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 20" style="enable-background:new 0 0 46 20;" xml:space="preserve">
                                                <polygon class="st0" points="12,11 28,11 28,13 34,9.99 28,7 28,9 12,9 "/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @php $count++ @endphp
                            @break
                        @endforeach
                    @endif
                    @foreach($gallery as $image)
                        @if(!empty($image['tile']))
                            <div class="tile-img tile-img-{{$count}}"
                                 data-src="{{str_replace(array('storage','https://uma.lvivcenter.org'),array('media',''),$image['tile'])}}"
                                 data-width="{{$image['tilepic']['WIDTH']}}"
                                 data-height="{{$image['tilepic']['HEIGHT']}}"
                                 data-layers="{{$image['tilepic']['PROPERTIES']['layers']}}"
                                 data-bitdepth="{{$image['tilepic']['PROPERTIES']['bitdepth']}}"
                                 data-quality="{{$image['tilepic']['PROPERTIES']['quality']}}"
                                 data-size="{{$image['tilepic']['PROPERTIES']['tile_width']}}"
                            >
                                <div class="tile-bar">
                                    <div id="tileviewerControlZoomOut" class="tile-minus tileviewerControlZoomOut">
                                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 20" style="enable-background:new 0 0 46 20;" xml:space="preserve">
                                            <rect x="13" y="9" class="st0" width="20" height="2"/>
                                        </svg>
                                    </div>
                                    <div id="tileviewerControlZoomIn" class="tile-plus tileviewerControlZoomIn">
                                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 30" style="enable-background:new 0 0 46 30;" xml:space="preserve">
                                            <polygon class="st0" points="33,14 24,14 24,5 22,5 22,14 13,14 13,16 22,16 22,25 24,25 24,16 33,16 "/>
                                        </svg>
                                    </div>
                                    <div class="tile-close">
                                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 30" style="enable-background:new 0 0 46 30;" xml:space="preserve">
                                            <polygon class="st0" points="32.19,22.78 24.41,15 32.19,7.22 30.78,5.81 23,13.58 15.22,5.81 13.81,7.22 21.59,15 13.81,22.78 15.22,24.19 23,16.41 30.78,24.19 "/>
                                        </svg>
                                    </div>
                                    <div class="tile-prev" data-id="{{$count}}">
                                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 20" style="enable-background:new 0 0 46 20;" xml:space="preserve">
                                                <polygon class="st0" points="34,9 18,9 18,7 12,10.01 18,13 18,11 34,11 "/>
                                            </svg>
                                    </div>
                                    <div class="tile-next" data-id="{{$count}}">
                                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 20" style="enable-background:new 0 0 46 20;" xml:space="preserve">
                                                <polygon class="st0" points="12,11 28,11 28,13 34,9.99 28,7 28,9 12,9 "/>
                                            </svg>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @php $count++ @endphp
                    @endforeach
                </div>
            </div>
            <div class="container visual-carousel__content">
                <div class="row">
                    <div class="col-md-8 offset-lg-3 offset-md-4">
                        <h1 class="visual-exposition__ttl">
                            {{$result->name}}
                        </h1>
                    </div>
                </div>
            </div>
        </section>
    @else
        <section class="visual-exposition">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-3">
                    <div class="visual-exposition__cont">
                        <div class="visual-exposition__sample">
                            @if(isset($result->representations))
                                @foreach($result->representations as $representation)
{{--                                    @if($result->id != 567)--}}
{{--                                        <img src="{{media_url($representation->media, 'large')}}" alt="img" data-action="zoom" property="contentUrl">--}}
{{--                                    @else--}}
                                        <img src="{{media_url($representation->media, 'large')}}" alt="img" class="img-zoom" property="contentUrl">
                                        @if(!empty($representation->media['tilepic']))
                                            <div class="tile-img"
                                                 data-src="{{str_replace(array('storage','https://uma.lvivcenter.org'),array('media',''),media_url($representation->media, 'tilepic'))}}"
                                                 data-width="{{$representation->media['tilepic']['WIDTH']}}"
                                                 data-height="{{$representation->media['tilepic']['HEIGHT']}}"
                                                 data-layers="{{$representation->media['tilepic']['PROPERTIES']['layers']}}"
                                                 data-bitdepth="{{$representation->media['tilepic']['PROPERTIES']['bitdepth']}}"
                                                 data-quality="{{$representation->media['tilepic']['PROPERTIES']['quality']}}"
                                                 data-size="{{$representation->media['tilepic']['PROPERTIES']['tile_width']}}"
                                            >
                                                <div class="tile-bar">
                                                    <div id="tileviewerControlZoomOut" class="tile-minus tileviewerControlZoomOut">
                                                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 20" style="enable-background:new 0 0 46 20;" xml:space="preserve">
                                                            <rect x="13" y="9" class="st0" width="20" height="2"/>
                                                        </svg>
                                                    </div>
                                                    <div id="tileviewerControlZoomIn" class="tile-plus tileviewerControlZoomIn">
                                                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 30" style="enable-background:new 0 0 46 30;" xml:space="preserve">
                                                            <polygon class="st0" points="33,14 24,14 24,5 22,5 22,14 13,14 13,16 22,16 22,25 24,25 24,16 33,16 "/>
                                                        </svg>
                                                    </div>
                                                    <div class="tile-close">
                                                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 30" style="enable-background:new 0 0 46 30;" xml:space="preserve">
                                                            <polygon class="st0" points="32.19,22.78 24.41,15 32.19,7.22 30.78,5.81 23,13.58 15.22,5.81 13.81,7.22 21.59,15 13.81,22.78 15.22,24.19 23,16.41 30.78,24.19 "/>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
{{--                                    @endif--}}
                                    @break
                                @endforeach
                            @endif
                        </div>
                        <h1 class="visual-exposition__ttl" property="name">
                            {{$result->name}}
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
    <div class="ctn-wrapper">
    <main>
        <section class="explanation">
            <div class="container">
                <div class="row ">
                    <div class="col-md-4 col-lg-3">
                        <h1 class="explanation__ttl explanation__ttl--deco explanation__ttl--light">
                            @lang('translations.image')
                        </h1>
                    </div>
                    <div class="col-md-8">
                        <dl class="info-grid">
                            <dt>ID:</dt>
                            <dd>{{$result->id}}</dd>
                            @if(isset($result->places) && count($result->places))
                                <dt>@lang('translations.city')</dt>
                                <dd>
                                    @foreach($result->places as $place)
                                        <a href="{{url('photos/?places[1]=' . $place->id)}}" property="contentLocation">
                                            {{$place->name}}</a>
                                    @endforeach
                                </dd>
                            @endif
                            @if((isset($result->dates_from_to) && !empty($result->dates_from_to)) || (isset($result->date_text) && !empty($result->date_text)))
                                <dt>@lang('translations.date'):</dt>
                                <dd>
                                    @if(isset($result->dates_from_to) && !empty($result->dates_from_to))
                                        {{simplify_date($result->dates_from_to)}}
                                    @elseif(isset($result->date_text) && !empty($result->date_text))
                                        {{simplify_date($result->date_text)}}
                                    @endif
                                </dd>
                            @endif
                            @if(isset($result->technique) && !empty($result->technique))
                                <dt>@lang('translations.technique'):</dt>
                                <dd>
                                    {{$result->technique}}
                                </dd>
                            @endif
                            @if(isset($result->text_dim) && !empty($result->text_dim))
                                <dt>@lang('translations.text_dim'):</dt>
                                <dd>
                                    {{$result->text_dim}}
                                </dd>
                            @endif
                            @if(isset($result->creator) && !empty($result->creator))
                                <dt>@lang('translations.author_text')</dt>
                                <dd>
                                    <a href="{{url('photos/?entities[1]=' . $result->creator_id)}}">
                                        {{$result->creator}}</a>
                                </dd>
                            @endif
                            @if(isset($result->collections) && count($result->collections))
                                <dt>@lang('translations.collection')</dt>
                                <dd>
                                    @foreach($result->collections as $collection)
                                        <a href="{{url('collections/' . $collection->id.'/photos')}}">
                                            {{$collection->name}}</a>
                                    @endforeach
                                </dd>
                            @endif
                            @if(isset($result->rights) && !empty($result->rights))
                                <dt>@lang('translations.copyright')</dt>
                                <dd property="description">
                                    {!! $result->rights !!}
                                </dd>
                            @endif
                            @if(isset($result->publisher) && !empty($result->publisher))
                                <dt>@lang('translations.publisher')</dt>
                                <dd property="description">
                                    {!! $result->publisher !!}
                                </dd>
                            @endif
                            @if(isset($result->work_description) && !empty($result->work_description))
                                <dt>@lang('translations.description')</dt>
                                <dd property="description">
                                    {!! $result->work_description !!}
                                </dd>
                            @endif
                            @if(isset($result->tags) && !empty($result->tags))
                                <dt>@lang('translations.tags'):</dt>
                                <dd>
                                    {{$result->tags}}
                                </dd>
                            @endif
                            @if(isset($result->categories) && count($result->categories))
                                <dt>@lang('translations.category'):</dt>
                                <dd>
                                    @foreach($result->categories as $category)
                                        {{$category->name}}
                                    @endforeach
                                </dd>
                            @endif
                        </dl>
                    </div>
                    <div class="col-lg-1">
                        @include('includes.share')
{{--                        <a href="#" class="icon-share btn-share explanation__share">--}}
{{--                            <span class="share-after"></span>--}}
{{--                        </a>--}}
                    </div>
                </div>
            </div>
        </section>
        @if(!empty($related))
            <section class="posts">
                <div class="container">
                    <h2 class="section-title">
                        @lang('translations.linked_objects')
                    </h2>
                    <div class="row">
                        @php $countRelated = 0; @endphp
                        @foreach($related as $object)
                            @if($countRelated++ > 3) @break @endif
                                @php switch ($object->object_type_id){
                                case '25': $slug = 'videos'; break;
                                default: $slug = 'photos';
                            } @endphp
                            <article class="col-md-6 col-xl-3 post-itm">
                                <a href="{{url($slug . '/' . $object->id)}}" class="post-itm__link">
                                    <figure class="post-itm__cont">
                                            <span class="post-itm__pict">
                                                @if(isset($object->representations))
                                                    <img src="{{media_url($object->representations->media, 'medium')}}" alt="image">
                                                @endif
                                            </span>
                                            <figcaption class="post-itm__capt">
                                                <h3 class="post-itm__ttl">{{isset($object->name) && !empty($object->name) ? $object->name : $object->id}}</h3>
                                                <ul class="data-list list-style">
                                                    <li class="data-list__i">
                                                        ID: <span class="data-list__id">{{$object->id}}</span>
                                                    </li>
                                                    @if(!empty($object->places))
                                                        @foreach($object->places as $place)
                                                            @if(isset($place->name))
                                                                <li class="data-list__i">
                                                                    @lang('translations.city'): <span class="data-list__id">{{$place->name}}</span>
                                                                </li>
                                                            @endif
                                                            @break;
                                                        @endforeach
                                                    @endif
                                                    @if(!empty($object->year))
                                                        <li class="data-list__i">
                                                            @lang('translations.date'): <span class="data-list__year">{{$object->year}}</span>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </figcaption>
                                    </figure>
                                </a>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </main>
</div>
</div>
@endsection
@section('js-libs')
    <script src="{{asset('js/swiper.min.js')}}"></script>
    <script src="{{asset('js/circular-slider.js')}}"></script>
@endsection