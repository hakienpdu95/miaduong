<div class="my-4 page-header-breadcrumb d-flex align-items-center justify-content-between flex-wrap gap-2 {{ $customStyle ?? ''}}">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                @forelse ($breadcrumbs ?? [] as $index => $breadcrumb)
                    @if ($index === count($breadcrumbs) - 1)
                        <li class="breadcrumb-item active" aria-current="page">
                            {!! $customIcons[$breadcrumb['url']] ?? '' !!}
                            {{ __($breadcrumb['title']) }}
                        </li>
                    @else
                        <li class="breadcrumb-item">
                            <a href="{{ $breadcrumb['url'] }}">
                                {!! $customIcons[$breadcrumb['url']] ?? '' !!}
                                {{ __($breadcrumb['title']) }}
                            </a>
                        </li>
                    @endif
                @empty
                    <li class="breadcrumb-item active">Home</li>
                @endforelse
            </ol>
        </nav>
    </div>
</div>