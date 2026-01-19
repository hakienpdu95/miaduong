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
        Schema::create('import_batches', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->string('sku', 100)->nullable()->default(NULL)->comment('Mã SKU')->unique()->index();
            $table->enum('unit_type', ['box','set_kit','device_equipment','piece_item','unit_piece'])->default('box')->comment('Đơn vị tính')->index();
            $table->enum('import_method', ['single_item','batch_series'])->default('single_item')->comment('Phương pháp nhập')->index();
            $table->unsignedBigInteger('importer_id')->comment('Thuộc người dùng')->constrained('users')->onDelete('cascade')->index();
            $table->unsignedBigInteger('quantity')->nullable()->default(NULL)->comment('Số lượng');
            $table->date('import_date')->nullable()->default(NULL)->comment('Ngày nhập')->index();
            $table->text('notes')->nullable()->default(NULL)->comment('Mô tả');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('import_batches');
    }
};