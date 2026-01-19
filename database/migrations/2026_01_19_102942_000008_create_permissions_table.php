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
        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedBigInteger('user_id')->comment('Người dùng')->constrained('users');
            $table->string('module_name', 255)->comment('Tên module');
            $table->boolean('can_view')->default(false)->comment('Quyền xem');
            $table->boolean('can_create')->default(false)->comment('Quyền tạo');
            $table->boolean('can_edit')->default(false)->comment('Quyền sửa');
            $table->boolean('can_delete')->default(false)->comment('Quyền xóa');
            $table->timestamp('expired_at')->nullable()->default(NULL)->comment('Thời hạn quyền');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
            $table->index(['user_id','module_name']);
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};