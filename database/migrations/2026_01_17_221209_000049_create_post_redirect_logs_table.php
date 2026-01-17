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
        Schema::create('post_redirect_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('post_redirect_id')->comment('Bài viết redirect')->constrained('post_redirects')->onDelete('cascade');
            $table->string('user_ip', 45)->nullable()->default(NULL)->comment('Địa chỉ IP người xem');
            $table->text('user_agent')->nullable()->default(NULL)->comment('Thông tin trình duyệt');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('post_redirect_logs');
    }
};