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
        Schema::create('enterprise_representatives', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedBigInteger('enterprise_id')->comment('Thuộc doanh nghiệp')->constrained('enterprises')->onDelete('cascade');
            $table->string('phone', 20)->nullable()->default(NULL)->comment('Số điện thoại');
            $table->string('email', 255)->nullable()->default(NULL)->comment('Email');
            $table->enum('gender', ['male','female','other'])->default('other')->comment('Giới tính');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
            $table->index('enterprise_id');
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('enterprise_representatives');
    }
};