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
        Schema::create('units', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->string('code', 255)->comment('Mã đơn vị sử dụng')->unique()->index();
            $table->string('name', 255)->comment('Tên đơn vị');
            $table->string('supervisor_name', 255)->nullable()->default(NULL)->comment('Tên quản đốc');
            $table->string('supervisor_phone', 20)->nullable()->default(NULL)->comment('Phone');
            $table->string('quantity', 50)->nullable()->default(NULL)->comment('Số lượng thiết bị');
            $table->text('description')->nullable()->default(NULL)->comment('Mô tả');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            $table->timestamp('deleted_at')->nullable()->default(NULL)->comment('Thời gian xóa mềm');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};