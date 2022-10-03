@extends('layouts.main')
@section('content')
    <div class="ctn-wrapper">
        <main>
            @if(!empty($message))
                <section class="msg-article">
                    <div class="container">
                        <div class="row ">
                            <div class="col-lg-8 offset-lg-3">
                                <div class="msg-article__hero">
                                    <img src="https://www.lvivcenter.org/image.php?newsid={{$message['id']}}" alt="{{!empty($message['title'.$lang]) ? $message['title'.$lang] : ''}}">
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-3 msg-article__lcol">
                                <time class="msg-article__date">{{ $message['date'] }}</time>
                            </div>
                            <div class="col-md-8">
                                <article class="cms-editor">
                                    <h1 class="msg-article__ttl">@if(!empty($message['title'.$lang])){{$message['title'.$lang]}} @else @lang('translations.not_translated')@endif</h1>
                                    {!! !empty($message['description'.$lang]) ? preg_replace('/(<[^>]+) style=".*?"/i', '$1', $message['description'.$lang]) : '' !!}
                                </article>
                            </div>
                            <div class="col-lg-1">
                                <a href="#" class="icon-share btn-share"></a>
                            </div>
                        </div>
                    </div>
                </section>
                @if(!empty($related))
                    <section class="posts">
                        <div class="container">
                            <h2 class="section-title">
                                @lang('translations.latest_news')
                            </h2>
                            <div class="row">
                                @php $count = 0 @endphp
                                @foreach($related as $item)
                                    @if($message['id'] == $item['id']) @continue @endif
                                    @if($count++ == 4) @break @endif
                                    <article class="col-md-6 col-xl-3 post-itm message-itm">
                                        <a href="{{url('messages/'.$item['slug'])}}" class="post-itm__link">
                                            <figure class="post-itm__cont">
                                                    <span class="post-itm__pict">
                                                        <img src="https://www.lvivcenter.org/image.php?newsid={{$item['id']}}" alt="{{!empty($item['title'.$lang]) ? $item['title'.$lang] : ''}}">
                                                    </span>
                                                <figcaption class="post-itm__capt">
                                                    <h3 class="post-itm__ttl">{{!empty($item['title'.$lang]) ? $item['title'.$lang] : ''}}</h3>
                                                    {!! !empty($item['short_description'.$lang]) ? strip_tags($item['short_description'.$lang]) : '' !!}
                                                    <time class="post-itm__time" datetime="{{$item['date']}}">{{message_date($item['date'])}}</time>
                                                </figcaption>
                                            </figure>
                                        </a>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif
            @endif
        </main>
    </div>
@endsection
@section('js-libs')
    <script src="{{asset('js/zoom.min.js')}}"></script>
@endsection