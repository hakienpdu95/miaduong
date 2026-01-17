@php
    // Giả định $category và $parent được truyền từ CategoryView
    $mainCategory = $parent ?? $category; // Danh mục chính (luôn là cha hoặc gốc)
    $currentSlug = $category->slug; // Slug hiện tại để xác định active

    // Lấy danh mục con của $mainCategory
    $siblings = $mainCategory->children()->where('is_active', true)->orderBy('order')->take(4)->get();

    // Nếu $category không phải là $mainCategory và không nằm trong $siblings, thay thế mục cuối
    if ($category->id !== $mainCategory->id && !$siblings->contains('slug', $currentSlug)) {
        $siblings = $siblings->take(3); // Lấy 3 mục đầu
        $siblings->push($category); // Thêm danh mục hiện tại vào cuối
    }
@endphp

<div class="breadcrumb">
    <h1 class="main mb-0">
        <a href="{{ $mainCategory->getUrl() }}" title="{{ $mainCategory->translated_name }}">{{ $mainCategory->translated_name }}</a>
    </h1>
    @if($siblings->isNotEmpty())
        <div class="sub">
            @foreach($siblings as $sibling)
                @if($sibling->slug === $currentSlug)
                    <h1 class="mb-0">
                        <a class="active" href="{{ $sibling->getUrl() }}" title="{{ $sibling->translated_name }}">{{ $sibling->translated_name }}</a>
                    </h1>
                @else
                    <a href="{{ $sibling->getUrl() }}" title="{{ $sibling->translated_name }}">{{ $sibling->translated_name }}</a>
                @endif
            @endforeach
        </div>
    @endif
</div>