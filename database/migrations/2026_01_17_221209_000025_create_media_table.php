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
        Schema::create('media', function (Blueprint $table) {
            $table->increments('id')->comment('Khóa chính');
            $table->unsignedBigInteger('model_id')->comment('ID Model')->index();
            $table->string('model_type', 255)->comment('Loại model')->index();
            $table->char('uuid', 36)->nullable()->default(NULL)->comment('UUID')->unique();
            $table->string('collection_name', 255)->comment('Tên Bộ Sưu Tập');
            $table->string('name', 255)->comment('Tên');
            $table->string('file_name', 255)->comment('Tên File');
            $table->string('mime_type', 255)->nullable()->default(NULL)->comment('Loại MIME');
            $table->string('disk', 255)->comment('Đĩa Lưu Trữ');
            $table->string('conversions_disk', 255)->nullable()->default(NULL)->comment('Đĩa Chuyển Đổi');
            $table->unsignedBigInteger('size')->comment('Kích Thước');
            $table->json('manipulations')->comment('Thao Tác');
            $table->json('custom_properties')->comment('Thuộc Tính Tùy Chọn');
            $table->json('generated_conversions')->comment('Chuyển Đổi Tạo');
            $table->json('responsive_images')->comment('Ảnh Responsive');
            $table->unsignedInteger('order_column')->nullable()->default(NULL)->comment('Thứ Tự');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
            $table->index(['order_column','collection_name','mime_type','disk','size']);
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};