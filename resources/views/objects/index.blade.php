@extends('layouts.main')
@section('content')
    <main class="pt40px pt30px-m pb90px pb70px-m">
        <section class="ctrl-section">
            <div class="container">
                <h1 class="ttl1 mb40px mb30px-m ctrl-section__ttl">
                    @lang('translations.collection')
                </h1>
                <div class="object-top">
                    <button class="ctrl-section__opener icon-setting show-on-xs">@lang('translations.filters')</button>
                    <a href="/" class="msf-actions__btn msf-reset" style="{{$current_years || !empty($search_string) || (isset($params['list']) && count($params['list'])) || (isset($params['authors']) && count($params['authors'])) ? '' : 'display:none;'}}">@lang('translations.reset')</a>
                </div>
                <div class="row ctrl-section__row">
                    <div class="col-lg-10 ctrl-section__col">
                        <form action="" id="mainSearchForm" class="mainSearchForm">
                            <button class="ctrl-section__closer icon-close"></button>
                            <ul class="ms-ctrls">
                                <li class="ms-ctrls__col ms-ctrls__col--srch">
                                    <label for="0001" class="msf-query icon-search">
                                        <input name="quick-search" type="text" class="msf-query__inp" placeholder="@lang('translations.filter_search')" value="{{!empty($search_string) ? $search_string : ''}}">
                                    </label>
                                    <nav class="srch-sgs">
                                        @lang('translations.start_typing')
                                    </nav>
                                </li>
                                <li class="ms-ctrls__col ms-ctrls__col--years">
                                    <div id="" class="filterRange">
                                        <input type="text" name="years" class="filterRange__inp" readonly="readonly">
                                        <label class="range-lbl filterRange__lbl" for="range-slider">
                                            <input type="number" class="range-lbl__input range-lbl__input--min">
                                            <div id="range-slider" class="filterRange__slider"
                                                 data-min="{{$filter_years[0]}}"
                                                 data-max="{{$filter_years[count($filter_years) - 1]}}"
                                                 @if($current_years)
                                                     data-current-min="{{$current_years[0]}}"
                                                     data-current-max="{{$current_years[1]}}"
                                                 @endif
                                            ></div>
                                            <input type="number" class="range-lbl__input range-lbl__input--max">
                                        </label>
                                    </div>
                                </li>
                                @if(count($filters))
                                <li class="ms-ctrls__col ms-ctrls__col--filter">
                                    <ul class="msf-list">
                                        <li class="msf-list__el active">
                                            <span class="msf-list__name">@lang('translations.object_author')</span>
                                            <ul class="msf-filter">
                                                @foreach($authors as $item)
                                                    <li class="msf-filter__opts chkb-block">
                                                        @php $check = isset($params['authors']) && in_array($item->entity_id, $params['authors']) ? 'checked' : ''; @endphp
                                                        <input type="checkbox" class="chkb-block__ctrl" name="author-{{$item->entity_id}}" id="author-{{$item->entity_id}}" {{$check}}>
                                                        <label class="chkb-block__lbl" for="author-{{$item->entity_id}}">{{$item->displayname}}</label>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                        @foreach($filters as $key => $filter)
                                            <li class="msf-list__el active">
                                                <span class="msf-list__name">
                                                    @switch($key)
                                                        @case(51) @lang('translations.filter_machinery') @break
                                                        @case(52) @lang('translations.filter_material') @break
                                                        @case(53) @lang('translations.filter_type') @break
                                                        @case(54) @lang('translations.filter_genre') @break
                                                        @case(55) @lang('translations.filter_plot') @break
                                                        @case(56) @lang('translations.filter_provence') @break
                                                        @case(57) @lang('translations.filter_exposition') @break
                                                        @case(58) @lang('translations.filter_country') @break
                                                        @case(59) @lang('translations.filter_culture') @break
                                                    @endswitch
                                                </span>
                                                <ul class="msf-filter {{in_array($key, array(57, 56)) ? 'list-two' : ''}}">
                                                    @foreach($filter as $item)
                                                        <li class="msf-filter__opts chkb-block">
                                                            @php $check = isset($params['list'][$key]) && in_array($item->item_id, $params['list'][$key]) ? 'checked' : ''; @endphp
                                                            <input type="checkbox" class="chkb-block__ctrl" name="msf-{{$key}}-{{$item->item_id}}" id="opt-{{$key}}-{{$item->item_id}}" {{$check}}>
                                                            <label class="chkb-block__lbl" for="opt-{{$key}}-{{$item->item_id}}">{{$item->name_singular}}</label>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                                @endif
                                <li class="ms-ctrls__col ms-ctrls__col--tags hide-on-sm">
                                    <ul class="tags-list">
                                        @if((isset($params['authors']) && count($params['authors'])))
                                            @foreach($authors as $author)
                                                @if(in_array($author->entity_id, $params['authors']))
                                                    <li class="tags-list__tag">
                                                        <a href="{{str_replace('author-' . $author->entity_id . '=on', '', $url)}}" data-filter-id="author-{{$author->entity_id}}" class="tag">{{$author->displayname}}</a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        @endif
                                        @if((isset($params['list']) && count($params['list'])))
                                            @php
                                                $params_array = array();
                                                foreach($params['list'] as $value){
                                                    $params_array = array_merge($params_array, $value);
                                                }
                                            @endphp
                                            @foreach($filters as $key => $filter)
                                                @foreach($filter as $item)
                                                    @if(in_array($item->item_id, $params_array))
                                                        <li class="tags-list__tag">
                                                            <a href="{{str_replace('msf-' . $key . '-' . $item->item_id . '=on', '', $url)}}" data-filter-id="opt-{{$key}}-{{$item->item_id}}" class="tag">{{$item->name_singular}}</a>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                        @endif
                                        @if($current_years)
                                            <li class="tags-list__tag">
                                                <a href="{{str_replace('&years=' . join('%2C', $current_years), '', $url)}}" class="tag">{{join('-', $current_years)}}</a>
                                            </li>
                                        @endif
                                        @if(!empty($search_string))
                                            <li class="tags-list__tag">
                                                <a href="{{remove_get_parameter($url, 'quick-search')}}" class="tag">{{$search_string}}</a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                                <li class="ms-ctrls__col ms-ctrls__col--actions">
                                    <div class="msf-actions active">
                                        <a href="/" class="msf-actions__btn msf-reset" style="{{$current_years || !empty($search_string) || (isset($params['list']) && count($params['list'])) || (isset($params['authors']) && count($params['authors'])) ? '' : 'display:none;'}}">@lang('translations.reset')</a>
                                        <button type="submit" class="msf-actions__btn msf-smb">@lang('translations.show')</button>
                                    </div>
                                </li>
                            </ul>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <section class="cards">
            <div class="container">
                @if($results)
                    <p class="cards-total mb20px">
                        @lang('translations.total') <span class="cards-total__val">{{$count}}</span> @lang('translations.works')
                    </p>
                    <ul class="cards-list mb20px">
                        <li class="loader">
                            <div class="loader-body">
                                <div></div>
                                <div></div>
                                <div></div>
                            </div>
                        </li>
                        @foreach($results as $item)
                            <li class="cards-list__card mb45px">
                                <a href="{{url('object/' . $item->url . '/')}}" class="card">
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
                        @if($current_page < $pages)
                            <li class="cards-list__card more-post-item">
                                <a href="#" class="card-more" onclick="show_more_objects(15)">
                                    <span class="card-more__txt">
                                        @lang('translations.show_all')
                                    </span>
                                </a>
                            </li>
                        @endif
                    </ul>

                    <input type="hidden" name="load_page" value="{{$current_page}}">
                    <input type="hidden" name="pages_count" value="{{$pages}}">
                    <input type="hidden" name="pages_query" value="{{$query_string}}">

                    <input type="hidden" name="query_list" value="{{$filters_json}}">
                    <input type="hidden" name="query_years" value="{{isset($params['years']) ? $params['years'] : ''}}">
                    <input type="hidden" name="query_author" value="{{isset($params['authors']) ? join(',', $params['authors']) : ''}}">
                    <input type="hidden" name="query_string" value="{{$search_string}}">
                @else
                    <h2 style="text-align:center;">
                        @lang('translations.not_found')
                    </h2>
                @endif
                @if($pages > 1)
                    @include('includes.pagination')
                @endif
            </div>
        </section>
    </main>
@endsection