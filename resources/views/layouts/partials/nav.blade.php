@php
    $categories = App\Models\Category::getMenuTree();
@endphp

<nav class="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement" id="genesis-nav-primary" aria-label="Main navigation">
    <ul class="container menu-wrap">
        <li class="main home {{ request()->routeIs('home') ? 'active' : '' }}">
            <a class="menu-heading" href="{{ route('home') }}" title="Trang chủ">
                <i class="ic-home"></i>
            </a>
        </li>

        @foreach($categories as $category)
            @include('layouts.partials.menu-item', ['category' => $category])
        @endforeach

        <li class="main search"></li>

        <li class="main menu">
            <div class="menu-btn">
                <i class="ic-menu"></i>
            </div>
            <div class="dropdown-menu">
                <div class="container mega-menu">
                    @foreach($categories as $category)
                        <div class="menu-col">
                            <a href="{{ $category->getUrl() }}" title="{{ $category->translated_name }}">{{ $category->translated_name }}</a>
                            @foreach($category->children as $child)
                                <a href="{{ $child->getUrl() }}" title="{{ $child->translated_name }}">{{ $child->translated_name }}</a>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </li>
    </ul>
</nav>