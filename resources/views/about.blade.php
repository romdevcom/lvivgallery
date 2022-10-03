@extends('layouts.main')
@section('content')
    <article class="explanation explanation--grey explanation--hero">
        <div class="container">
            <div class="row ">
                <div class="col-md-4 col-lg-3">
                    <h1 class="explanation__ttl explanation__ttl--deco">
                        @lang('translations.urban')
                        <br>
                        @lang('translations.media_archive')
                    </h1>
                </div>
                <div class="col-md-8 col-lg-9">
                    <div class="cms-editor">
                        @lang('translations.about_text_p_1')
                    </div>
                </div>
            </div>
        </div>
    </article>
    <div class="ctn-wrapper">
        <main>
            <section class="explanation explanation--multiple">
                <div class="container">
                    <div class="row">
                        <div class="col-md-4 col-lg-3">
                            <h2 class="explanation__ttl explanation__ttl--deco explanation__ttl--light">
                                @lang('translations.structure')
                            </h2>
                        </div>
                        <div class="col-md-8 col-lg-9">
                            <div class="cms-editor">
                                @lang('translations.about_text_p_2')
                                <section class="choice">
                                    <p class="mb-15">@lang('translations.collection_formatted')</p>
                                    @include('includes.header_tabs')
                                </section>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-lg-3">
                            <h2 class="explanation__ttl explanation__ttl--deco explanation__ttl--light">
                                @lang('translations.prioritets')
                            </h2>
                        </div>
                        <div class="col-md-8 col-lg-9">
                            <div class="cms-editor">
                                @lang('translations.about_text_p_3')
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-lg-3">
                            <h2 class="explanation__ttl explanation__ttl--deco explanation__ttl--light">
                                @lang('translations.researches')
                            </h2>
                        </div>
                        <div class="col-md-8 col-lg-9">
                            <div class="cms-editor">
                                @lang('translations.about_text_p_4')
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-lg-3">
                            <h2 class="explanation__ttl explanation__ttl--deco explanation__ttl--light">
                                @lang('translations.access')
                            </h2>
                        </div>
                        <div class="col-md-8 col-lg-9">
                            <div class="cms-editor">
                                @lang('translations.about_text_p_5')
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-lg-3">
                            <h2 class="explanation__ttl explanation__ttl--deco explanation__ttl--light">
                                @lang('translations.mediaarchive')
                            </h2>
                        </div>
                        <div class="col-md-8 col-lg-9">
                            <div class="cms-editor">
                                @lang('translations.about_text_p_6')
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-lg-3">
                            <h2 class="explanation__ttl explanation__ttl--deco explanation__ttl--light">
                                @lang('translations.mediateka')
                            </h2>
                        </div>
                        <div class="col-md-8 col-lg-9">
                            <div class="cms-editor">
                                @lang('translations.about_text_p_7')
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-lg-3">
                            <h2 class="explanation__ttl explanation__ttl--deco explanation__ttl--light">
                                @lang('translations.umovy')
                            </h2>
                        </div>
                        <div class="col-md-8 col-lg-9">
                            <div class="cms-editor">
                                @lang('translations.about_text_p_8')
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-lg-3">
                            <h2 class="explanation__ttl explanation__ttl--deco explanation__ttl--light">
                                @lang('translations.collaboration')
                            </h2>
                        </div>
                        <div class="col-md-8 col-lg-9">
                            <div class="cms-editor">
                                @lang('translations.about_text_p_9')
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
@endsection