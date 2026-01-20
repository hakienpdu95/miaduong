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
        Schema::create('equipments', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->string('sku', 100)->nullable()->default(NULL)->comment('Mã SKU')->unique()->index();
            $table->enum('unit_type', ['box','set_kit','device_equipment','piece_item','unit_piece'])->default('box')->comment('Đơn vị tính')->index();
            $table->enum('import_method', ['single_item','batch_series'])->default('single_item')->comment('Phương pháp nhập')->index();
            $table->unsignedBigInteger('quantity')->nullable()->default(NULL)->comment('Số lượng');
            $table->string('name', 255)->comment('Tên thiết bị')->index();
            $table->string('image_path', 700)->nullable()->default(NULL)->comment('Full image path');
            $table->date('import_date')->nullable()->default(NULL)->comment('Ngày nhập thiết bị')->index();
            $table->unsignedBigInteger('country_id')->nullable()->default(NULL)->comment('Quốc gia xuất xứ')->constrained('country')->onDelete('cascade')->index();
            $table->unsignedBigInteger('unit_id')->comment('Thuộc đơn vị sử dụng')->constrained('units')->onDelete('cascade')->index();
            $table->longText('attachment')->nullable()->default(NULL)->comment('File đính kèm dạng soạn thảo');
            $table->longText('additional_info')->nullable()->default(NULL)->comment('Thông tin bổ sung');
            $table->unsignedBigInteger('managed_by')->nullable()->default(NULL)->comment('Người quản lý')->constrained('users')->onDelete('set null');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('equipments');
    }
};