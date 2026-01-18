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
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->string('name', 255)->comment('Tên vai trò (ví dụ: province, commune, enterprise, employee, editor)')->unique();
            $table->text('description')->nullable()->default(NULL)->comment('Mô tả vai trò');
            $table->unsignedTinyInteger('priority')->default(0)->comment('Thứ tự ưu tiên vai trò')->index();
            $table->boolean('is_active')->default(true)->comment('Trạng thái vai trò')->index();
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        

        DB::table('roles')->updateOrInsert( ['name' => 'administrator'], ['name' => 'administrator','description' => 'Quản lý toàn hệ thống - có quyền cài đặt hệ thống','priority' => '110','is_active' => '1'] );
        DB::table('roles')->updateOrInsert( ['name' => 'warehouse_management'], ['name' => 'warehouse_management','description' => 'Quản lý kho','priority' => '100','is_active' => '1'] );
        DB::table('roles')->updateOrInsert( ['name' => 'foreman'], ['name' => 'foreman','description' => 'Tài khoản quản đốc','priority' => '90','is_active' => '1'] );
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};