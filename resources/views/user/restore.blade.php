@extends('layouts.main')
@section('content')
    <div class="ctn-wrapper">
        <main>
            <section class="explanation">
                <div class="container">
                    <div class="row ">
                        <div class="col-lg-3">
                            <h2 class="explanation__ttl explanation__ttl--deco explanation__ttl--light">
                                @lang('translations.oral_histories')
                            </h2>
                            @include('user.includes.simple_profile')
                        </div>
                        <div class="col-md-9 register-container">
                            <h1 class="visual-exposition__ttl black">@lang('translations.restore_password')</h1>
                            @if($restore)
                                <form id="restore-password-form" class="user-form" method="POST">
                                    <input type="hidden" name="user-key" id="user-key" value="{{$key}}">
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
                                    <div class="user-form__label col-md-4"></div>
                                    <div class="user-form__input col-md-8">
                                        <button type="submit" class="btn btn--ico icon-arrow-r2">@lang('translations.change_password')</button>
                                    </div>
                                </form>
                            @else
                                <form id="restore-password-email-form" class="user-form" method="POST">
                                    <div class="user-form__label col-md-4">
                                        <label for="user-email">Email*:</label>
                                        <span>(@lang('translations.as_login'))</span>
                                    </div>
                                    <div class="user-form__input input-pen col-md-8">
                                        <input type="email" name="user-email" id="user-email" placeholder="Email" required>
                                    </div>
                                    <div class="clear"></div>
                                    <div class="user-form__label col-md-4"></div>
                                    <div class="user-form__input col-md-8">
                                        <button type="submit" class="btn btn--ico icon-arrow-r2">@lang('translations.send_request')</button>
                                    </div>
                                </form>
                            @endif
                            <span class="form-message"></span>
                        </div>
                    </div>
                </div>
            </section>
            <script>
                let langSuccess = '@lang('translations.success_send')На вашу пошту відправлено запит для відновлення паролю.';
                let langError = '@lang('translations.try_again') <a href="{{url('user/registration')}}">@lang('translations.registrer')</a>';
                let langErrorMail = '@lang('translations.email_not_used') <a href="{{url('user/registration')}}">@lang('translations.to_register')</a>';
                let langErrorRetype = '@lang('translations.error_reg_retype')';
                let langRestore = '@lang('translations.success_change')';
            </script>
        </main>
    </div>
@endsection