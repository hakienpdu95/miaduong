<!-- Form mẫu tham khảo để làm chức năng duyệt yêu cầu -->
<h1>Chỉnh sửa bài viết</h1>
<form action="{{ auth()->user()->hasRole('admin') ? route('posts.update', $post) : route('approvals.submit', ['type' => 'post', 'id' => $post->id]) }}" method="POST">
    @csrf
    @if (auth()->user()->hasRole('admin'))
        @method('PUT')
    @endif
    <div>
        <label>Tiêu đề</label>
        <input type="text" name="title" value="{{ old('title', $post->title) }}" required>
    </div>
    <div>
        <label>Nội dung</label>
        <textarea name="content" required>{{ old('content', $post->content) }}</textarea>
    </div>
    @if (auth()->user()->hasRole('admin'))
        <button type="submit">Cập nhật</button>
    @else
        <button type="submit">Gửi duyệt</button>
    @endif
</form>