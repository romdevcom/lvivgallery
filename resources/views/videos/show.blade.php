@extends('layouts.main')
@section('content')
    <section class="visual-exposition">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-3">
                    <div class="visual-exposition__cont">
                        <div class="visual-exposition__sample visual-exposition__sample--video">
                            @if(isset($result->representations))
                                @foreach($result->representations as $representation)
                                    <video id="visual-exposition__video" width="730" height="410" preload="auto" controls controlsList="nodownload" poster="{{media_url($representation->media, 'large')}}">
                                        <source src="{{media_url($representation->media, 'h264_hi')}}" type="video/mp4">
                                        @lang('translations.video_error')
                                    </video>
                                    <canvas  width="700" id="video-convas"></canvas>
                                    @break
                                @endforeach
                            @endif
                        </div>
                        <h1 class="visual-exposition__ttl">
                            {{$result->name}}
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="ctn-wrapper">
        <main>
            <section class="explanation">
                <div class="container">
                    <div class="row ">
                        <div class="col-md-4 col-lg-3">
                            <h2 class="explanation__ttl explanation__ttl--deco explanation__ttl--light">
                                @lang('translations.videos_and_movies')
                            </h2>
                        </div>
                        <div class="col-md-8">
                            <dl class="info-grid">
                                <dt>ID:</dt>
                                <dd>{{$result->id}}</dd>
                                @if(isset($result->places))
                                    <dt>@lang('translations.city')</dt>
                                    <dd>
                                        @foreach($result->places as $place)
                                            <a href="{{url('photos/?places[1]=' . $place->id)}}">
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
                                @if(isset($result->movie_genre) && !empty($result->movie_genre))
                                    <dt>@lang('translations.genre'):</dt>
                                    <dd>
                                        {{$result->movie_genre}}
                                    </dd>
                                @endif
                                @if(isset($result->movie_issue) && !empty($result->movie_issue))
                                    <dt>@lang('translations.movie_issue'):</dt>
                                    <dd>
                                        {{$result->movie_issue}}
                                    </dd>
                                @endif
                                @if(isset($result->technique) && !empty($result->technique))
                                    <dt>@lang('translations.technique'):</dt>
                                    <dd>
                                        {{$result->technique}}
                                    </dd>
                                @endif
                                @if(isset($result->movie_duration) && !empty($result->movie_duration))
                                    <dt>@lang('translations.duration'):</dt>
                                    <dd>
                                        {{$result->movie_duration}}
                                    </dd>
                                @endif
                                @if(isset($result->colorType) && !empty($result->colorType))
                                    <dt>@lang('translations.colorType'):</dt>
                                    <dd>
                                        @lang('translations.'.str_replace(' ','_',$result->colorType))
                                    </dd>
                                @endif
                                @if(isset($result->movie_audio) && !empty($result->movie_audio))
                                    <dt>@lang('translations.movie_audio'):</dt>
                                    <dd>
                                        {{$result->movie_audio}}
                                    </dd>
                                @endif
                                @if(isset($result->movie_authors_text) && !empty($result->movie_authors_text))
                                    <dt>@lang('translations.author_text'):</dt>
                                    <dd>
                                        {{$result->movie_authors_text}}
                                    </dd>
                                @endif
                                @if(isset($result->movie_producer_text) && !empty($result->movie_producer_text))
                                    <dt>@lang('translations.producers'):</dt>
                                    <dd>
                                        {{$result->movie_producer_text}}
                                    </dd>
                                @endif
                                @if(isset($result->creator) && !empty($result->creator))
                                    <dt>@lang('translations.author_text')</dt>
                                    <dd>
                                        <a href="{{url('videos/?entities[1]=' . $result->creator_id)}}">
                                            {{$result->creator}}</a>
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
                                @if(isset($result->collections) && count($result->collections))
                                    <dt>@lang('translations.collection')</dt>
                                    <dd>
                                        @foreach($result->collections as $collection)
                                            <a href="{{url('collections/' . $collection->id.'/photos')}}">
                                                {{$collection->name}}</a>
                                        @endforeach
                                    </dd>
                                @endif
                                @if(isset($result->collection_call_number) && !empty($result->collection_call_number))
                                    <dt>@lang('translations.collection_call_number'):</dt>
                                    <dd>
                                        {{$result->collection_call_number}}
                                    </dd>
                                @endif
                                @if(isset($result->movie_copyright_text) && !empty($result->movie_copyright_text))
                                    <dt>@lang('translations.copyright'):</dt>
                                    <dd>
                                        {{$result->movie_copyright_text}}
                                    </dd>
                                @endif
                                @if(isset($result->description) && !empty($result->description))
                                    <dt>@lang('translations.description')</dt>
                                    <dd>
                                        {{strip_tags($result->description)}}
                                    </dd>
                                @endif
                                @if(isset($result->work_description) && !empty($result->work_description))
                                    <dt>@lang('translations.description')</dt>
                                    <dd property="description">
                                        {!! $result->work_description !!}
                                    </dd>
                                @endif
                                @if(isset($result->movie_language) && !empty($result->movie_language))
                                    <dt>@lang('translations.language'):</dt>
                                    <dd>
                                        {{$result->movie_language}}
                                    </dd>
                                @endif
                                @if(isset($result->tags) && !empty($result->tags))
                                    <dt>@lang('translations.tags'):</dt>
                                    <dd>
                                        {{$result->tags}}
                                    </dd>
                                @endif
                                @if(isset($result->text_dim) && !empty($result->text_dim))
                                    <dt>@lang('translations.text_dim'):</dt>
                                    <dd>
                                        {{$result->text_dim}}
                                    </dd>
                                @endif
                                @if(isset($result->categories))
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
                        </div>
                    </div>
                </div>
            </section>
            <section class="posts">
                <div class="container">
                    <h2 class="section-title">
                        @lang('translations.linked_objects')
                    </h2>
                    <div class="row">
                        @foreach($related as $object)
                            @php switch ($object->object_type_id){
                                case '25': $slug = 'videos'; break;
                                default: $slug = 'photos';
                            } @endphp
                            <article class="col-md-6 col-xl-3 post-itm">
                                <a href="{{url($slug . '/' . $object->id)}}" class="post-itm__link">
                                    <figure class="post-itm__cont">
                                    <span class="post-itm__pict">
                                        @if(isset($relatedImages[$object->id]))
                                                <img src="{{$relatedImages[$object->id]}}" alt="{{$object->name}}">
                                        @endif
                                    </span>
                                        <figcaption class="post-itm__capt">
                                            <h3 class="post-itm__ttl">{{isset($object->name) && !empty($object->name) ? $object->name : $object->id}}</h3>
                                            <ul class="data-list list-style">
                                                <li class="data-list__i">
                                                    ID: <span class="data-list__id">{{$object->id}}</span>
                                                </li>
                                                @if(isset($object->places))
                                                    <li class="data-list__i">
                                                        @lang('translations.city'): <span class="data-list__city">
                                                            @foreach($object->places as $place)
                                                                {{$place->name}}
                                                                @break
                                                            @endforeach
                                                        </span>
                                                    </li>
                                                @endif
                                                @if(isset($object->dates_from_to))
                                                    <li class="data-list__i">
                                                        @lang('translations.date'): <span class="data-list__year">
                                                        {{$object->dates_from_to}}
                                                        </span>
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
        </main>
    </div>
@endsection
@section('js-libs')
    <script src="{{asset('js/zoom.min.js')}}"></script>
@endsection