<!-- Form mẫu tham khảo để làm chức năng duyệt yêu cầu -->
<h1>Chỉnh sửa sản phẩm</h1>
<form action="{{ auth()->user()->hasRole('admin') ? route('products.update', $product) : route('approvals.submit', ['type' => 'product', 'id' => $product->id]) }}" method="POST">
    @csrf
    @if (auth()->user()->hasRole('admin'))
        @method('PUT')
    @endif
    <div>
        <label>Tên sản phẩm</label>
        <input type="text" name="name" value="{{ old('name', $product->name) }}" required>
    </div>
    <div>
        <label>Mô tả</label>
        <textarea name="description" required>{{ old('description', $product->description) }}</textarea>
    </div>
    <div>
        <label>Giá</label>
        <input type="number" name="price" value="{{ old('price', $product->price) }}" required>
    </div>
    @if (auth()->user()->hasRole('admin'))
        <button type="submit">Cập nhật</button>
    @else
        <button type="submit">Gửi duyệt</button>
    @endif
</form>