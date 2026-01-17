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
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedBigInteger('enterprise_id')->nullable()->default(NULL)->comment('Doanh nghiệp liên kết')->constrained('enterprises')->onDelete('cascade')->index();
            $table->unsignedBigInteger('brand_id')->nullable()->default(NULL)->comment('Thương hiệu liên kết')->constrained('brands')->onDelete('cascade')->index();
            $table->unsignedBigInteger('category_id')->nullable()->default(NULL)->comment('Danh mục sản phẩm')->constrained('product_categories')->onDelete('set null')->index();
            $table->string('name', 255)->comment('Tên sản phẩm');
            $table->string('code', 50)->nullable()->default(NULL)->comment('Mã sản phẩm')->unique()->index();
            $table->string('gtin', 50)->nullable()->default(NULL)->comment('Mã GTIN')->unique()->index();
            $table->string('sku', 100)->nullable()->default(NULL)->comment('Mã SKU')->index();
            $table->enum('status', ['unverified','pending','in_review','verified','rejected'])->default('unverified')->comment('Trạng thái xác thực')->index();
            $table->boolean('is_active')->default(true)->comment('Trạng thái hoạt động');
            $table->enum('expiration_unit', ['minute','hour','day','week','month','year','indefinite'])->default('indefinite')->comment('Thời hạn lưu hành của sản phẩm')->index();
            $table->integer('expiration_value')->nullable()->default(NULL)->comment('Giá trị thời hạn lưu hành');
            $table->date('expiry_date')->nullable()->default(NULL)->comment('Thời hạn sử dụng');
            $table->longText('description')->nullable()->default(NULL)->comment('Mô tả sản phẩm');
            $table->longText('product_story')->nullable()->default(NULL)->comment('Câu chuyện sản phẩm');
            $table->string('video_link', 255)->nullable()->default(NULL)->comment('Video clip quá trình sản xuất');
            $table->boolean('is_national_brand')->default(false)->comment('Sản phẩm thương hiệu quốc gia')->index();
            $table->enum('risk_level', ['low','medium','high'])->default('low')->comment('Mức độ rủi ro')->index();
            $table->text('risk_description')->nullable()->default(NULL)->comment('Mô tả rủi ro (tác động sức khỏe)');
            $table->string('quality_standard', 255)->nullable()->default(NULL)->comment('Tiêu chuẩn chất lượng công bố');
            $table->boolean('is_verified')->default(false)->comment('Đã chứng nhận hợp quy/chuẩn')->index();
            $table->boolean('is_eco_friendly')->default(false)->comment('Sản phẩm thân thiện môi trường')->index();
            $table->text('warning_labels')->nullable()->default(NULL)->comment('Cảnh báo rủi ro');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};