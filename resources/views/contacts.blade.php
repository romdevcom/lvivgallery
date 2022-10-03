@extends('layouts.main')
@section('content')
    <article class="explanation explanation--dark explanation--hero explanation--big">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-lg-3">
                    <h1 class="explanation__ttl explanation__ttl--deco">
                        @lang('translations.title_contacts')
                    </h1>
                </div>
                <div class="col-md-8 col-lg-9">
                    <div class="contacts">
                        <section class="contacts__block">
                            <div class="row contacts__row">
                                <div class="col-md-4 contacts__col">
                                    <b>@lang('translations.email'):</b>
                                </div>
                                <div class="col-md-3 contacts__col">
                                    <a href="mailto:uma@lvivcenter.org">uma@lvivcenter.org</a>
                                </div>
                                <div class="w-100"></div>
                                <div class="col-md-4 contacts__col">
                                    <b>@lang('translations.phone_number'):</b>
                                </div>
                                <div class="col-md-3 contacts__col">
                                    <a href="tel:+380322751734">+38-032 275-17-34</a>
                                </div>
                            </div>
                        </section>
                        <section class="contacts__block">
                            <div class="row contacts__row">
                                <div class="col-md-4 contacts__col">
                                    <b>@lang('translations.address'):</b>
                                </div>
                                <div class="col-md-6 contacts__col">
                                    <p>@lang('translations.contact_address')</p>
                                </div>
                                <div class="w-100"></div>
                                <div class="col-md-4 contacts__col">
                                    <b>@lang('translations.ofline_hours')</b>
                                </div>
                                <div class="col-md-6 contacts__col">
                                    <p>@lang('translations.ofline_hours_details')</p>
                                </div>
                            </div>
                        </section><!-- /contacts__block -->

                        <section class="contacts__block">
                            <div class="row contacts__row">
                                <div class="col-md-4 contacts__col">
                                    <b>@lang('translations.coordiantor')</b>:
                                </div>
                                <div class="col-md-3 contacts__col">
                                    <p>@lang('translations.oleksandr')</p>
                                </div>
                                <div class="col-md-3 contacts__col">
                                    <a href="mailto:o.makhanets@lvivcenter.org">o.makhanets@lvivcenter.org</a>
                                </div>
                                <div class="w-100"></div>
                                <div class="col-md-4 contacts__col">
                                    <b>@lang('translations.kerivnyca')</b>:
                                </div>
                                <div class="col-md-3 contacts__col">
                                    <p>@lang('translations.natalia')</p>
                                </div>
                                <div class="col-md-3 contacts__col">
                                    <a href="mailto:n.otrischenko@lvivcenter.org">n.otrischenko@lvivcenter.org</a>
                                </div>
                                <div class="w-100"></div>
                                <div class="col-md-4 contacts__col">
                                    <b>@lang('translations.konsultant')</b>:
                                </div>
                                <div class="col-md-3 contacts__col">
                                    <p>@lang('translations.bohdan')</p>
                                </div>
                                <div class="col-md-3 contacts__col">
                                    <a href="mailto:b.shumylovych@lvivcenter.org">b.shumylovych@lvivcenter.org</a>
                                </div>
                                <div class="w-100"></div>
                                <div class="col-md-4 contacts__col">
                                    <b>@lang('translations.project_assistant')</b>:
                                </div>
                                <div class="col-md-3 contacts__col">
                                    <p>@lang('translations.anastasia')</p>
                                </div>
                                <div class="col-md-3 contacts__col">
                                    <a href="mailto:a.kholyavka@lvivcenter.org">a.kholyavka@lvivcenter.org</a>
                                </div>
                                <div class="w-100"></div>
                                <div class="col-md-4 contacts__col">
                                    <b>@lang('translations.digitizing_assistant')</b>:
                                </div>
                                <div class="col-md-3 contacts__col">
                                    <p>@lang('translations.regina')</p>
                                </div>
                                <div class="col-md-3 contacts__col">
                                    <a href="mailto:r.zhelezniakova@lvivcenter.org">r.zhelezniakova@lvivcenter.org</a>
                                </div>
                            </div>
                        </section><!-- /contacts__block -->
                    </div>
                </div>
            </div>
            <!--<hr>-->
        </div>
    </article>
@endsection