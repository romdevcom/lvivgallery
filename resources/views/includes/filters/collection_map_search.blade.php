@php
    $searcher = new App\Searcher();
@endphp
<aside class="col-sm-6 offset-sm-3 col-lg-3 offset-lg-0">
    <form id="#" action="{{url('collections/'.$collection->id.'/maps')}}" method="GET" class="aside-form">
        <ul class="aside-bar list-style">
            <li class="aside-bar__block">
                <div class="aside-search">
                    <input type="text" name="full-search" class="aside-search__ctrl"
                           placeholder="@lang('translations.search_by_key')"
                           value="{{old('full-search')}}">
                    <button type="submit" class="aside-search__btn icon-search">search</button>
                </div>
            </li>
        </ul>
        <ul class="aside-bar list-style">
            <li class="aside-bar__block">
                @php
                    $min = null;
                    $max = null;
                    $dates = old('dates');
                    $dates = explode(',', $dates);
                    if(is_array($dates) && count($dates) > 1) {
                        $min = $dates[0];
                        $max = $dates[1];
                    }
                    //dd($dates);
                @endphp
                <p class="aslide-ttl">@lang('translations.time')</p>
                <div id="aside-range">

                    <input type="text" name="dates" class="aside-range__date" value="{{strlen(old('dates')) > 4 ? old('dates') : '1600,'.date('Y')}}" readonly>
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
                        <input type="checkbox" name="filter-trigger" id="filter-trigger-places" class="main-filter-chxb">
                        <label for="filter-trigger-places" class="main-filter-lbl">main-checkbox</label>
                        <a href="#" class="aside-accordion__opener">@lang('translations.places')</a>
                        <div class="aside-accordion__content">
                            <dl class="aside-filter list-style aside-filter--advanced">
                                <dt></dt>
                                @php
                                    $placesList = $searcher->prepareObjSelect(['singular' => 'place', 'plural' => 'places'], 253,'name_sort',false,$collection->id);
                                @endphp
                                @foreach($placesList as $key => $place)
                                    @if(isset($place->cnt))
                                        <dd class="aside-filter__itm">
                                            <input type="checkbox" class="aside-filter__checkbox"
                                                   name="places[{{$key}}]" value="{{$place->id}}"
                                                   id="place-{{$place->id}}"
                                                    {{is_array(old('places')) &&
                                                    in_array($place->id, old('places')) ? 'checked' : ''}}>
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