@extends('layouts.main')
@section('content')
    <div class="ctn-wrapper">
        <div class="container">
            <div class="row primary-content">
                @include('includes.filters.video_search')
                <main class="col-lg-9">
                    @include('includes.header_tabs')
                    <section class="posts">
                        <div class="row m-grid">
                            @if(isset($results))
                                @for($i = (count($results) - 1); $i >=0; $i--)
                                    @php $result = $results[$i]; @endphp
                                    <article class="col-md-6 col-xl-4 post-itm grid-item">
                                        <a href="{{url('videos/'. $result->id)}}" class="post-itm__link">
                                            <figure class="post-itm__cont">
                                                <span class="post-itm__pict">
                                                     @if(!empty($result->image))
                                                        <img class="img-fluid rounded mb-3 mb-md-0" src="{{$result->image}}" alt="{{$result->name}}">
                                                    @endif
                                                </span>
                                                <figcaption class="post-itm__capt">
                                                    <h3 class="post-itm__ttl">{{$result->name}}</h3>
                                                    @if(isset($result->old_changed) && $result->old_changed)
                                                        <time class="post-itm__time"
                                                              datetime="{{str_replace('-', '.', explode(' ', $result->old_changed)[0])}}">
                                                            {{str_replace('-', '.', explode(' ', $result->old_changed)[0])}}
                                                        </time>
                                                    @endif
                                                    <ul class="data-list list-style">
                                                        <li class="data-list__i">
                                                            ID: <span class="data-list__id">{{$result->id}}</span>
                                                        </li>
                                                        @if(!empty($result->place))
                                                            <li class="data-list__i">
                                                                @lang('translations.city'): <span class="data-list__id">{{$result->place}}</span>
                                                            </li>
                                                        @endif
                                                        @if(!empty($result->year))
                                                            <li class="data-list__i">
                                                                @lang('translations.date'): <span class="data-list__year">{{simplify_date($result->year)}}</span>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </figcaption>
                                            </figure>
                                        </a>
                                    </article>
                                @endfor
                                @if(!empty($results) && count($results) == 15)
                                    <div class="col-md-6 col-xl-4 post-itm grid-item more-post-item">
                                        <a href="#" class="icon-pl load-post" onclick="show_more_posts(25)">
                                            @lang('translations.show_more_results')
                                        </a>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </section>
                    <input type="hidden" name="load_page" value="{{$currentPage}}">
                    <input type="hidden" name="pages_count" value="{{$pages}}">
                    <input type="hidden" name="pages_query" value="{{$queryString}}">
                    @if(isset($results))
                        @include('includes.pagination')
                    @else
                        {{--<p class="search-error">nothing to show!</p>--}}
                        <p class="search-error">@lang('translations.nothing_to_show')</p>
                    @endif
                </main>
            </div>
        </div>
    </div>
@endsection
@section('js-libs')
    <script src="{{asset('js/masonry.js')}}"></script>
    <script src="{{asset('js/jquery-ui.min.js')}}"></script>
    <script src="{{asset('js/jquery.nicescroll.min.js')}}"></script>
{{--    <script src="{{secure_asset('js/jquery.ui.touch-punch.min.js')}}"></script>--}}
    <script src="{{asset('js/sticky-kit.min.js')}}"></script>
{{--    <script src="{{secure_asset('js/jquery.mCustomScrollbar.concat.min.js')}}"></script>--}}
@endsection