<aside class="app-sidebar sticky" id="sidebar">
    <div class="main-sidebar-header">
        <a href="#" class="header-logo">
            <img src="{{ asset('assets/images/brand-logos/desktop-logo.png') }}" alt="logo" class="desktop-logo">
            <img src="{{ asset('assets/images/brand-logos/toggle-dark.png') }}" alt="logo" class="toggle-dark">
            <img src="{{ asset('assets/images/brand-logos/desktop-dark.png') }}" alt="logo" class="desktop-dark">
            <img src="{{ asset('assets/images/brand-logos/toggle-logo.png') }}" alt="logo" class="toggle-logo">
        </a>
    </div>
    <div class="main-sidebar simplebar-scrollable-y" id="sidebar-scroll">
        <div class="simplebar-wrapper">
            <div class="simplebar-height-auto-observer-wrapper">
                <div class="simplebar-height-auto-observer"></div>
            </div>
            <div class="simplebar-mask">
                <div class="simplebar-offset">
                    <div class="simplebar-content-wrapper">
                        <div class="simplebar-content">
                            <nav class="main-menu-container nav nav-pills flex-column sub-open">
                                <div class="slide-left active d-none" id="slide-left">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24"> <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path> </svg>
                                </div>
                                <ul class="main-menu">
                                    <!-- Dashboard -->
                                    <li class="slide__category"><span class="category-name">{{ __('Dashboard') }}</span></li>
                                    <li class="slide {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                        <a href="#" class="side-menu__item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                            <i class="fa-light fa-house me-2"></i>
                                            <span class="side-menu__label">{{ __('Dashboard') }}</span>
                                        </a>
                                    </li>

                                    <!-- Dynamic Categories and Modules -->
                                    @foreach ($categories ?? [] as $category)
                                        <li class="slide__category"><span class="category-name">{{ __($category['label']) }}</span></li>
                                        @foreach ($category['modules'] as $module)
                                            <li class="slide has-sub {{ request()->routeIs($module['name'] . '.*') ? 'active open' : '' }}">
                                                <a href="javascript:void(0);" class="side-menu__item {{ request()->routeIs($module['name'] . '.*') ? 'active' : '' }}">
                                                    {!! $module['icon'] !!}
                                                    <span class="side-menu__label">{{ __($module['label']) }}</span>
                                                    <i class="fa-light fa-angle-right side-menu__angle"></i>
                                                </a>
                                                <ul class="slide-menu child1 {{ request()->routeIs($module['name'] . '.*') ? 'active' : '' }}"
                                                    style="display: {{ request()->routeIs($module['name'] . '.*') ? 'block' : 'none' }};"
                                                    data-popper-placement="bottom">
                                                    <li class="slide side-menu__label1">
                                                        <a href="javascript:void(0)">{{ __($module['label']) }}</a>
                                                    </li>
                                                    @foreach ($module['children'] as $child)
                                                        <li class="slide {{ request()->routeIs($child['name']) || request()->routeIs($child['name'] . '.*') ? 'active' : '' }}">
                                                            <a href="{{ route($child['name']) }}" class="side-menu__item {{ request()->routeIs($child['name']) || request()->routeIs($child['name'] . '.*') ? 'active' : '' }}">
                                                                {{ __($child['label']) }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @endforeach
                                    @endforeach
                                </ul>
                                <div class="slide-right d-none" id="slide-right">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
                                        <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path>
                                    </svg>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="simplebar-placeholder"></div>
        </div>
    </div>
</aside>
