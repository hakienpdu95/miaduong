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
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedBigInteger('user_id')->comment('Thuộc người dùng')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('user_subscription_id')->nullable()->default(NULL)->comment('Thuộc gói đăng ký người dùng')->constrained('user_subscriptions')->onDelete('cascade');
            $table->string('gateway', 100)->comment('Brand name của ngân hàng')->index();
            $table->timestamp('transaction_date')->nullable()->default(NULL)->comment('Thời gian xảy ra giao dịch phía ngân hàng (bắt buộc)');
            $table->string('account_number', 100)->nullable()->default(NULL)->comment('Số tài khoản ngân hàng');
            $table->string('sub_account', 250)->nullable()->default(NULL)->comment('Tài khoản ngân hàng phụ (nếu có)');
            $table->decimal('amount_in', 20, 2)->default(0.00)->comment('Số tiền giao dịch vào');
            $table->decimal('amount_out', 20, 2)->default(0.00)->comment('Số tiền giao dịch ra');
            $table->decimal('accumulated', 20, 2)->default(0.00)->comment('Số dư tích lũy sau giao dịch');
            $table->string('code', 250)->nullable()->default(NULL)->comment('Mã code thanh toán')->index();
            $table->text('transaction_content')->nullable()->default(NULL)->comment('Nội dung giao dịch');
            $table->string('reference_number', 255)->nullable()->default(NULL)->comment('Mã tham chiếu');
            $table->text('body')->nullable()->default(NULL)->comment('Nội dung giao dịch');
            $table->enum('status', ['pending','completed','failed'])->default('pending')->comment('Trạng thái giao dịch');
            $table->enum('transaction_type', ['purchase','renewal','refund'])->default('purchase')->comment('Loại giao dịch');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
            $table->index('user_id');
            $table->index('user_subscription_id');
            $table->index('status');
            $table->index('transaction_type');
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};