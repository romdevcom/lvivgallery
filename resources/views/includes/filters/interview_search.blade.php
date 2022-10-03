<form id="#" action="{{url('interviews')}}" method="GET" class="aside-form interview-search">
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
            @if($interviewCollections)
                @php
                    $thisCollections = array();
                    if(!empty($all) && isset($all['collections']) && count($all['collections']) > 0){
                        foreach ($all['collections'] as $col){
                            array_push($thisCollections, $col);
                        }
                    }
                @endphp
                <ul class="aside-accordion list-style">
                    <li class="aside-accordion__itm">
                        <input type="checkbox" name="filter-trigger" id="filter-trigger-сollect" class="main-filter-chxb">
                        <label for="filter-trigger-сollect" class="main-filter-lbl">main-checkbox</label>
                        <a href="#" class="aside-accordion__opener">@lang('translations.collections')</a>
                        <div class="aside-accordion__content">
                            <dl class="aside-filter list-style aside-filter--advanced">
                                <dt></dt>
                                @foreach($interviewCollections as $key => $collection)
                                    <dd class="aside-filter__itm">
                                        <input type="checkbox" class="aside-filter__checkbox"
                                               name="collections[{{$key}}]" value="{{$collection['id']}}"
                                               id="collection-{{$collection['id']}}"
                                                {{in_array($collection['id'], $thisCollections) ? 'checked' : ''}}>
                                        <label for="collection-{{$collection['id']}}">
                                            {{$collection['name']}}({{$collection['count']}})
                                        </label>
                                    </dd>
                                @endforeach
                            </dl>
                        </div>
                    </li>
                </ul>
            @endif
        </li>
        <li class="aside-bar__block">
            <div class="aside-action">
                <button type="submit" class="btn icon-arrow-r2 btn--ico aside-action__show">@lang('translations.show_results')</button>
                <button class="icon-refresh aside-action__reload" onclick="reload_page(event);">refresh</button>
            </div>
        </li>
    </ul>
</form>