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
                        <div class="col-md-9">
                            @php $count = 0; @endphp
                            @if(!empty($accounts) && count($accounts) > 0)
                                <div class="accounts-body">
                                    <div class="accounts-item head">
                                        <div class="accounts-item__id">ID</div>
                                        <div class="accounts-item__user">@lang('translations.user')</div>
                                        <div class="accounts-item__access">@lang('translations.access_to')</div>
                                        <div class="accounts-item__button"></div>
                                    </div>
                                    @foreach($accounts as $account)
                                        <div id="account-{{$account->id}}" class="accounts-item" data-id="{{$account->id}}">
                                            <div class="accounts-item__id">{{$account->id}}</div>
                                            <div class="accounts-item__user">
                                                <strong>{{$account->name}}</strong>
                                                <span>{{$account->email}}</span>
                                            </div>
                                            <div class="accounts-item__access">{{$account->status}}</div>
                                            <div class="accounts-item__button">
                                                <a href="#" class="btn btn--ico icon-arrow-r2 show-details" data-id="{{$account->id}}" data-show="@lang('translations.more_details')" data-hide="@lang('translations.hide_details')">@lang('translations.more_details')</a>
                                            </div>
                                            <div class="accounts-item__hidden">
                                                @php $access = isset($account->access) && is_array($account->access) && count($account->access) > 0 ? $account->access[0] : false; @endphp
                                                @if($access)
                                                    <div class="accounts-item__hidden-item">
                                                        <strong>@lang('translations.status'):</strong>
                                                        <span>{{$access->status}}</span>
                                                    </div>
                                                    @if(!empty($access->date_from) && !empty($access->date_to))
                                                        <div class="accounts-item__hidden-item">
                                                            <strong>@lang('translations.to_date'):</strong>
                                                            <span>{{$access->date_from}} - {{$access->date_to}}</span>
                                                        </div>
                                                    @endif
                                                    <div class="accounts-item__hidden-item border">
                                                        <strong>@lang('translations.comment'):</strong>
                                                        <span>{{$access->description}}</span>
                                                    </div>
                                                    <form id="form-add-access" class="accounts-item__hidden-form">
                                                        <div class="col-md-4">
                                                            <input type="hidden" name="user-id" value="{{$account->id}}">
                                                            <strong>@lang('translations.access_to_date'):</strong>
                                                            <button type="submit" class="btn">@lang('translations.allow')</button>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="user-date-start" style="display:none">@lang('translations.start_date')</label>
                                                            <input type="date" id="user-date-start" name="user-date-start" placeholder="@lang('translations.start_date')" required>
                                                            <a href="#" class="user-access-disable" data-id="{{$account->id}}">@lang('translations.ban')</a>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="user-date-end" style="display:none">@lang('translations.end_date')</label>
                                                            <input type="date" id="user-date-end" name="user-date-end" placeholder="@lang('translations.end_date')" required>
                                                        </div>
                                                        <span class="form-message-{{$account->id}}" style="display:none"></span>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                        {{--<div class="accounts-item">--}}
                                            {{--<div class="accounts-item__title">--}}
                                                {{--<strong>#{{$account->id}}:</strong> {{$account->email}}--}}
                                                {{--<span></span>--}}
                                            {{--</div>--}}
                                            {{--<div class="accounts-item__hidden">--}}
                                                {{--<div>--}}
                                                    {{--<label>Role <input type="number" name="user-role" value="{{$account->role}}" min="1" max="2"></label>--}}
                                                    {{--<a href="#" class="account-change-role btn btn--ico" data-id="{{$account->id}}">Change</a>--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
            <script>
                let langError = '@lang('translations.error')';
                let langSuccess = '@lang('translations.success_change_access')';
                let langDisable = '@lang('translations.success_ban')';
            </script>
        </main>
    </div>
@endsection