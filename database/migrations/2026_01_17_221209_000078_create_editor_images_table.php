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
        Schema::create('editor_images', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->string('full_path', 700)->nullable()->default(NULL)->comment('Full relative path');
            $table->string('hash', 700)->nullable()->default(NULL)->comment('Mã Hash');
            $table->string('original_name', 700)->nullable()->default(NULL)->comment('Original Name');
            $table->unsignedBigInteger('imageable_id')->nullable()->default(NULL)->comment('ID của model liên kết')->index();
            $table->string('imageable_type')->nullable()->default(NULL)->comment('Loại model liên kết')->index();
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('editor_images');
    }
};