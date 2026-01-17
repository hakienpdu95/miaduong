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
        Schema::create('wards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255)->comment('Tên phường/xã')->index();
            $table->char('ward_code', 5)->comment('Mã phường/xã')->unique()->index();
            $table->enum('place_type', ['phuong','xa','dac-khu'])->default('xa')->comment('Loại: phường, xã, đặc khu')->index();
            $table->char('province_code', 2)->comment('Tỉnh/thành phố liên kết');
            $table->foreign('province_code')->references('province_code')->on('provinces')->onDelete('cascade');
            $table->boolean('is_active')->default(true)->comment('Trạng thái hoạt động')->index();
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            $table->timestamp('deleted_at')->nullable()->default(NULL)->comment('Thời gian xóa mềm');
            
            $table->index('province_code');
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('wards');
    }
};