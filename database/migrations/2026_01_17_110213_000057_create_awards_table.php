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
        Schema::create('awards', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->string('name', 255)->comment('Tên giải thưởng');
            $table->enum('award_type', ['national','provincial','local','industry_specific','other'])->default('other')->comment('Loại: Quốc gia, Tỉnh, Địa phương, Ngành cụ thể, Khác')->index();
            $table->char('province_code', 2)->nullable()->default(NULL)->comment('Mã tỉnh')->references('province_code')->on('provinces')->onDelete('set null')->index();
            $table->char('ward_code', 5)->nullable()->default(NULL)->comment('Thuộc xã')->references('ward_code')->on('wards')->onDelete('set null')->index();
            $table->unsignedSmallInteger('year')->nullable()->default(NULL)->comment('Năm cấp')->index();
            $table->text('description')->nullable()->default(NULL)->comment('Mô tả giải thưởng');
            $table->string('issued_by', 255)->nullable()->default(NULL)->comment('Tổ chức cấp (ví dụ: UBND tỉnh Sơn La)');
            $table->boolean('is_active')->default(true)->comment('Trạng thái hoạt động')->index();
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('awards');
    }
};