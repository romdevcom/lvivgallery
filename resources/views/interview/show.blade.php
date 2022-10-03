@extends('layouts.main')
@section('content')
    <section class="visual-exposition interview-single">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-3">
                    <div class="visual-exposition__cont">
                        @if(!$allow)
                            <div class="interview-info">
                                <p>@lang('translations.make_attention')</p>
                                <p>@lang('translations.login_please'), <a href="{{url('user/registration')}}">@lang('translations.registrer')</a> @lang('translations.or_request').</p>
                            </div>
                        @elseif($allow && !empty($result->audio))
                            <div class="audio-player">
                                <audio controls>
                                    <source src="{{$result->audio}}" type="audio/mpeg">
                                </audio>
                            </div>
                        @endif
                        <h1 class="visual-exposition__ttl">
                            {{$result->name}}
                        </h1>
                        @if(!empty($collection))
                            <span class="sub-title">
                                @lang('translations.collection'): {{$collection->name}}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="ctn-wrapper">
        <main>
            <section class="explanation">
                <div class="container">
                    <div class="row ">
                        <div class="col-md-4 col-lg-3">
                            <h2 class="explanation__ttl explanation__ttl--deco explanation__ttl--light">
                                @lang('translations.oral_histories')
                            </h2>
                            @include('user.includes.profile')
                        </div>
                        <div class="col-md-8">
                            <dl class="info-grid">
                                <dt>ID:</dt>
                                <dd>{{$result->id}}</dd>
                                @if(isset($result->description) && !empty($result->description))
                                    <dt>@lang('translations.description'):</dt>
                                    <dd class="html-description">
                                        {!! $result->description !!}
                                    </dd>
                                @endif
                                @if(isset($result->dates_from_to) && !empty($result->dates_from_to))
                                    <dt>@lang('translations.date'):</dt>
                                    <dd>
                                        {{$result->dates_from_to}}
                                    </dd>
                                @endif
                                @if(!empty($collection))
                                    <dt>@lang('translations.collection'):</dt>
                                    <dd>
                                        <a href="{{url('collections/' . $collection->id.'/interviews')}}">
                                            {{$collection->name}}
                                        </a>
                                    </dd>
                                @endif
                            </dl>
                            @if($allow && !empty($result->pdf))
                                <div class="iframe-container">
                                    <embed src="https://drive.google.com/viewerng/viewer?embedded=true&url={{$result->pdf}}" height="800">
                                </div>
                            @endif
                            @if(isset($related) && !empty($related))
                                <div class="related-interview">
                                    <h2 class="section-title">
                                        @lang('translations.linked_objects')
                                    </h2>
                                    @foreach($related as $key => $relatedItem)
                                        <article class="col-md-6 post-itm grid-item interview-itm">
                                            @if($relatedItem->name)
                                                <strong class="post-itm__ttl">{{$relatedItem->name}}</strong>
                                            @endif
                                            @if($relatedItem->description)
                                                <div class="interview-itm__desc">
                                                    {{$relatedItem->description}}
                                                </div>
                                            @endif
                                            @if($relatedItem->date)
                                                <span>@lang('translations.interview_date'): {{$relatedItem->date}}</span>
                                            @endif
                                            <div class="ta-right">
                                                <a href="{{url('interviews/'. $relatedItem->id)}}" class="btn btn--more">@lang('translations.more_details')</a>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="col-lg-1">
                            @include('includes.share')
                        </div>
                    </div>
                </div>
            </section>
        </main>
        <script>
            let langErrorRetype = 'Поле підтвердження пароля не є ідентичним з полем пароль!';
            let langError = 'Виникла помилка, спробуйте знову!';
            let langErrorLogin = 'Неправильний логін або пароль!';
            let langErrorEmail = 'Даний email вже зареєстрований, <a href="#" class="restore-password" data-place="login">ви можете відновити пароль</a>?';
            let langErrorIncorrect = 'Login or password is incorrect';
            let langSuccessRegistration = 'Thank you for registration, you can sigh in with form below';
            let langSuccessRestore = 'The letter with instruction was sent on current email!';
        </script>
    </div>
@endsection
@section('js-libs')
    <script src="{{asset('js/zoom.min.js')}}"></script>
@endsection