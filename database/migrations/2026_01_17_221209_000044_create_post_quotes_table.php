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
        Schema::create('post_quotes', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedBigInteger('post_id')->comment('ID bài viết cha')->constrained('posts')->onDelete('cascade');
            $table->string('image', 255)->nullable()->default(NULL)->comment('Hình ảnh quote');
            $table->string('title', 255)->comment('Tiêu đề');
            $table->longText('content')->nullable()->default(NULL)->comment('Nội dung');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
            $table->index('post_id');
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('post_quotes');
    }
};