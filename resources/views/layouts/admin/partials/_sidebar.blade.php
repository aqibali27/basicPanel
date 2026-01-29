<div id="sidebarMain" class="d-none">
    <aside
        class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered">
        <div class="navbar-vertical-container text-capitalize">
            <div class="navbar-vertical-footer-offset">
                <div class="navbar-brand-wrapper justify-content-between">

                    @php($platform_logo=\App\Model\BusinessSetting::where(['key'=>'logo'])->first()->value)
                    <a class="navbar-brand" href="{{route('admin.dashboard')}}" aria-label="Front">
                        <img class="navbar-brand-logo" style="object-fit: contain;"
                             onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'"
                             src="{{asset('storage/app/public/platform/'.$platform_logo)}}"
                             alt="Logo">
                        <img class="navbar-brand-logo-mini" style="object-fit: contain;"
                             onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'"
                             src="{{asset('storage/app/public/platform/'.$platform_logo)}}" alt="Logo">
                    </a>

                    <button type="button" class="js-navbar-vertical-aside-toggle-invoker navbar-vertical-aside-toggle btn btn-icon btn-xs btn-ghost-dark">
                        <i class="tio-first-page navbar-vertical-aside-toggle-short-align" data-toggle="tooltip" data-placement="right" title="" data-original-title="Collapse"></i>
                        <i class="tio-last-page navbar-vertical-aside-toggle-full-align" data-template="<div class=&quot;tooltip d-none d-sm-block&quot; role=&quot;tooltip&quot;><div class=&quot;arrow&quot;></div><div class=&quot;tooltip-inner&quot;></div></div>" data-toggle="tooltip" data-placement="right" title="" data-original-title="Expand"></i>
                    </button>

                    <div class="navbar-nav-wrap-content-left d-none d-xl-block">
                        <button type="button" class="js-navbar-vertical-aside-toggle-invoker close">
                            <i class="tio-first-page navbar-vertical-aside-toggle-short-align" data-toggle="tooltip" data-placement="right" title="" data-original-title="Collapse"></i>
                            <i class="tio-last-page navbar-vertical-aside-toggle-full-align"></i>
                        </button>
                    </div>
                </div>

                <div class="navbar-vertical-content">
                    <div class="sidebar--search-form py-3">
                        <div class="search--form-group">
                            <button type="button" class="btn"><i class="tio-search"></i></button>
                            <input type="text" class="js-form-search form-control form--control" id="search-bar-input" placeholder="Search Menu...">
                        </div>
                    </div>

                    <ul class="navbar-nav navbar-nav-lg nav-tabs">
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin')?'show':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('admin.dashboard')}}" title="{{translate('Dashboards')}}">
                                <i class="tio-home-vs-1-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{translate('dashboard')}}
                                    </span>
                            </a>
                        </li>

                        @if (Helpers::module_permission_check(MANAGEMENT_SECTION['user_management']))
                            <li
                                class="nav-item {{ Request::is('admin/employee*') || Request::is('admin/custom-role*') ? 'scroll-here' : '' }}">
                                <small class="nav-subtitle">{{ translate('user_management') }}</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>

                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/customer/transaction') || Request::is('admin/customer/list') || Request::is('admin/customer/view*') || Request::is('admin/customer/settings') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                    href="javascript:">
                                    <i class="tio-poi-user nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('customer') }}
                                    </span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{ Request::is('admin/customer/transaction') || Request::is('admin/customer/list') || Request::is('admin/customer/view*') || Request::is('admin/customer/settings') ? 'block' : '' }}; top: 831.076px;">
                                    <li
                                        class="nav-item {{ Request::is('admin/customer/list') || Request::is('admin/customer/view*') ? 'active' : '' }}">
                                        <a class="nav-link" href="{{ route('admin.customer.list') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{ translate('list') }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif


                        @if(Helpers::module_permission_check(MANAGEMENT_SECTION['order_management']))
                            <li class="nav-item">
                                <small
                                    class="nav-subtitle">{{translate('order')}} {{translate('management')}}</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>

                            <li class="navbar-vertical-aside-has-menu {{Request::is('admin/orders/list/*')?'active':''}}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                    <i class="tio-shopping-cart nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{translate('order')}}
                                    </span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{Request::is('admin/order*')?'block':'none'}}">
                                    <li class="nav-item {{Request::is('admin/orders/list/all')?'active':''}}">
                                        <a class="nav-link" href="" title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate sidebar--badge-container">
                                                <span>{{translate('all')}}</span>
                                                <span class="badge badge-soft-info badge-pill ml-1">
                                                    0
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif

                        @if(Helpers::module_permission_check(MANAGEMENT_SECTION['user_management']))
                            <li class="nav-item">
                                <small
                                    class="nav-subtitle">{{translate('user')}} {{translate('management')}}</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>
                            @if(auth('admin')->user()->admin_role_id == 1)
                                <li class="navbar-vertical-aside-has-menu {{Request::is('admin/custom-role*') || Request::is('admin/employee*')?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:" title="{{translate('Employees')}}">
                                        <i class="tio-incognito nav-icon"></i>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{translate('Employees')}}
                                        </span>
                                    </a>
                                    <ul class="js-navbar-vertical-aside-submenu nav nav-sub " style="display: {{Request::is('admin/custom-role*') || Request::is('admin/employee*')?'block':''}}">

                                        <li class="nav-item {{Request::is('admin/custom-role*')? 'active': ''}}">
                                            <a class="nav-link" href="{{route('admin.custom-role.create')}}" title="{{translate('Employee Role Setup')}}">
                                                <span class="tio-circle nav-indicator-icon"></span>
                                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                                    {{translate('Employee Role Setup')}}</span>
                                            </a>
                                        </li>

                                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/employee*')?'active':''}}">
                                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:" title="{{translate('Employee Setup')}}">
                                                <span class="tio-user mr-2"></span>
                                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                                    {{translate('Employee Setup')}}
                                                </span>
                                            </a>
                                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{Request::is('admin/employee*')?'block':''}}">
                                                <li class="nav-item {{Request::is('admin/employee/add-new')?'active':''}}">
                                                    <a class="nav-link " href="{{route('admin.employee.add-new')}}" title="{{translate('add new')}}">
                                                        <span class="tio-circle nav-indicator-icon"></span>
                                                        <span class="text-truncate">{{translate('add new')}}</span>
                                                    </a>
                                                </li>
                                                <li class="nav-item {{Request::is('admin/employee/list')?'active':''}}">
                                                    <a class="nav-link" href="{{route('admin.employee.list')}}" title="{{translate('List')}}">
                                                        <span class="tio-circle nav-indicator-icon"></span>
                                                        <span class="text-truncate">{{translate('list')}}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>

                            @endif
                        @endif

                        @if(Helpers::module_permission_check(MANAGEMENT_SECTION['system_management']))
                        <li class="nav-item">
                            <small class="nav-subtitle">{{translate('system')}} {{translate('setting')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/business-settings/store*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.business-settings.store.store-setup')}}">
                                <i class="tio-settings nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('Business_Setup')}}</span>
                            </a>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/email-setup*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"  href="{{ route('admin.business-settings.email-setup',['user','new-order']) }}">
                                <i class="tio-email nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('Email_Template')}}</span>
                            </a>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/business-settings/web-app/social-media') || Request::is('admin/business-settings/page-setup/*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-pages nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('Page & Media')}}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{Request::is('admin/business-settings/web-app/social-media') || Request::is('admin/business-settings/page-setup*')?'block':'none'}}">
                                <li class="nav-item {{Request::is('admin/business-settings/page-setup*')?'active':''}}">
                                    <a class="nav-link "
                                       href="{{route('admin.business-settings.page-setup.about-us')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('Page_Setup')}}</span>
                                    </a>
                                </li>
                                <li class="navbar-vertical-aside-has-menu {{Request::is('admin/business-settings/web-app/social-media')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.business-settings.web-app.third-party.social-media')}}"
                                       title="{{\App\CentralLogics\translate('Social Media Links')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{\App\CentralLogics\translate('Social Media')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif

                        @if(Helpers::module_permission_check(MANAGEMENT_SECTION['system_management']))
                            <li class="navbar-vertical-aside-has-menu {{Request::is('admin/business-settings/web-app/third-party*')?'active':''}}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                    <i class="tio-running nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('3rd_Party')}}</span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{Request::is('admin/business-settings/web-app/third-party*')?'block':'none'}}">
                                    <li class="nav-item {{Request::is('admin/business-settings/web-app/third-party/payment-method') || Request::is('admin/business-settings/web-app/third-party/mail-config') || Request::is('admin/business-settings/web-app/third-party/sms-module')||
                                                        Request::is('admin/business-settings/web-app/third-party/map-api-settings') || Request::is('admin/business-settings/web-app/third-party/recaptcha') ||
                                                        Request::is('admin/business-settings/web-app/third-party/social-login') || Request::is('admin/business-settings/web-app/third-party/chat')?'active':''}}">
                                        <a class="nav-link" href="{{route('admin.business-settings.web-app.payment-method')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('3rd Party Configurations')}}</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>


                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/business-settings/web-app/system-setup*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.business-settings.web-app.system-setup.language.index')}}">
                                <i class="tio-security-on-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('System Setup')}}</span>
                            </a>
                        </li>
                        @endif
                        <li class="nav-item pt-10">
                            <div class=""></div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </aside>
</div>

<div id="sidebarCompact" class="d-none">

</div>


{{--<script>
    $(document).ready(function () {
        $('.navbar-vertical-content').animate({
            scrollTop: $('#scroll-here').offset().top
        }, 'slow');
    });
</script>--}}

@push('script_2')
    <script>
        $(window).on('load' , function() {
            if($(".navbar-vertical-content li.active").length) {
                $('.navbar-vertical-content').animate({
                    scrollTop: $(".navbar-vertical-content li.active").offset().top - 150
                }, 10);
            }
        });

        //Sidebar Menu Search
        var $rows = $('.navbar-vertical-content  .navbar-nav > li');
        $('#search-bar-input').keyup(function() {
            var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();

            $rows.show().filter(function() {
                var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
                return !~text.indexOf(val);
            }).hide();
        });

    </script>
@endpush

