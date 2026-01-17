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
        Schema::create('post_redirects', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->boolean('is_redirect')->default(false)->comment('Bài viết thiết lập chuyển hướng')->index();
            $table->string('redirect_url', 255)->comment('Link đích để redirect')->unique()->index();
            $table->unsignedBigInteger('publisher_id')->comment('Nhà xuất bản')->constrained('publishers')->onDelete('cascade');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('post_redirects');
    }
};