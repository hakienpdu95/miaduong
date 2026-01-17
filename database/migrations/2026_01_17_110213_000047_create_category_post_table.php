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
        Schema::create('category_post', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->comment('Danh mục')->constrained('categories')->onDelete('cascade');
            $table->unsignedBigInteger('post_id')->comment('Bài viết')->constrained('posts')->onDelete('cascade');
            $table->primary(['category_id','post_id']);
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('category_post');
    }
};