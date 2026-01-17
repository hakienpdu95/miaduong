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
        Schema::create('modules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255)->comment('Tên module')->unique();
            $table->string('category', 255)->nullable()->default(NULL)->comment('Danh mục module (admin, content, report)');
            $table->text('description')->nullable()->default(NULL)->comment('Mô tả module');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};