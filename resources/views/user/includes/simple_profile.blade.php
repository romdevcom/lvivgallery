<div class="interview-user">
    @if(!empty($user))
        <div class="user-profile">
            <h3 class="title-light">@lang('translations.my_profile')</h3>
            <h3 class="title">{{$user->name}}</h3>
            <div class="half-link">
                <a href="{{url('user')}}">@lang('translations.edit_access')</a>
                <a href="{{url('user/restore')}}" class="light">@lang('translations.change_password')</a>
            </div>
            <a href="#" class="btn btn--ico icon-arrow-r2 log-out">@lang('translations.log_out')</a>
        </div>
    @else
        <h3 class="title">@lang('translations.my_profile')</h3>
        <a href="{{url('user/login')}}" class="btn btn--ico icon-arrow-r2">@lang('translations.enter_to')</a>
        <a href="{{url('user/restore')}}" class="title-light forget-password">@lang('translations.forget_password')</a>
        <a href="{{url('user/registration')}}" class="btn btn--ico icon-arrow-r2">@lang('translations.registration')</a>
    @endif
</div>