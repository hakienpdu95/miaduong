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
        Schema::create('post_review_links', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedBigInteger('post_review_id')->comment('ID sản phẩm review cha')->constrained('post_reviews')->onDelete('cascade');
            $table->string('label', 255)->comment('Nhãn link');
            $table->string('url', 500)->comment('Đường dẫn URL');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
            $table->index('post_review_id');
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('post_review_links');
    }
};