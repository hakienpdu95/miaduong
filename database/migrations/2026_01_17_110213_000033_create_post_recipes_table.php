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
        Schema::create('post_recipes', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->string('description', 255)->nullable()->default(NULL)->comment('Mô tả chi tiết công thức');
            $table->unsignedInteger('prep_time')->nullable()->default(NULL)->comment('Thời gian chuẩn bị (phút)')->index();
            $table->unsignedInteger('cook_time')->nullable()->default(NULL)->comment('Thời gian nấu (phút)')->index();
            $table->unsignedInteger('total_time')->nullable()->default(NULL)->comment('Tổng thời gian (phút, tự tính prep + cook nếu cần)')->index();
            $table->unsignedTinyInteger('servings')->nullable()->default(NULL)->comment('Số khẩu phần (ví dụ: 4)')->index();
            $table->string('yield', 100)->nullable()->default(NULL)->comment('Kết quả (ví dụ: 4 servings hoặc 2 chiếc bánh)');
            $table->enum('difficulty', ['easy','medium','hard'])->nullable()->default('easy')->comment('Độ khó (dễ, trung bình, khó)')->index();
            $table->string('cuisine', 100)->nullable()->default(NULL)->comment('Phong cách ẩm thực (ví dụ: Việt Nam, Ý)')->index();
            $table->string('category', 100)->nullable()->default(NULL)->comment('Phân loại món (ví dụ: Món chính, Tráng miệng)')->index();
            $table->text('keywords')->nullable()->default(NULL)->comment('Từ khóa (ví dụ: món ăn gia đình, nhanh gọn)');
            $table->string('video_url', 255)->nullable()->default(NULL)->comment('Link video hướng dẫn (YouTube hoặc embed)');
            $table->text('notes')->nullable()->default(NULL)->comment('Ghi chú thêm (tips, biến tấu)');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('post_recipes');
    }
};