<?php

namespace App\Http\Livewire\Frontend\Homepage;

use Livewire\Component;
use App\Http\Livewire\Traits\WithGlideSlider;
use App\Models\Section;
use Illuminate\Support\Facades\Log;

class HighlightSlider extends Component
{
    use WithGlideSlider;

    public $sectionType = 'highlight_slider'; // Type mặc định cho section
    public $highlightPosts = []; // Public property cho data động từ DB

    public function mount($sectionType = null)
    {
        if ($sectionType) {
            $this->sectionType = $sectionType;
        }

        Log::info('HighlightSlider mount called', ['sectionType' => $this->sectionType]);

        // Gọi trait mount với config cơ bản
        $this->mountWithGlide([
            'perView' => 1,
            'autoplay' => false,
            'arrows' => true,
            'bullets' => true,
        ]);
    }

    // Method load data động từ DB (tương tự StickyPosts)
    private function loadHighlightPosts()
    {
        Log::info('loadHighlightPosts started');

        $section = Section::where('type', $this->sectionType)
            ->whereNull('deleted_at')
            ->first();

        if (!$section) {
            Log::warning('Section not found', ['type' => $this->sectionType]);
            $this->highlightPosts = collect();
            return;
        }

        // Gọi trực tiếp getItems() từ model
        $this->highlightPosts = $section->getItems()->unique('id');

        Log::info('Fetched posts count from getItems', [
            'count' => $this->highlightPosts->count(),
            'post_ids' => $this->highlightPosts->pluck('id')
        ]);
    }

    // Placeholder cho lazy loading
    public function placeholder()
    {
        Log::info('HighlightSlider placeholder rendered');

        return <<<'HTML'
        <div class="container">
            <p>Đang tải slider highlight...</p>
            <!-- Có thể thêm skeleton cho UX -->
        </div>
        HTML;
    }

    public function render()
    {
        // Force load data nếu chưa
        if (empty($this->highlightPosts)) {
            $this->loadHighlightPosts();
        }

        // Dynamic adjust config để tránh clone duplicate nếu ít slides
        $postCount = $this->highlightPosts->count();
        if ($postCount < 2) {
            $this->glideConfig['type'] = 'slider'; // Chuyển sang slider để tránh carousel clone
            $this->glideConfig['rewind'] = false;
            $this->glideConfig['bound'] = false;
            $this->glideConfig['autoplay'] = false; // Optional: Tắt autoplay nếu chỉ 1 slide
            $this->glideConfig['arrows'] = false; // Optional: Ẩn arrows nếu không cần
            $this->glideConfig['bullets'] = false; // Optional: Ẩn bullets
        }

        Log::info('HighlightSlider render called', [
            'has_posts' => $postCount > 0,
            'adjusted_config' => $this->glideConfig // Log config để debug
        ]);

        // Emit refresh nếu data thay đổi (update slider với config mới)
        $this->dispatch('refresh-slider-' . $this->sliderId);

        return view('livewire.frontend.homepage.highlight-slider');
    }
}



------------------------------------

<div class="section-highlight">
    <div class="container">
        <div id="{{ $sliderId }}" class="glide-slider position-relative" data-config="{{ json_encode($glideConfig) }}" wire:ignore.self>
            <div class="glide__track" data-glide-el="track">
                <ul class="glide__slides">
                    @if (!isset($highlightPosts) || $highlightPosts->isEmpty())
                        <li class="glide__slide">
                            <p>Không có bài post highlight nào.</p>
                        </li>
                    @else
                        @foreach ($highlightPosts as $post)
                            <li class="glide__slide">
                                <div class="row post-card highlight-card">
                                    <div class="col-lg-8">
                                        <div class="post-thumbnail ratio ratio-16x9">
                                            <a href="{{ route('pages.show', $post->slug) }}" title="{{ $post->title }}">
                                                <img width="1200" height="630" src="{{url($post->getThumbnailUrl('585x420'))}}" class="img-full wp-post-image lazyautosizes lazyloaded" alt="{{ $post->title }}" title="{{ $post->title }}" loading="lazy" style="width: 100%;">
                                            </a>
                                            <div class="favorite" id="{{ $post->id }}" data-cateid="{{ $post->categories->first()?->id ?? '' }}" data-catename="{{ $post->categories->first()?->name ?? 'Uncategorized' }}" data-web="AMARINBABYANDKIDS">
                                                <i class="fa-light fa-heart"></i> <span>Like</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 meta-box">
                                        <h3 class="entry-title mb-2">
                                            <a href="{{ route('pages.show', $post->slug) }}" title="{{ $post->title }}">{{ $post->title }}</a>
                                        </h3>
                                        <p class="excerpt">{{ $post->excerpt ?? 'No description available.' }}</p>
                                        <div class="meta-info">
                                            <a class="meta-item category" href="{{ $post->categories->first()?->getUrl() ?? '/category' }}" title="{{ $post->categories->first()?->name ?? 'Uncategorized' }}">{{ $post->categories->first()?->name ?? 'Uncategorized' }}</a>
                                            <span class="posted-on create-date">
                                                <time class="entry-date published updated" datetime="{{ $post->created_at->toDateTimeString() }}">{{ $post->created_at->format('F j, Y') }}</time>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>

            @if ($glideConfig['arrows'] ?? false)
                <div class="glide__arrows" data-glide-el="controls">
                    <button class="glide__arrow glide__arrow--left" data-glide-dir="<">prev</button>
                    <button class="glide__arrow glide__arrow--right" data-glide-dir=">">next</button>
                </div>
            @endif

            @if ($glideConfig['bullets'] ?? false)
            <div class="glide__bullets" data-glide-el="controls[nav]">
                @foreach ($highlightPosts as $index => $post)
                    <button class="glide__bullet" data-glide-dir="={{ $index }}"></button>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            function initGlideSlider(id) {
                const el = document.getElementById(id);
                if (el && !el.glideInstance) {
                    const config = JSON.parse(el.dataset.config);
                    el.glideInstance = new Glide('#' + id, config).mount();
                }
            }

            // Init sau navigated hoặc DOM load
            document.addEventListener('livewire:navigated', () => initGlideSlider('{{ $sliderId }}'));
            document.addEventListener('DOMContentLoaded', () => initGlideSlider('{{ $sliderId }}'));

            // Wrapper cho Livewire.on để đảm bảo Livewire ready, event name unique per slider
            document.addEventListener('livewire:init', () => {
                Livewire.on('refresh-slider-{{ $sliderId }}', () => {
                    const instance = document.getElementById('{{ $sliderId }}')?.glideInstance;
                    if (instance) instance.update();
                });
            });
        </script>
    @endpush    
</div>
