@extends('layouts.main')
@section('content')
    <div class="ctn-wrapper">
        <div class="container">
            <div class="row justify-content-center primary-content">
                <main>
                    @if(!empty($results))
                        <section class="posts">
                            <div class="container">
                                <div class="row">
                                    @foreach($results as $result)
                                        <article class="col-md-6 col-xl-3 post-itm message-itm">
                                            <a href="{{isset($result['href']) ? $result['href'] : url('messages/'.$result['slug'])}}" class="post-itm__link" {{isset($result['href']) ? 'target="_blank"' : ''}}>
                                                <figure class="post-itm__cont">
                                                <span class="post-itm__pict">
                                                    <img src="https://www.lvivcenter.org/image.php?newsid={{$result['id']}}&maxx=500&maxy=500&fit=fitxy" alt="{{!empty($result['title'.$lang]) ? $result['title'.$lang] : ''}}">
                                                </span>
                                                    <figcaption class="post-itm__capt">
                                                        <h3 class="post-itm__ttl">{{$result['title'.$lang]}}</h3>
                                                        {!! strip_tags($result['short_description'.$lang]) !!}
                                                        <time class="post-itm__time" datetime="{{$result['date']}}">{{message_date($result['date'])}}</time>
                                                    </figcaption>
                                                </figure>
                                            </a>
                                        </article>
                                    @endforeach
                                </div>
                                @include('includes.pagination')
                            </div>
                        </section>
                    @else
                        <p>@lang('translations.nothing_to_show')</p>
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
    <script src="{{asset('js/sticky-kit.min.js')}}"></script>
@endsection