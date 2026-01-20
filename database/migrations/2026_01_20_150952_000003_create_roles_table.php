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
            $table->string('name', 255)->comment('Tên vai trò')->unique();
            $table->string('title', 255)->comment('Tiêu đề vai trò');
            $table->text('description')->nullable()->default(NULL)->comment('Mô tả vai trò');
            $table->unsignedTinyInteger('priority')->default(0)->comment('Thứ tự ưu tiên vai trò')->index();
            $table->boolean('is_active')->default(true)->comment('Trạng thái vai trò')->index();
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        

        DB::table('roles')->updateOrInsert( ['name' => 'administrator'], ['name' => 'administrator','title' => 'Quản trị viên','description' => 'Nhóm người dùng là quản trị hệ thống có vai trò quản lý toàn bộ dữ liệu có trên hệ thống','priority' => '110','is_active' => '1'] );
        DB::table('roles')->updateOrInsert( ['name' => 'warehouse_management'], ['name' => 'warehouse_management','title' => 'Quản lý kho','description' => 'Nhóm người có vai trò khởi tạo dữ liệu ban đầu của thiết bị','priority' => '100','is_active' => '1'] );
        DB::table('roles')->updateOrInsert( ['name' => 'foreman'], ['name' => 'foreman','title' => 'Quản đốc','description' => 'Nhóm người dùng có vai trò quản lý các thiết bị của mình và tiến hành cập nhật các dữ liệu liên quan đến bảo trì - bảo dưỡng thiết bị','priority' => '90','is_active' => '1'] );
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};