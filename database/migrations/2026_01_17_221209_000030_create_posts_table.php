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
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug', 255)->comment('Slug SEO')->unique()->index();
            $table->string('title', 255)->comment('Tiêu đề bài viết');
            $table->text('excerpt')->nullable()->default(NULL)->comment('Tóm tắt bài viết');
            $table->longText('content')->nullable()->default(NULL)->comment('Nội dung bài viết (Quill or SunEditor)');
            $table->json('image')->nullable()->default(NULL)->comment('Đường dẫn hình ảnh');
            $table->enum('status', ['publish','draft','pending','private','scheduled'])->default('draft')->comment('Trạng thái')->index();
            $table->enum('type', ['post','page','custom'])->default('post')->comment('Loại: post, page, custom');
            $table->enum('item_type', ['basic','redirect','recipe','review','school','quote'])->default('basic')->comment('Loại nội dung');
            $table->unsignedBigInteger('reference_id')->nullable()->default(NULL)->comment('ID tham chiếu (basic, redirect, recipe, review, school, quote)')->index();
            $table->boolean('is_sticky')->default(false)->comment('Bài viết được ghim')->index();
            $table->boolean('is_suggest')->default(false)->comment('Bài viết có thể bạn quan tâm')->index();
            $table->boolean('is_featured')->default(false)->comment('Bài viết nổi bật')->index();
            $table->boolean('is_password')->default(false)->comment('Bài viết đặt mật khẩu')->index();
            $table->timestamp('featured_until')->nullable()->default(NULL)->comment('Thời gian hết nổi bật')->index();
            $table->boolean('is_active')->default(true)->comment('Trạng thái hoạt động')->index();
            $table->unsignedTinyInteger('priority')->default(0)->comment('Mức độ ưu tiên (0-100)')->index();
            $table->unsignedBigInteger('author_id')->nullable()->default(NULL)->comment('Tác giả')->constrained('users')->onDelete('set null')->index();
            $table->unsignedBigInteger('template_id')->nullable()->default(NULL)->comment('Template')->constrained('templates')->onDelete('cascade');
            $table->unsignedBigInteger('parent_id')->nullable()->default(NULL)->comment('Bài viết cha')->constrained('posts')->onDelete('cascade');
            $table->string('password', 255)->nullable()->default(NULL)->comment('Mật khẩu bài viết');
            $table->timestamp('published_at')->nullable()->default(NULL)->comment('Ngày xuất bản')->index();
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};