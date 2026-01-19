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
            $table->unsignedBigInteger('import_batch_id')->comment('Thuộc batch nhập thiết bị')->constrained('import_batches')->onDelete('cascade')->index();
            $table->char('serial', 10)->nullable()->default(NULL)->comment('Mã serial thiết bị')->unique()->index();
            $table->string('name', 255)->comment('Tên thiết bị')->index();
            $table->string('image_path', 700)->nullable()->default(NULL)->comment('Full image path');
            $table->date('import_date')->nullable()->default(NULL)->comment('Ngày nhập thiết bị')->index();
            $table->unsignedBigInteger('country_id')->nullable()->default(NULL)->comment('Quốc gia xuất xứ')->constrained('country')->onDelete('cascade')->index();
            $table->unsignedBigInteger('unit_id')->comment('Thuộc đơn vị sử dụng')->constrained('units')->onDelete('cascade')->index();
            $table->longText('attachment')->nullable()->default(NULL)->comment('File đính kèm dạng soạn thảo');
            $table->longText('additional_info')->nullable()->default(NULL)->comment('Thông tin bổ sung');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('equipments');
    }
};