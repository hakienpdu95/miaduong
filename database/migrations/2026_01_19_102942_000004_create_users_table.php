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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->string('name', 255)->comment('Tên người dùng');
            $table->string('username', 255)->nullable()->default(NULL)->comment('Tên đăng nhập (cho đăng nhập thủ công)')->unique()->index();
            $table->string('email', 255)->nullable()->default(NULL)->comment('Email người dùng (cho phép NULL cho tài khoản mạng xã hội không có email)')->unique()->index();
            $table->string('password', 255)->nullable()->default(NULL)->comment('Mật khẩu (cho phép NULL cho tài khoản mạng xã hội)');
            $table->unsignedBigInteger('managed_by')->nullable()->default(NULL)->comment('Người quản lý')->constrained('users')->onDelete('set null');
            $table->boolean('is_active')->default(true)->comment('Trạng thái hoạt động')->index();
            $table->string('remember_token', 100)->nullable()->default(NULL)->comment('Token đăng nhập');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            $table->timestamp('deleted_at')->nullable()->default(NULL)->comment('Thời gian xóa mềm');
            
            $table->fullText(['name','email','username']);
        });

        

        DB::table('users')->updateOrInsert( ['email' => 'admin@gmail.com'], ['email' => 'admin@gmail.com','name' => 'admin','username' => 'admin','password' => Hash::make('checkvn@123!'),'is_active' => '1'] );
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};