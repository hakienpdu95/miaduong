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
        Schema::create('brands', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->string('name', 255)->comment('Tên thương hiệu');
            $table->text('description')->nullable()->default(NULL)->comment('Mô tả thương hiệu');
            $table->string('logo', 255)->nullable()->default(NULL)->comment('Đường dẫn logo thương hiệu');
            $table->unsignedBigInteger('enterprise_id')->comment('Doanh nghiệp/HTX/hộ sản xuất sở hữu thương hiệu')->constrained('enterprises')->onDelete('cascade')->index();
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
            $table->index('name');
            $table->index('logo');
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};