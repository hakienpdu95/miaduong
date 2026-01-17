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
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 255)->comment('Tiêu đề sự kiện');
            $table->string('slug', 255)->comment('Slug SEO')->unique()->index();
            $table->string('short_title', 155)->comment('Tiêu đề ngắn gọn');
            $table->enum('type', ['basic','fair','exhibition','conference'])->default('basic')->comment('Loại sự kiện');
            $table->longText('description')->nullable()->default(NULL)->comment('Mô tả sự kiện');
            $table->dateTime('start_date')->comment('Ngày giờ bắt đầu')->index();
            $table->dateTime('end_date')->comment('Ngày giờ kết thúc')->index();
            $table->char('province_code', 2)->comment('Thuộc tỉnh')->references('province_code')->on('provinces')->onDelete('cascade');
            $table->char('ward_code', 5)->comment('Thuộc xã')->references('ward_code')->on('wards')->onDelete('cascade');
            $table->unsignedBigInteger('industry_id')->nullable()->default(NULL)->comment('Thuộc ngành hàng')->constrained('industries')->onDelete('cascade');
            $table->enum('location_type', ['offline','online'])->default('offline')->comment('Loại địa điểm: Offline hoặc Online');
            $table->string('venue_name', 255)->nullable()->default(NULL)->comment('Tên địa điểm (khi location_type là physical)');
            $table->string('venue_address', 500)->nullable()->default(NULL)->comment('Địa chỉ địa điểm (khi location_type là physical)');
            $table->string('website', 255)->nullable()->default(NULL)->comment('Website sự kiện');
            $table->enum('price_type', ['free','fixed','range'])->default('free')->comment('Loại giá: Free, Fixed, Price Range');
            $table->decimal('single_price', 10, 2)->nullable()->default(NULL)->comment('Giá đơn (khi price_type là single)');
            $table->string('price_range', 255)->nullable()->default(NULL)->comment('Khoảng giá (khi price_type là range)');
            $table->string('poster_image', 255)->nullable()->default(NULL)->comment('Đường dẫn hình ảnh poster');
            $table->string('first_name', 255)->comment('Họ người gửi sự kiện');
            $table->string('last_name', 255)->comment('Tên người gửi sự kiện');
            $table->string('submitter_email', 255)->comment('Email người gửi sự kiện');
            $table->enum('status', ['publish','draft','pending','trash'])->default('pending')->comment('Trạng thái sự kiện');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            $table->timestamp('deleted_at')->nullable()->default(NULL)->comment('Thời gian xóa mềm');
            
            $table->index(['start_date','end_date']);
            $table->index('province_code');
            $table->index('ward_code');
            $table->index('status');
            $table->index('location_type');
            $table->index('price_type');
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};