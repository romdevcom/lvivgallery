@extends('layouts.main')
@section('content')
    <div class="ctn-wrapper">
        <div class="container">
            <div class="row primary-content interview-main">
                <div class="col-md-4 col-lg-3">
                    @include('user.includes.profile')
                    @include('includes.filters.interview_search')
                </div>
                <main class="col-lg-9">
                    @include('includes.header_tabs')
                    @if(!$allow)
                        <div class="interview-info">
                            <p>@lang('translations.make_attention')</p>
                            <p>@lang('translations.login_please'), <a href="{{url('user/registration')}}">@lang('translations.registrer')</a> @lang('translations.or_request').</p>
                        </div>
                    @endif
                    @if($list)
                        @if(isset($results) && !empty($results))
                            <section class="posts">
                                <div class="row m-grid">
                                    @for($i = (count($results) - 1); $i >=0; $i--)
                                        @php $result = $results[$i]; @endphp
                                        <article class="col-md-6 post-itm grid-item interview-itm">
                                            @if($result->name)
                                                <strong class="post-itm__ttl">{{$result->name}}</strong>
                                            @endif
                                            @if($result->description)
                                                <div class="interview-itm__desc">
                                                    {{$result->description}}
                                                </div>
                                            @endif
                                            @if($result->date)
                                                <span>@lang('translations.interview_date'): {{$result->date}}</span>
                                            @endif
                                            <div class="ta-right">
                                                <a href="{{url('interviews/'. $result->id)}}" class="btn btn--more">@lang('translations.more_details')</a>
                                            </div>
                                        </article>
                                    @endfor
                                </div>
                            </section>
                            {{--<input type="hidden" name="load_page" value="{{$currentPage}}">--}}
                            {{--<input type="hidden" name="pages_count" value="{{$pages}}">--}}
                            {{--<input type="hidden" name="pages_query" value="{{$queryString}}">--}}
                            @if(isset($results))
                                @include('includes.pagination')
                            @else
                                <p class="search-error">nothing to show!</p>
                                <p class="search-error">@lang('translations.nothing_to_show')</p>
                            @endif
                        @endif
                    @else
                        <section class="wide-posts">
                            @foreach($collectionObj as $result)
                                <article class="wide-article">
                                    <a href="{{url('collections/'.$result->id.'/interviews')}}" class="wide-article__link">
                                        <figure class="wide-article__cont row align-items-center">
                                            <span class="col-md-5 wide-article__pict">
                                                @if(!empty($result->image))
                                                    <img src="{{$result->image}}" alt="{{$result->name}}">
                                                @endif
                                            </span>
                                            <figcaption class="col-md-7 wide-article__capt">
                                                <h3 class="wide-article__ttl">{{$result->name}}</h3>
                                                <div class="cms-editor wide-article__desc">
                                                    <p>@if(!empty($result->short_text)){{$result->short_text}}@endif</p>
                                                </div>
                                                <div class="wide-posts__action">
                                                    @if(empty($result->objects))
                                                        <span class="wide-article__q">@lang('translations.collection_in_process')</span>
                                                    @else
                                                        <span class="wide-article__q">{{count($result->objects)}} @lang('translations.objects')</span>
                                                    @endif
                                                    <span class="btn btn--more m-0">@lang('translations.more')</span>
                                                </div>
                                            </figcaption>
                                        </figure>
                                    </a>
                                </article>
                            @endforeach
                        </section>
                    @endif
                </main>
                <script>
                    let langErrorRetype = '@lang('translations.error_retype')';
                    let langError = '@lang('translations.error')';
                    let langErrorLogin = '@lang('translations.error_login_or_pass')';
                    let langErrorEmail = '@lang('translations.error_email')';
                </script>
            </div>
        </div>
    </div>
@endsection
@section('js-libs')
    <script src="{{asset('js/masonry.js')}}"></script>
    <script src="{{asset('js/jquery-ui.min.js')}}"></script>
    <script src="{{asset('js/jquery.nicescroll.min.js')}}"></script>
    <script src="{{asset('js/sticky-kit.min.js')}}"></script>
@endsection