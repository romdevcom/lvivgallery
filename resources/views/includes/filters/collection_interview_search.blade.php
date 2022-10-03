<form id="#" action="{{url($slug)}}" method="GET" class="aside-form interview-search">
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
            <div class="aside-action">
                <button type="submit" class="btn icon-arrow-r2 btn--ico aside-action__show">@lang('translations.show_results')</button>
                <button class="icon-refresh aside-action__reload" onclick="reload_page(event);">refresh</button>
            </div>
        </li>
    </ul>
</form>