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
        Schema::create('comments', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedBigInteger('post_id')->comment('Liên kết bài viết')->constrained('posts')->onDelete('cascade')->index();
            $table->unsignedBigInteger('user_id')->nullable()->default(NULL)->comment('Người dùng (null nếu vãng lai)')->constrained('users')->onDelete('set null')->index();
            $table->unsignedBigInteger('parent_id')->nullable()->default(NULL)->comment('Bình luận cha')->constrained('comments')->onDelete('cascade')->index();
            $table->string('email', 255)->comment('Email người bình luận');
            $table->string('guest_name', 100)->nullable()->default(NULL)->comment('Tên khách vãng lai');
            $table->string('title', 255)->nullable()->default(NULL)->comment('Tiêu đề bình luận');
            $table->text('content')->comment('Nội dung bình luận');
            $table->boolean('is_approved')->default(false)->comment('Trạng thái duyệt')->index();
            $table->string('ip_address', 60)->nullable()->default(NULL)->comment('IP để chống spam');
            $table->string('user_agent', 255)->nullable()->default(NULL)->comment('User agent để chống spam');
            $table->json('meta')->nullable()->default(NULL)->comment('Dữ liệu bổ sung (ví dụ: lượt thích)');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};