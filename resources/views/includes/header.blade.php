<header class="header">
    <div class="limiter">
        <div class="container-fluid">
            <div class="header__wrapper">
                <a href="/" class="site-logo header__col header__col--l">
                    <img src="/img/logo-color.svg" alt="site logo">
                    <span>@lang('translations.logo_text')</span>
                </a>
                <div class="header-main header__col header__col--r">
                    <div class="header-main__holder hide-on-md">
                        <div class="header-main__scroller">
                            <ul class="header-nav">
                                <li>
                                    <a target="_blank" href="@lang('translations.menu_link_news')">
                                        @lang('translations.menu_link_news_name')
                                    </a>
                                </li>
                                <li>
                                    <a target="_blank" href="@lang('translations.menu_link_exhibitions')">
                                        @lang('translations.menu_link_exhibitions_name')
                                    </a>
                                </li>
                                <li>
                                    <a target="_blank" href="@lang('translations.menu_link_museums')">
                                        @lang('translations.menu_link_museums_name')
                                    </a>
                                </li>
                                <li class="active">
                                    <a href="/">
                                        @lang('translations.collection')
                                    </a>
                                </li>
                                <li>
                                    <a target="_blank" href="@lang('translations.menu_link_library')">
                                        @lang('translations.menu_link_library_name')
                                    </a>
                                </li>
                                <li>
                                    <a target="_blank" href="@lang('translations.menu_link_information')">
                                        @lang('translations.menu_link_information_name')
                                    </a>
                                </li>
                                <li>
                                    <a target="_blank" href="@lang('translations.menu_link_catalogs')">
                                        @lang('translations.menu_link_catalogs_name')
                                    </a>
                                </li>
                                <li>
                                    <a target="_blank" href="@lang('translations.menu_link_archives')">
                                        @lang('translations.menu_link_archives_name')
                                    </a>
                                </li>
                                <li>
                                    <a target="_blank" href="@lang('translations.menu_link_public')">
                                        @lang('translations.menu_link_public_name')
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    @if(isset($url))
                        @php $translation = lang_code() == 15 ? str_replace('/uk', '/en', $url) : str_replace('/en', '/uk', $url); @endphp
                    @else
                        @php $translation = '/'; @endphp
                    @endif
                    @php $opposite = lang_code() == 15 ? 'EN' : 'UK'; @endphp
                    <a href="{{$translation}}" class="site-lng">
                        {{$opposite}}
                    </a>
                </div>
                <button class="menu-opener show-on-md">
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </div>
</header>