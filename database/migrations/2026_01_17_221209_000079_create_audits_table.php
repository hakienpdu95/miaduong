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
        Schema::create('audits', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->string('user_type', 255)->nullable()->default(NULL)->comment('Loại user (polymorphic, nullable)')->index();
            $table->unsignedBigInteger('user_id')->nullable()->default(NULL)->comment('ID user')->index();
            $table->string('event', 255)->comment('Sự kiện (e.g., created, updated)');
            $table->string('auditable_type', 255)->comment('Loại model được kiểm toán (polymorphic)')->index();
            $table->unsignedBigInteger('auditable_id')->comment('ID model được kiểm toán')->index();
            $table->text('old_values')->nullable()->default(NULL)->comment('Giá trị cũ (JSON hoặc text)');
            $table->text('new_values')->nullable()->default(NULL)->comment('Giá trị mới (JSON hoặc text)');
            $table->text('url')->nullable()->default(NULL)->comment('URL request');
            $table->string('ip_address', 45)->nullable()->default(NULL)->comment('Địa chỉ IP');
            $table->string('user_agent', 1023)->nullable()->default(NULL)->comment('User agent');
            $table->string('tags', 255)->nullable()->default(NULL)->comment('Tags');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};