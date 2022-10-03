@extends('layouts.main')
@section('content')
    @php $lang = lang_code() == 15 ? 'uk' : 'en'; @endphp
    <main class="pt20px pt30px-m">
        <article class="object">
            <a href="/" class="back">
                @lang('translations.collection')
            </a>
            <div class="container" itemscope itemtype="https://schema.org/VisualArtwork">
                <div class="row object-head">
                    <div class="col-md-6 offset-md-4 mb30px">
                        @if(isset($result->name))
                            <h1 class="ttl2 object-head__ttl mb15px" itemprop="name">
                                {{$result->name}}
                            </h1>
                        @endif
                        @if(isset($result->entities))
                            @foreach($result->entities as $entity)
                                @if($entity->type_id == 108)
                                    <p class="object-head__subttl">
                                        {{$entity->name}}
                                    </p>
                                    @break
                                @endif
                            @endforeach
                        @endif
                    </div>
                    <div class="col-12 object-visual mb35px">
                        @if(isset($result->embed3d) && !empty($result->embed3d))
                            @include('includes.images.embed')
                        @else
                            @if(isset($zoomify) && count($zoomify) && isset($zoomify['main']))
                                @include('includes.images.zoomify')
                            @else
                                @include('includes.images.tilepic')
                            @endif
                        @endif
                        @include('includes.images.tilepic-tiles')
                        <ul class="object-actions">
                            <li class="object-actions__el">
                                <button class="icon-fullscreen object-actions__btn object-actions__btn--fullscreen img-zoom" title="enter to fullscreen mode"></button>
                            </li>
                            <li class="object-actions__el">
                                <button class="icon-chain object-actions__btn object-actions__btn--copy" title="copy url"></button>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="row object-info">
                    <div class="col-md-3 offset-md-1 object-info__cat">
                        @lang('translations.object_main_information')
                    </div>
                    <div class="col-md-7 object-info__main">
                        <dl class="detail-list">
                            @if(isset($result->stocknumber))
                                <dt>@lang('translations.object_id')</dt>
                                <dd>{{$result->stocknumber}}</dd>
                            @endif
                            @if(isset($result->entities))
                                @foreach($result->entities as $entity)
                                    @if($entity->type_id == 108)
                                        <dt>@lang('translations.object_author')</dt>
                                        <dd itemprop="creator" itemscope itemtype="https://schema.org/Person">
                                            <a href="/{{$lang}}/?author-{{$entity->id}}=on" itemprop="sameAs">
                                                <span itemprop="name">{{$entity->name}}</span>
                                            </a>
                                        </dd>
                                        @break
                                    @endif
                                @endforeach
                            @endif
                            @if(isset($result->name))
                                <dt>@lang('translations.object_name')</dt>
                                <dd>{{$result->name}}</dd>
                            @endif
                            @if(isset($result->original_name))
                                <dt>@lang('translations.object_name_origin')</dt>
                                <dd>{{$result->original_name}}</dd>
                            @endif
                            @if(isset($result->displayCreationDate))
                                <dt>@lang('translations.object_date')</dt>
                                <dd itemprop="dateCreated">{{$result->displayCreationDate}}</dd>
                            @endif
                            @if(isset($result->country))
                                <dt>@lang('translations.object_country')</dt>
                                <dd>
                                    @foreach($result->country as $key => $item)
                                        <a href="/{{$lang}}/?msf-58-{{$key}}=on">{{$item}}</a>
                                    @endforeach
                                </dd>
                            @endif
                            @if(isset($result->culture))
                                <dt>@lang('translations.object_culture')</dt>
                                <dd>
                                    @foreach($result->culture as $key => $item)
                                        <a href="/{{$lang}}/?msf-59-{{$key}}=on">{{$item}}</a>
                                    @endforeach
                                </dd>
                            @endif
                            @if(isset($result->technique))
                                <dt>@lang('translations.object_technique')</dt>
                                <dd>
                                    @foreach($result->technique as $key => $item)
                                        <a href="/{{$lang}}/?msf-51-{{$key}}=on">{{$item}}</a>
                                    @endforeach
                                </dd>
                            @endif
                            @if(isset($result->medium))
                                <dt>@lang('translations.object_medium')</dt>
                                <dd itemprop="artMedium">
                                    @foreach($result->medium as $key => $item)
                                        <a href="/{{$lang}}/?msf-52-{{$key}}=on">{{$item}}</a>
                                    @endforeach
                                </dd>
                            @endif
                            @if(!empty($result->dimension_label) && !empty($result->dimension_values))
                                <dt>@lang('translations.object_dimensions') ({{$result->dimension_label}}, @lang('translations.cm'))</dt>
                                <dd>
                                    {!!$result->dimension_values!!}
                                </dd>
                            @endif
                        </dl>
                    </div>
                </div>
                @if(isset($result->genre) || isset($result->worktype) || isset($result->iconclass) || isset($result->provenance) || isset($result->exposition))
                    <div class="row object-info">
                        <div class="col-md-3 offset-md-1 object-info__cat">
                            @lang('translations.object_additional')
                        </div>
                        <div class="col-md-7 object-info__main">
                            <dl class="detail-list">
                                @if(isset($result->worktype))
                                    <dt>@lang('translations.object_type')</dt>
                                    <dd itemprop="artform">
                                        @foreach($result->worktype as $key => $item)
                                            <a href="/{{$lang}}/?msf-53-{{$key}}=on">{{$item}}</a>
                                        @endforeach
                                    </dd>
                                @endif
                                @if(isset($result->genre))
                                    <dt>@lang('translations.object_genre')</dt>
                                    <dd>
                                        @foreach($result->genre as $key => $item)
                                            <a href="/{{$lang}}/?msf-54-{{$key}}=on">{{$item}}</a>
                                        @endforeach
                                    </dd>
                                @endif
                                @if(isset($result->iconclass))
                                    <dt>@lang('translations.object_iconclass')</dt>
                                    <dd itemprop="artworkSurface">
                                        @foreach($result->iconclass as $key => $item)
                                            <a href="/{{$lang}}/?msf-52-{{$key}}=on">{{$item}}</a>
                                        @endforeach
                                    </dd>
                                @endif
                                @if(isset($result->provenance))
                                    <dt>@lang('translations.object_provenance')</dt>
                                    <dd>
                                        @foreach($result->provenance as $key => $item)
                                            <a href="/{{$lang}}/?msf-56-{{$key}}=on">{{$item}}</a>
                                        @endforeach
                                    </dd>
                                @endif
                                @if(isset($result->exposition))
                                    <dt>@lang('translations.object_exposition')</dt>
                                    <dd>
                                        @foreach($result->exposition as $key => $item)
                                            <a href="/{{$lang}}/?msf-56-{{$key}}=on">{{$item}}</a>
                                        @endforeach
                                    </dd>
                                @endif
                            </dl>
                        </div>
                    </div>
                @endif
                @if(isset($result->entities) || count($author_info))
                    <div class="row object-info">
                        <div class="col-md-3 offset-md-1 object-info__cat">
                            @lang('translations.about_author')
                        </div>
                        <div class="col-md-7 object-info__main author-description">
                            <dl class="detail-list">
                                @if(isset($result->entities))
                                    @foreach($result->entities as $entity)
                                        @if($entity->type_id == 108)
                                            <dt>@lang('translations.object_author')</dt>
                                            <dd itemprop="creator" itemscope itemtype="https://schema.org/Person">
                                                <a href="/{{$lang}}/?author={{$entity->id}}" itemprop="sameAs">
                                                    <span itemprop="name">{{$entity->name}}</span>
                                                </a>
                                            </dd>
                                            @break
                                        @endif
                                    @endforeach
                                @endif
                                @if(isset($author_info[132]) && !empty($author_info[132]))
                                    <dt>@lang('translations.object_author_origin')</dt>
                                    <dd>{{$author_info[132]}}</dd>
                                @endif
                                @if(isset($author_info[125]) && !empty($author_info[125]))
                                    <dt>@lang('translations.object_author_years')</dt>
                                    <dd>{{$author_info[125]}}</dd>
                                @endif
                                @if(isset($author_info[131]) && !empty($author_info[131]))
                                    <dt>@lang('translations.object_country')</dt>
                                    <dd>{{$author_info[131]}}</dd>
                                @endif
                                @if(isset($author_info[124]) && !empty($author_info[124]))
                                    <dt>@lang('translations.object_author_biography')</dt>
                                    <dd class="no-bold">{!!$author_info[124]!!}</dd>
                                @endif
                            </dl>
                        </div>
                    </div>
                @endif
                @if(isset($result->description) || isset($result->work_description))
                    <div class="row object-info">
                        <div class="col-md-3 offset-md-1 object-info__cat">
                            @lang('translations.description')
                        </div>
                        <div class="col-md-7 object-info__main">
                            <div class="content-block">
                                {!! isset($result->work_description) ? $result->work_description : $result->description !!}
                            </div>
                        </div>
                    </div>
                @endif
                @if(isset($result->inscriptions))
                    <div class="row object-info">
                        <div class="col-md-3 offset-md-1 object-info__cat">
                            @lang('translations.inscriptions')
                        </div>
                        <div class="col-md-7 object-info__main">
                            <div class="content-block">
                                {!! $result->inscriptions !!}
                            </div>
                        </div>
                    </div>
                @endif
                @if(isset($result->person_portrayed) || isset($result->person_portrayed_dates))
                    <div class="row object-info">
                        <div class="col-md-3 offset-md-1 object-info__cat">
                            @lang('translations.portrayed')
                        </div>
                        <div class="col-md-7 object-info__main">
                            <dl class="detail-list">
                                @if(isset($result->person_portrayed))
                                    <dt>@lang('translations.object_person_portrayed')</dt>
                                    <dd>{{$result->person_portrayed}}</dd>
                                @endif
                                @if(isset($result->person_portrayed_dates))
                                    <dt>@lang('translations.object_person_portrayed_date')</dt>
                                    <dd>{{$result->person_portrayed_dates}}</dd>
                                @endif
                            </dl>
                        </div>
                    </div>
                @endif
            </div>
        </article>
        @if(!empty($related))
            <section class="cards bg-grey5 pt65px pb75px">
                <div class="container">
                    <h2 class="section-ttl mb35px mb30px-m">
                        @lang('translations.other')
                    </h2>
                    <ul class="cards-list cards-list--compact-xs mb20px">
                        @foreach($related as $item)
                            <li class="cards-list__card mb45px">
                                <a href="{{url('object/' . $item->url)}}" class="card">
                                    <figure class="card__wrapper">
                                        @if(isset($item->image) && !empty($item->image))
                                            <div class="card__img mb15px">
                                                <img src="{{$item->image}}" alt="img">
                                            </div>
                                        @endif
                                        <figcaption>
                                            @if(isset($item->name))
                                                <p class="card__ttl">
                                                    {{$item->name}}
                                                </p>
                                            @endif
                                            @if(isset($item->author))
                                                <p class="card__subttl">
                                                    {{$item->author}}
                                                </p>
                                            @endif
                                        </figcaption>
                                    </figure>
                                </a>
                            </li>
                        @endforeach
                        <li class="cards-list__card">
                            <a href="/{{$lang}}" class="card-more">
                                <span class="card-more__txt fw600 clr-primary">
                                    @lang('translations.see_all_collection')
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </section>
        @endif
    </main>
@endsection
@section('scripts')
    <script>document.addEventListener('contextmenu', event => event.preventDefault());</script>
    <link rel="stylesheet" href="/css/circular-slider.css">
    <link rel="stylesheet" href="/css/jquery.tileviewer.css">

    @if(count($zoomify))
        <script src="/js/openseadragon.min.js"></script>
    @else
        <script src="/js/jquery.hotkeys.js"></script>
        <script src="/js/jquery.mousewheel.js"></script>
        <script src="/js/circular-slider.js"></script>
        <script src="/js/jquery.tileviewer.js"></script>
    @endif
@endsection