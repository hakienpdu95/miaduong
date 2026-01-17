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
        Schema::create('subscription_logs', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedBigInteger('user_subscription_id')->comment('Thuộc gói đăng ký người dùng')->constrained('user_subscriptions')->onDelete('cascade');
            $table->enum('action', ['created','renewed','upgraded','cancelled','expired'])->default('created')->comment('Hành động');
            $table->text('details')->nullable()->default(NULL)->comment('Chi tiết hành động');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            
            $table->index('user_subscription_id');
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_logs');
    }
};