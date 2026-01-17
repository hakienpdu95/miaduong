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
        Schema::create('ratings', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedBigInteger('post_id')->comment('Bài viết')->constrained('posts')->onDelete('cascade')->index();
            $table->unsignedBigInteger('user_id')->nullable()->default(NULL)->comment('Người dùng (null nếu vãng lai)')->constrained('users')->onDelete('set null')->index();
            $table->string('email', 255)->nullable()->default(NULL)->comment('Email (bắt buộc cho vãng lai)')->index();
            $table->tinyInteger('value')->comment('Giá trị: -2, -1, 0, 1, 2')->check('value >= -2 AND value <= 2');
            $table->string('ip_address', 60)->nullable()->default(NULL)->comment('IP để chống spam');
            $table->string('user_agent', 255)->nullable()->default(NULL)->comment('User agent để chống spam');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};