@extends('layouts.main')
@section('content')
    <div class="ctn-wrapper">
        <main>
            <section class="explanation explanation--multiple">
                <div class="container">
                    <div class="row">
                        <div class="col-md-4 col-lg-3">
                            <h1 class="explanation__ttl explanation__ttl--deco explanation__ttl--light">
                                @lang('translations.faq')
                            </h1>
                        </div>
                        <div class="col-md-8 col-lg-9">
                            <h3 id="faq-1" class="faq-title">@lang('translations.faq_1')</h3>
                            <div class="cms-editor">
                                @lang('translations.faq_answer_full_1')
                            </div>
                            <h3 id="faq-2" class="faq-title">@lang('translations.faq_2')</h3>
                            <div class="cms-editor">
                                @lang('translations.faq_answer_full_2')
                            </div>
                            <h3 id="faq-3" class="faq-title">@lang('translations.faq_3')</h3>
                            <div class="cms-editor">
                                @lang('translations.faq_answer_full_3')
                            </div>
                            <h3 id="faq-4" class="faq-title">@lang('translations.faq_4')</h3>
                            <div class="cms-editor">
                                @lang('translations.faq_answer_full_4')
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
@endsection