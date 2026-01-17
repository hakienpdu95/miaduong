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
        Schema::create('pregnancy_weeks', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedTinyInteger('week_number')->comment('Số tuần thai 1-40')->unique()->index();
            $table->string('slug', 255)->comment('Slug SEO')->unique()->index();
            $table->string('title', 255)->comment('Tiêu đề tuần');
            $table->string('baby_size', 255)->nullable()->default(NULL)->comment('So sánh kích thước em bé');
            $table->text('excerpt')->nullable()->default(NULL)->comment('Tóm tắt ngắn gọn');
            $table->longText('content')->nullable()->default(NULL)->comment('Nội dung chi tiết');
            $table->enum('status', ['publish','draft','pending'])->default('draft')->comment('Trạng thái xuất bản')->index();
            $table->timestamp('published_at')->nullable()->default(NULL)->comment('Ngày xuất bản')->index();
            $table->boolean('is_featured')->default(false)->comment('Tuần nổi bật')->index();
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('pregnancy_weeks');
    }
};