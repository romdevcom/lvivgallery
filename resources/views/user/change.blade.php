@extends('layouts.main')
@section('content')
    <div class="ctn-wrapper">
        <main>
            <section class="explanation">
                <div class="container">
                    <div class="row ">
                        <div class="col-md-3">
                            <h2 class="explanation__ttl explanation__ttl--deco explanation__ttl--light">
                                @lang('translations.oral_histories')
                            </h2>
                            @include('user.includes.simple_profile')
                        </div>
                        <div class="col-md-9 register-container">
                            <h1 class="visual-exposition__ttl black">@lang('translations.change_password_page')</h1>
                            <form id="change-password-form" class="user-form" method="POST">
                                <div class="user-form__label col-md-4">
                                    <label for="user-old-password">@lang('translations.old_password')*:</label>
                                </div>
                                <div class="user-form__input input-pen col-md-8">
                                    <input type="password" name="user-old-password" id="user-old-password" placeholder="@lang('translations.password')" required>
                                </div>
                                <div class="clear"></div>
                                <div class="user-form__label col-md-4">
                                    <label for="user-new-password">@lang('translations.new_password')*:</label>
                                </div>
                                <div class="user-form__input input-pen col-md-8">
                                    <input type="password" name="user-new-password" id="user-new-password" placeholder="@lang('translations.password')" required>
                                </div>
                                <div class="clear"></div>
                                <div class="user-form__label col-md-4">
                                    <label for="user-new-password-retype">@lang('translations.new_password_again')*:</label>
                                </div>
                                <div class="user-form__input input-pen col-md-8">
                                    <input type="password" name="user-new-password-retype" id="user-new-password-retype" placeholder="@lang('translations.password')" required>
                                </div>
                                <div class="clear"></div>
                                <div class="user-form__label col-md-4"></div>
                                <div class="user-form__input col-md-8">
                                    <button type="submit" class="btn btn--ico icon-arrow-r2">@lang('translations.registration')</button>
                                </div>
                            </form>
                            <span class="form-message"></span>
                        </div>
                    </div>
                </div>
            </section>
            <script>
                let langErrorRetype = '@lang('translations.error')';
                let langErrorOld = '@lang('translations.error')';
                let langError = '@lang('translations.error')';
                let langSuccess = '@lang('translations.error')';
            </script>
        </main>
    </div>
@endsection
@section('js-libs')
    <script src="{{asset('js/zoom.min.js')}}"></script>
@endsection