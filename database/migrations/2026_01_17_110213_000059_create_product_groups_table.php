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
        Schema::create('product_groups', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->string('name', 255)->comment('Tên nhóm sản phẩm');
            $table->enum('group_type', ['national','provincial','local'])->default('local')->comment('Loại nhóm: Quốc gia (chủ lực), Tỉnh (chủ lực), Xã (đặc sản)')->index();
            $table->char('province_code', 2)->nullable()->default(NULL)->comment('Mã tỉnh/thành phố (NULL cho nhóm quốc gia hoặc xã)')->references('province_code')->on('provinces')->onDelete('set null');
            $table->char('ward_code', 5)->nullable()->default(NULL)->comment('Mã phường/xã (NULL cho nhóm quốc gia hoặc tỉnh)')->references('ward_code')->on('wards')->onDelete('set null');
            $table->text('description')->nullable()->default(NULL)->comment('Mô tả chi tiết về nhóm sản phẩm');
            $table->boolean('is_active')->default(true)->comment('Trạng thái hoạt động')->index();
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            $table->timestamp('deleted_at')->nullable()->default(NULL)->comment('Thời gian xóa mềm');
            
            $table->index('province_code');
            $table->index('ward_code');
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('product_groups');
    }
};