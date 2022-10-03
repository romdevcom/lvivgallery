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
                            <h1 class="visual-exposition__ttl black">@lang('translations.registration_page')</h1>
                            <form id="registration-form" class="user-form" method="POST">
                                <div class="user-form__label col-md-4">
                                    <label for="user-email">Email*:</label>
                                    <span>(@lang('translations.as_login'))</span>
                                </div>
                                <div class="user-form__input input-pen col-md-8">
                                    <input type="email" name="user-email" id="user-email" placeholder="Email" required>
                                </div>
                                <div class="clear"></div>
                                <div class="user-form__label col-md-4">
                                    <label for="user-password">@lang('translations.enter_password')*:</label>
                                </div>
                                <div class="user-form__input input-pen col-md-8">
                                    <input type="password" name="user-password" id="user-password" placeholder="@lang('translations.password')" required>
                                </div>
                                <div class="clear"></div>
                                <div class="user-form__label col-md-4">
                                    <label for="user-password-retype">@lang('translations.password_again')*:</label>
                                </div>
                                <div class="user-form__input input-pen col-md-8">
                                    <input type="password" name="user-password-retype" id="user-password-retype" placeholder="@lang('translations.password')" required>
                                </div>
                                <div class="clear"></div>
                                <div class="user-form__label col-md-4">
                                    <label for="user-first-name">@lang('translations.your_name')*:</label>
                                </div>
                                <div class="user-form__input input-pen col-md-8">
                                    <input type="text" name="user-first-name" id="user-first-name" placeholder="@lang('translations.name')" required>
                                </div>
                                <div class="clear"></div>
                                <div class="user-form__label col-md-4">
                                    <label for="user-last-name">@lang('translations.your_last_name')*:</label>
                                </div>
                                <div class="user-form__input input-pen col-md-8">
                                    <input type="text" name="user-last-name" id="user-last-name" placeholder="@lang('translations.last_name')" required>
                                </div>
                                <div class="clear"></div>
                                <div class="user-form__label col-md-4">
                                    <label for="user-date">@lang('translations.time_range')*:</label>
                                </div>
                                <div class="user-form__input input-pen col-md-8">
                                    <input type="text" name="user-date" id="user-date" placeholder="@lang('translations.time_range_example')" required>
                                </div>
                                <div class="clear"></div>
                                <div class="user-form__label col-md-4">
                                    <label for="user-description">@lang('translations.reason_to_access')*:</label>
                                </div>
                                <div class="user-form__input input-pen col-md-8">
                                    <input type="text" name="user-description" id="user-description" placeholder="@lang('translations.let_know')" required>
                                </div>
                                <div class="clear"></div>
                                <div class="user-form__label col-md-4"></div>
                                <div class="user-form__input col-md-8">
                                    <button type="submit" class="btn btn--ico icon-arrow-r2">@lang('translations.registration')</button>
                                    <span class="form-message"></span>
                                </div>
                            </form>
                            <span class="success-message" style="display:none">@lang('translations.success_registrationp')</span>
                        </div>
                    </div>
                </div>
            </section>
            <script>
                let langErrorRetype = '@lang('translations.error_reg_retype')';
                let langError = '@lang('translations.error')';
                let langErrorLogin = '@lang('translations.error_login_or_pass')';
                let langErrorEmail = '@lang('translations.error_email')';
                let langSuccessRegistration = '@lang('translations.success_registration')';
            </script>
        </main>
    </div>
@endsection
@section('js-libs')
    <script src="{{asset('js/zoom.min.js')}}"></script>
@endsection