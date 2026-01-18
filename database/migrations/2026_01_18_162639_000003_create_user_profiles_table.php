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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id')->comment('Người dùng')->constrained('users')->unique();
            $table->char('province_code', 2)->nullable()->default(NULL)->comment('Gán cho tài khoản cấp tỉnh')->references('province_code')->on('provinces')->onDelete('cascade');
            $table->char('ward_code', 5)->nullable()->default(NULL)->comment('Gán cho tài khoản cấp xã')->references('ward_code')->on('wards')->onDelete('cascade');
            $table->string('phone', 20)->nullable()->default(NULL)->comment('Số điện thoại');
            $table->text('address')->nullable()->default(NULL)->comment('Địa chỉ');
            $table->string('avatar', 255)->nullable()->default(NULL)->comment('URL ảnh đại diện');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};