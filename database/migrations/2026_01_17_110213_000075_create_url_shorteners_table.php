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
        Schema::create('url_shorteners', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedBigInteger('user_id')->comment('Người dùng')->constrained('users')->onDelete('cascade');
            $table->string('short_code', 255)->comment('Mã rút gọn')->unique();
            $table->string('long_url', 2048)->comment('URL đích');
            $table->enum('redirect_type', ['301','302'])->default('301')->comment('Dạng chuyển hướng');
            $table->unsignedBigInteger('clicks')->default(0)->comment('Đếm lượt truy cập');
            $table->timestamp('expires_at')->nullable()->default(NULL)->comment('Thời gian hết hạn');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
            $table->index('expires_at');
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('url_shorteners');
    }
};