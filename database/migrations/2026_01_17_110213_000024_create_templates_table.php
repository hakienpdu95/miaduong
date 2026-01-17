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
        Schema::create('templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255)->comment('Tên template');
            $table->string('blade_path', 255)->comment('Đường dẫn file Blade');
            $table->string('item_type', 255)->nullable()->default(NULL)->comment('Gán template cho item_type cụ thể')->index();
            $table->boolean('is_active')->default(true)->comment('Trạng thái hoạt động')->index();
            $table->boolean('is_default')->default(false)->comment('Template mặc định')->index();
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};