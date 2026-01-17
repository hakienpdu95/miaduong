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
        Schema::create('provinces', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255)->comment('Tên tỉnh/thành phố')->index();
            $table->string('short_name', 255)->comment('Tên ngắn gọn của tỉnh/thành phố');
            $table->string('logo', 255)->nullable()->default(NULL)->comment('Logo tỉnh');
            $table->char('province_code', 2)->comment('Mã tỉnh/thành phố')->unique()->index();
            $table->enum('place_type', ['thanh-pho','tinh'])->default('tinh')->comment('Loại: Thành phố Trung Ương hoặc Tỉnh')->index();
            $table->unsignedBigInteger('region_id')->comment('Thuộc vùng')->constrained('regions')->onDelete('cascade');
            $table->string('country', 2)->default('VN')->comment('Mã quốc gia')->index();
            $table->boolean('is_active')->default(true)->comment('Trạng thái hoạt động')->index();
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            $table->timestamp('deleted_at')->nullable()->default(NULL)->comment('Thời gian xóa mềm');
            
            $table->index('region_id');
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('provinces');
    }
};