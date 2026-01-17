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
        Schema::create('enterprises', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->char('province_code', 2)->comment('Thuộc tỉnh')->references('province_code')->on('provinces')->onDelete('cascade')->index();
            $table->char('ward_code', 5)->comment('Thuộc xã')->references('ward_code')->on('wards')->onDelete('cascade')->index();
            $table->string('code', 50)->nullable()->default(NULL)->comment('Mã Code');
            $table->string('gcp', 50)->nullable()->default(NULL)->comment('Mã GCP');
            $table->string('gln', 50)->nullable()->default(NULL)->comment('Mã GLN');
            $table->string('logo', 255)->nullable()->default(NULL)->comment('Đường dẫn logo doanh nghiệp');
            $table->string('name', 255)->nullable()->default(NULL)->comment('Tên doanh nghiệp');
            $table->string('tax_code', 20)->nullable()->default(NULL)->comment('Mã số thuế')->unique()->index();
            $table->date('license_issued_date')->nullable()->default(NULL)->comment('Ngày cấp giấy đăng ký kinh doanh');
            $table->string('email', 255)->nullable()->default(NULL)->comment('Email');
            $table->string('phone', 20)->nullable()->default(NULL)->comment('Phone');
            $table->string('website', 100)->nullable()->default(NULL)->comment('Website');
            $table->unsignedBigInteger('industry_id')->nullable()->default(NULL)->comment('Danh mục ngành hàng')->constrained('industries')->onDelete('set null')->index();
            $table->enum('type', ['cooperative','enterprise','household'])->default('enterprise')->comment('Loại chủ thể');
            $table->double('latitude')->nullable()->default(NULL)->comment('Latitude');
            $table->double('longitude')->nullable()->default(NULL)->comment('Longitude');
            $table->boolean('is_national_brand')->default(false)->comment('DN thương hiệu quốc gia không')->index();
            $table->boolean('is_active')->default(true)->comment('Trạng thái doanh nghiệp')->index();
            $table->enum('status', ['unverified','pending','in_review','verified','rejected'])->default('unverified')->comment('Trạng thái xác thực')->index();
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            $table->timestamp('deleted_at')->nullable()->default(NULL)->comment('Thời gian xóa mềm');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('enterprises');
    }
};