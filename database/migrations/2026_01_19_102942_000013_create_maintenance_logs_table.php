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
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedBigInteger('equipment_id')->comment('Thuộc thiết bị')->constrained('equipments')->onDelete('cascade')->index();
            $table->unsignedBigInteger('maintenance_type_id')->comment('Thuộc loại bảo trì')->constrained('maintenance_types')->onDelete('cascade')->index();
            $table->date('maintenance_time')->comment('Thời gian bảo trì')->index();
            $table->unsignedBigInteger('performer_id')->comment('Thuộc người dùng - quản đốc thực hiện')->constrained('users')->onDelete('cascade')->index();
            $table->longText('description')->nullable()->default(NULL)->comment('Mô tả');
            $table->enum('status', ['operating_active','under_repair','broken_damaged'])->default('operating_active')->comment('Trạng thái')->index();
            $table->date('setup_time')->comment('Cài đặt thời gian bảo trì tiếp theo')->index();
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_logs');
    }
};