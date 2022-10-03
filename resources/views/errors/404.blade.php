@extends('layouts.main')
@section('content')
    <main class="pt20px pt30px-m error-404">
        <div class="container">
            <div class="row">
                <p>@lang('translations.oops')</p>
                <a href="/" class="msf-actions__btn msf-smb">@lang('translations.to_main_page')</a>
            </div>
        </div>
    </main>
@endsection