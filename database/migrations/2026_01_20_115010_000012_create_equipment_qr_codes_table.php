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
        Schema::create('equipment_qr_codes', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedBigInteger('equipment_id')->comment('Thuộc thiết bị')->constrained('equipments')->onDelete('cascade')->index();
            $table->char('serial_number', 8)->nullable()->default(NULL)->comment('Serial Number')->unique()->index();
            $table->unsignedBigInteger('managed_by')->nullable()->default(NULL)->comment('Người quản lý')->constrained('users')->onDelete('set null');
            $table->unsignedBigInteger('created_by')->nullable()->default(NULL)->comment('Tạo bởi')->constrained('users')->onDelete('set null');
            $table->unsignedBigInteger('updated_by')->nullable()->default(NULL)->comment('Cập nhật bởi')->constrained('users')->onDelete('set null');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_qr_codes');
    }
};