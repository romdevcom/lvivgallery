@extends('layouts.main')
@section('content')
    <div class="ctn-wrapper">
        <main>
            <section class="explanation">
                <div class="container">
                    <div class="row ">
                        <div class="col-md-4 col-lg-3">
                            <h2 class="explanation__ttl explanation__ttl--deco explanation__ttl--light">
                                @lang('translations.oral_histories')
                            </h2>
                            @include('user.includes.simple_profile')
                        </div>
                        <div class="col-md-9">
                            <div class="col-md-12">
                                <h1 class="visual-exposition__ttl black">@lang('translations.change_access_page')</h1>
                                <div class="request-body">
                                    <form id="date-form" class="user-form" method="POST">
                                        <div class="clear"></div>
                                        <div class="user-form__label col-md-4">
                                            <label for="user-date">@lang('translations.time_range')*:</label>
                                        </div>
                                        <div class="user-form__input input-pen col-md-8">
                                            <input type="text" name="user-date" id="user-date" placeholder="@lang('translations.time_range_example')"
                                                @if($accesses && count($accesses) > 0) value="{{$accesses[0]->date}}" @else required @endif
                                            >
                                        </div>
                                        <div class="clear"></div>
                                        <div class="user-form__label col-md-4">
                                            <label for="user-description">@lang('translations.reason_to_access')*:</label>
                                        </div>
                                        <div class="user-form__input input-pen col-md-8">
                                            <input type="text" name="user-description" id="user-description" placeholder="@lang('translations.let_know')"
                                                   @if($accesses && count($accesses) > 0) value="{{$accesses[0]->description}}" @else required @endif
                                            >
                                        </div>
                                        <div class="clear"></div>
                                        <div class="user-form__label col-md-4"></div>
                                        <div class="user-form__input col-md-8">
                                            <button type="submit" class="btn btn--ico icon-arrow-r2">@lang('translations.make_request')</button>
                                        </div>
                                    </form>
                                    <span class="form-message"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <script>
                let langError = '@lang('translations.error')';
                let langSuccess = '@lang('translations.success_request')';
            </script>
        </main>
    </div>
@endsection