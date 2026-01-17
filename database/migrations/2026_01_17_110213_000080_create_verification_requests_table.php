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
        Schema::create('verification_requests', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->string('verifiable_type', 255)->comment('Loại entity')->index();
            $table->unsignedBigInteger('verifiable_id')->comment('ID entity')->index();
            $table->unsignedBigInteger('submitter_id')->comment('Người submit (DN)')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('approver_id')->nullable()->default(NULL)->comment('Approver hiện tại')->constrained('users')->onDelete('set null');
            $table->enum('current_level', ['level1','level2','admin_override'])->default('level1')->comment('Level duyệt (dễ mở rộng thêm level3)')->index();
            $table->enum('status', ['pending','in_review','approved','rejected'])->default('pending')->comment('Trạng thái workflow')->index();
            $table->text('notes')->nullable()->default(NULL)->comment('Lý do reject/comment');
            $table->timestamp('approved_at')->nullable()->default(NULL)->comment('Thời gian approved');
            $table->unsignedInteger('version')->default(1)->comment('Version cho multiple updates')->index();
            $table->unsignedBigInteger('audit_id')->nullable()->default(NULL)->comment('Link đến audit log sau approved')->constrained('audits')->onDelete('set null')->index();
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_requests');
    }
};