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
            $table->unsignedBigInteger('equipment_qr_code_id')->comment('Thuộc thiết bị')->constrained('equipment_qr_codes')->onDelete('cascade')->index();
            $table->unsignedBigInteger('maintenance_type_id')->comment('Thuộc loại bảo trì')->constrained('maintenance_types')->onDelete('cascade')->index();
            $table->date('start_date')->comment('Thời gian bắt đầu bảo trì')->index();
            $table->date('end_date')->comment('Thời gian kết thúc bảo trì')->index();
            $table->string('performer', 255)->comment('Người thực hiện')->index();
            $table->longText('description')->nullable()->default(NULL)->comment('Mô tả');
            $table->enum('status', ['operating_active','under_repair','broken_damaged'])->default('operating_active')->comment('Trạng thái')->index();
            $table->date('setup_time')->comment('Cài đặt thời gian bảo trì tiếp theo')->index();
            $table->unsignedBigInteger('created_by')->nullable()->default(NULL)->comment('Tạo bởi')->constrained('users')->onDelete('set null');
            $table->unsignedBigInteger('updated_by')->nullable()->default(NULL)->comment('Cập nhật bởi')->constrained('users')->onDelete('set null');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_logs');
    }
};