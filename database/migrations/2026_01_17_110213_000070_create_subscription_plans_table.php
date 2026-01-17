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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedBigInteger('category_id')->comment('Thuộc danh mục gói')->constrained('plan_categories')->onDelete('cascade');
            $table->string('name', 255)->comment('Tên gói (VD: Free, Premium, Pro)');
            $table->text('description')->nullable()->default(NULL)->comment('Mô tả gói');
            $table->unsignedInteger('max_items')->default(0)->comment('Số lượng tối đa tin đăng');
            $table->unsignedInteger('max_views')->nullable()->default(NULL)->comment('Số lượt xem tối đa');
            $table->unsignedInteger('max_edits')->nullable()->default(NULL)->comment('Số lần chỉnh sửa tối đa');
            $table->unsignedInteger('duration_days')->default(0)->comment('Thời gian hiệu lực (ngày)');
            $table->decimal('price', 10, 2)->default(0.00)->comment('Giá gói');
            $table->unsignedTinyInteger('priority')->default(0)->comment('Mức độ ưu tiên hiển thị tin')->index();
            $table->boolean('is_active')->default(true)->comment('Trạng thái gói')->index();
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
            $table->index('category_id');
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};