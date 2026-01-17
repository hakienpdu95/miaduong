@php
    $categories = App\Models\Category::getMenuTree();
@endphp

<div class="menu">
    <div class="menu-btn">
        <i class="ic-menu"></i>
    </div>
    <div class="dropdown-menu" style="display: none;">
        @foreach($categories as $category)
            <div class="menu-col">
                <div class="menu-heading">
                    <a href="{{ $category->getUrl() }}" title="{{ $category->translated_name }}">{{ $category->translated_name }}</a>
                    @if($category->children->isNotEmpty())
                        <i class="ic-chevron-down"></i>
                    @endif
                </div>
                @if($category->children->isNotEmpty())
                    <div class="submenu" style="display: none;">
                        @foreach($category->children as $child)
                            <a href="{{ $child->getUrl() }}" title="{{ $child->translated_name }}">{{ $child->translated_name }}</a>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach

        <div class="multi-link">
            <a href="/epaper" title="Báo in">Báo in</a>
            <a href="/video" title="Truyền hình">Truyền hình</a>
            <a href="/podcast" title="Podcast">Podcast</a>
            <a href="/emagazine" title="Emagazine">Emagazine</a>
            <a href="/anh" title="Ảnh">Ảnh</a>
            <a href="/infographic" title="Infographic">Infographic</a>
            <a href="/quiz" title="Quiz">Quiz</a>
            <a href="/story" title="Story">Story</a>
            <a href="/lens" title="Lens">Lens</a>
        </div>

        <div class="contact">
            <div class="contact-item">Đường dây nóng: <br>02393 693 427 &nbsp;/&nbsp; 02393 856 715</div>
            <a href="#" class="contact-item" title="Gửi tòa soạn">
                <i class="ic-mail"></i> Gửi tòa soạn
            </a>
            <a href="#" class="contact-item" title="Quảng cáo">
                <i class="ic-paper-plane"></i> Quảng cáo
            </a>
            <div class="text">
                Giấy phép số: 15/GP-BTTTT do Bộ Thông tin - Truyền thông cấp ngày 17 tháng 01 năm 2022. <br>
                Tổng Biên tập: Nghiêm Sỹ Đống <br>
                Trụ sở chính: Số 22, đường Phan Đình Phùng, phường Thành Sen, tỉnh Hà Tĩnh <br>
                Cơ sở 2: Số 223, đường Nguyễn Huy Tự, phường Thành Sen, tỉnh Hà Tĩnh <br>
                © Bản quyền thuộc về Báo Hà Tĩnh <br>
                Cấm sao chép dưới mọi hình thức nếu không có sự chấp thuận bằng văn bản.
            </div>
        </div>
    </div>
</div>