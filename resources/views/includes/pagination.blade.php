<nav class="pg-nav">
    <ul class="lisn pagination">
        @php
            if($current_page > 2) {
                $firstLink = $current_page - 2;
                $lastLink = $current_page + 2;
            } else {
                $firstLink = 1;
                $lastLink = 5;
            }
            if($pages < $lastLink){
                $lastLink = $pages;
            }
        @endphp
        @if($current_page > 1)
            <li class="prev icon-arr-l">
                <a href="{{url($slug) . '?' . $query_string . '&page=' . ($current_page - 1)}}"></a>
            </li>
        @endif
        @if($current_page > 3)
            <li>
                <a href="{{url($slug) . '?' . $query_string}}">1</a>
            </li>
            <li>
                <span>...</span>
            </li>
        @endif
        @for($page = $firstLink; $page <= $lastLink; $page++ )
            <li class="{{$page == $current_page ? 'active' : ''}}">
                <a href="{{$page == 1 ? url($slug). '?' . $query_string : url($slug) . '?' . $query_string . '&page=' . $page}}">{{$page}}</a>
            </li>
        @endfor

        @if(($current_page < $pages - 2) && $pages != 5)
            <li class="more">
                <span>...</span>
            </li>
            <li>
                <a href="{{url($slug) . '?' . $query_string . '&page=' . $pages}}">{{$pages}}</a>
            </li>
        @endif
        @if($current_page  < $pages)
            <li class="next icon-arr-r">
                <a href="{{url($slug) . '?' . $query_string . '&page=' . ($current_page + 1)}}"></a>
            </li>
        @endif
    </ul>
</nav>