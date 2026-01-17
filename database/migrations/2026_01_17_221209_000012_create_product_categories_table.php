<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->increments('id')->comment('Khóa chính');
            $table->string('name', 355)->comment('Tên danh mục');
            $table->char('code', 5)->nullable()->default(NULL)->comment('Mã danh mục (ví dụ: I, 1, 1.1)');
            $table->string('description', 500)->nullable()->default(NULL)->comment('Mô tả');
            $table->string('image', 255)->nullable()->default(NULL)->comment('Đường dẫn hình ảnh');
            $table->string('slug', 255)->comment('Slug SEO')->unique()->index();
            $table->unsignedBigInteger('parent_id')->nullable()->default(NULL)->comment('Danh mục cha')->constrained('product_categories')->nullOnDelete();
            $table->unsignedBigInteger('left')->comment('Vị trí trái (Nested Set)');
            $table->unsignedBigInteger('right')->comment('Vị trí phải (Nested Set)');
            $table->unsignedBigInteger('depth')->default(0)->comment('Độ sâu (Nested Set)');
            $table->boolean('is_active')->default(true)->comment('Trạng thái hoạt động');
            $table->unsignedBigInteger('order')->default(0)->comment('Thứ tự sắp xếp');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            $table->timestamp('deleted_at')->nullable()->default(NULL)->comment('Thời gian xóa mềm');
            
            $table->index('left');
            $table->index('right');
            $table->index('is_active');
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};