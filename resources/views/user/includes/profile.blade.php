<div class="interview-user">
    @if(!empty($user))
        <div class="user-profile">
            <h3 class="title-light">@lang('translations.my_profile')</h3>
            <h3 class="title">{{$user->name}}</h3>
            <div class="half-link">
                <a href="{{url('user')}}">@lang('translations.edit_access')</a>
                <a href="{{url('user/change')}}" class="light">@lang('translations.change_password')</a>
            </div>
            <a href="#" class="btn btn--ico icon-arrow-r2 log-out">@lang('translations.log_out')</a>
        </div>
    @else
        <h3 class="title">@lang('translations.log_in_window')</h3>
        <form id="login-form" class="user-form interview-form">
            <label for="user-email" style="display:none">Email</label>
            <div class="input-pen">
                <input type="email" name="user-email" id="user-email" placeholder="Email" required>
            </div>
            <label for="user-password" style="display:none">@lang('translations.password')</label>
            <div class="input-pen">
                <input type="password" name="user-password" id="user-password" placeholder="@lang('translations.password')" required>
            </div>
            <button type="submit" class="btn btn--ico icon-arrow-r2">@lang('translations.enter_to')</button>
            <span class="form-message" style="display:none"></span>
        </form>
        <a href="{{url('user/restore')}}" class="title-light forget-password">@lang('translations.forget_password')</a>
        <a href="{{url('user/registration')}}" class="btn btn--ico icon-arrow-r2">@lang('translations.registration')</a>
    @endif
</div>