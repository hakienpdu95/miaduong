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
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedBigInteger('user_id')->comment('Thuộc người dùng/doanh nghiệp')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('subscription_plan_id')->comment('Thuộc gói đăng ký')->constrained('subscription_plans')->onDelete('cascade');
            $table->dateTime('start_date')->comment('Ngày bắt đầu gói');
            $table->dateTime('end_date')->comment('Ngày kết thúc gói');
            $table->enum('status', ['active','expired','cancelled','pending'])->default('pending')->comment('Trạng thái gói');
            $table->unsignedInteger('used_items')->default(0)->comment('Số tin đã đăng');
            $table->unsignedInteger('remaining_items')->default(0)->comment('Số tin còn lại');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
            $table->index('user_id');
            $table->index('subscription_plan_id');
            $table->index('start_date');
            $table->index('end_date');
            $table->index('status');
            $table->index('used_items');
            $table->index('remaining_items');
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
    }
};