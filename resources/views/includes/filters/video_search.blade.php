<aside class="col-sm-6 offset-sm-3 col-lg-3 offset-lg-0">
    <form id="#" action="{{url('videos')}}" method="GET" class="aside-form">
        <ul class="aside-bar list-style">
            <li class="aside-bar__block">
                <div class="aside-search">
                    <input type="text" name="full-search" class="aside-search__ctrl"
                           placeholder="@lang('translations.search_by_key')"
                           value="{{!empty($string) ? $string : ''}}">
                    <button type="submit" class="aside-search__btn icon-search">search</button>
                </div>
            </li>
        </ul>
        <ul class="aside-bar list-style">
            <li class="aside-bar__block">
                <ul class="aside-accordion list-style">
                    <li class="aside-accordion__itm">
                        <input type="checkbox" name="filter-trigger" id="filter-trigger-genre" class="main-filter-chxb">
                        <label for="filter-trigger-genre" class="main-filter-lbl">main-checkbox</label>
                        {{--<label for="filter-trigger" class="main-filter-lbl">main-checkbox</label>--}}
                        <a href="#" class="aside-accordion__opener">@lang('translations.genres')</a>
                        <div class="aside-accordion__content">
                        @php
                            $searcher = new App\Searcher();
                            $genreList = $searcher->prepareListSelect('movie_genre', 25, 'name_sort');
                        @endphp
                            <dl class="aside-filter list-style aside-filter--advanced">
                                <dt></dt>
                                @foreach($genreList as $key => $genre)
                                    <dd class="aside-filter__itm">
                                        <input type="checkbox" class="aside-filter__checkbox"
                                               name="movie_genre[{{$key}}]" id="movie_genre-{{$genre->item_id}}"
                                               value="{{$genre->item_id}}"
                                                {{in_array($genre->item_id, $oldGenre) ? 'checked' : ''}}>
                                        <label for="movie_genre-{{$genre->item_id}}">{{$genre->name_plural}}({{$genre->cnt}})</label>
                                    </dd>
                                @endforeach
                            </dl>
                        </div>
                    </li>
                </ul>
            </li>
            <li class="aside-bar__block">
                <ul class="aside-accordion list-style">
                    <li class="aside-accordion__itm">
                        <input type="checkbox" name="filter-trigger" id="filter-trigger-technique" class="main-filter-chxb">
                        <label for="filter-trigger-technique" class="main-filter-lbl">main-checkbox</label>
                        {{--<label for="filter-trigger" class="main-filter-lbl">main-checkbox</label>--}}
                        <a href="#" class="aside-accordion__opener">@lang('translations.techniques')</a>
                        <div class="aside-accordion__content">
                            @php
                                $searcher = new App\Searcher();
                                $techniqueList = $searcher->prepareListSelect('movie_technique', 25, 'name_sort');
                            @endphp
                            <dl class="aside-filter list-style aside-filter--advanced">
                                <dt></dt>
                                @foreach($techniqueList as $key => $technique)
                                    <dd class="aside-filter__itm">
                                        <input type="checkbox" class="aside-filter__checkbox"
                                               name="movie_technique[{{$key}}]" id="movie_technique-{{$technique->item_id}}"
                                               value="{{$technique->item_id}}"
                                                {{in_array($technique->item_id, $oldTechniques) ? 'checked' : ''}}>
                                        <label for="movie_technique-{{$technique->item_id}}">{{$technique->name_singular}}({{$technique->cnt}})</label>
                                    </dd>
                                @endforeach
                            </dl>
                        </div>
                    </li>
                </ul>
            </li>
            <li class="aside-bar__block">
                @php
                    $min = null;
                    $max = null;
                    if(count($oldDates)) {
                        $min = $oldDates[0];
                        $max = $oldDates[1];
                    }
                @endphp
                <p class="aslide-ttl">@lang('translations.time')</p>
                <div id="aside-range">

                    <input type="text" name="dates" class="aside-range__date" value="{{count($oldDates) ? $oldDates[0] . ',' . $oldDates[1] : '1600,'.date('Y')}}" readonly>
                    <input type="hidden" class="" id="aside-range__input-min" readonly
                           value="{{$min ? $min : 1600}}">
                    <input type="hidden" class="" id="aside-range__input-max" readonly
                           value="{{$max ? $max : date('Y')}}">
                    <label class="aside-range__result range-lbl" for="range-slider">
                        <span class="range-lbl__output range-lbl__output--min"></span>
                        <span class="range-lbl__output range-lbl__output--max"></span>
                    </label>
                    <div id="range-slider" name="slider"></div>
                </div>
            </li>
            <li class="aside-bar__block">
                <ul class="aside-accordion list-style">

                    <li class="aside-accordion__itm">
                        <input type="checkbox" name="filter-trigger" id="filter-trigger-??ollect" class="main-filter-chxb">
                        <label for="filter-trigger-??ollect" class="main-filter-lbl">main-checkbox</label>
                        <a href="#" class="aside-accordion__opener">@lang('translations.collections')</a>
                        <div class="aside-accordion__content">
                            <dl class="aside-filter list-style aside-filter--advanced">
                                <dt></dt>
                                @php
                                    $collectionList = $searcher->prepareObjSelect(
                                        ['singular' => 'collection', 'plural' => 'collections'], 25,'name_sort'
                                    );
                                @endphp
                                @foreach($collectionList as $key => $collection)
                                    @if(isset($collection->cnt))
                                        <dd class="aside-filter__itm">
                                            <input type="checkbox" class="aside-filter__checkbox"
                                                   name="collections[{{$key}}]" value="{{$collection->id}}"
                                                   id="collection-{{$collection->id}}"
                                                    {{in_array($collection->id, $oldCollections) ? 'checked' : ''}}>
                                            <label for="collection-{{$collection->id}}">
                                                {{$collection->name}}({{$collection->cnt}})
                                            </label>
                                        </dd>
                                    @endif
                                @endforeach
                            </dl>
                        </div>
                    </li>
                    <li class="aside-accordion__itm">
                        <input type="checkbox" name="filter-trigger" id="filter-trigger-places" class="main-filter-chxb">
                        <label for="filter-trigger-places" class="main-filter-lbl">main-checkbox</label>
                        <a href="#" class="aside-accordion__opener">@lang('translations.places')</a>

                        <div class="aside-accordion__content">
                            <dl class="aside-filter list-style aside-filter--advanced">
                                <dt></dt>
                                @php
                                    $placesList = $searcher->prepareObjSelect(['singular' => 'place', 'plural' => 'places'],
                                    25, 'name_sort');
                                @endphp
                                @foreach($placesList as $key => $place)
                                    @if(isset($place->cnt))
                                        <dd class="aside-filter__itm">
                                            <input type="checkbox" class="aside-filter__checkbox"
                                                   name="places[{{$key}}]" value="{{$place->id}}"
                                                   id="place-{{$place->id}}"
                                                    {{in_array($place->id, $oldPlaces) ? 'checked' : ''}}>
                                            <label for="place-{{$place->id}}">
                                                {{$place->name}}({{$place->cnt}})
                                            </label>
                                        </dd>
                                    @endif
                                @endforeach
                            </dl>
                        </div>
                    </li>
                </ul>

            </li>
            <li class="aside-bar__block">
                <div class="aside-action">
                    <button type="submit" class="btn icon-arrow-r2 btn--ico aside-action__show">@lang('translations.show_results')</button>
                    <button class="icon-refresh aside-action__reload" onclick="reload_page(event);">refresh</button>
                </div>
            </li>
        </ul>
    </form>
</aside>
<a href="#" class="icon-arrow-t scrollup filter-scroll" style="display:none">scroll top</a>