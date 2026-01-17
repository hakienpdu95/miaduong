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
        Schema::create('plan_features', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedBigInteger('subscription_plan_id')->comment('Thuộc gói đăng ký')->constrained('subscription_plans')->onDelete('cascade');
            $table->string('feature_name', 255)->comment('Tên lợi ích (VD: Ghim tin, Quảng cáo, Hỗ trợ ưu tiên)');
            $table->string('value', 255)->comment('Giá trị lợi ích (VD: true, 5 lần, 1000 lượt)');
            $table->text('description')->nullable()->default(NULL)->comment('Mô tả lợi ích');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
            $table->index('feature_name');
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_features');
    }
};