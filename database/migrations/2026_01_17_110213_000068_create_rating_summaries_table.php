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
        Schema::create('rating_summaries', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedBigInteger('post_id')->comment('Bài viết')->constrained('posts')->onDelete('cascade')->index();
            $table->unsignedInteger('total_ratings')->default(0)->comment('Tổng lượt đánh giá');
            $table->integer('total_score')->default(0)->comment('Tổng điểm');
            $table->float('average_score')->default(0)->comment('Điểm trung bình');
            $table->json('rating_counts')->nullable()->default(NULL)->comment('Số lượt mỗi mức: {"-2": 10, "-1": 5, "0": 20, "1": 15, "2": 30}');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('rating_summaries');
    }
};