@php
    $hasChildren = $category->children->isNotEmpty();
@endphp

<li class="main">
    <a class="menu-heading" href="{{ $category->getUrl() }}" title="{{ $category->translated_name }}">{{ $category->translated_name }}</a>
    @if($hasChildren)
        <div class="submenu">
            @foreach($category->children as $child)
                <a href="{{ $child->getUrl() }}" title="{{ $child->translated_name }}">{{ $child->translated_name }}</a>
            @endforeach
        </div>
    @endif
</li>