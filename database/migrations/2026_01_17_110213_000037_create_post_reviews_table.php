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
        Schema::create('post_reviews', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedBigInteger('post_id')->comment('ID bài viết cha')->constrained('posts')->onDelete('cascade');
            $table->string('title', 255)->comment('Tiêu đề sản phẩm');
            $table->string('product_name', 255)->nullable()->default(NULL)->comment('Tên sản phẩm');
            $table->string('image', 255)->nullable()->default(NULL)->comment('Đường dẫn hình ảnh sản phẩm');
            $table->longText('description')->nullable()->default(NULL)->comment('Mô tả sản phẩm');
            $table->decimal('price', 15, 2)->nullable()->default(NULL)->comment('Giá sản phẩm');
            $table->longText('review_content')->nullable()->default(NULL)->comment('Nội dung review cá nhân');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
            $table->index('post_id');
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('post_reviews');
    }
};