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
        Schema::create('product_images', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedBigInteger('product_id')->comment('Khóa ngoại liên kết với sản phẩm')->constrained('products')->onDelete('cascade')->index();
            $table->enum('type', ['main','gallery'])->nullable()->default(NULL)->comment('Loại ảnh (main: ảnh bìa, gallery: hình ảnh)')->index();
            $table->string('full_url', 255)->nullable()->default(NULL)->comment('URL ảnh chất lượng cao');
            $table->string('thumbnail_url', 255)->nullable()->default(NULL)->comment('URL ảnh LQIP (low-quality image placeholder)');
            $table->unsignedInteger('order')->default(0)->comment('Thứ tự hiển thị ảnh')->index();
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};