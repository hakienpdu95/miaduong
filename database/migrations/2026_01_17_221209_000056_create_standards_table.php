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
        Schema::create('standards', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->char('code', 40)->nullable()->default(NULL)->comment('Mã tiêu chuẩn');
            $table->string('name', 255)->comment('Tên tiêu chuẩn (VietGAP, GlobalGAP, v.v.)');
            $table->string('image', 255)->nullable()->default(NULL)->comment('Đường dẫn hình ảnh tiêu chuẩn');
            $table->text('description')->nullable()->default(NULL)->comment('Mô tả tiêu chuẩn');
            $table->boolean('is_active')->default(true)->comment('Trạng thái hoạt động');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
            $table->index('name');
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('standards');
    }
};