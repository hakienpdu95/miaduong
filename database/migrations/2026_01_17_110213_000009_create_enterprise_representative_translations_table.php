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
        Schema::create('enterprise_representative_translations', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedBigInteger('representative_id')->comment('Thuộc người đại diện DN')->constrained('enterprise_representatives')->onDelete('cascade')->unique()->index();
            $table->char('locale', 5)->comment('Mã ngôn ngữ (vi,en,ko,..)')->unique()->index();
            $table->string('name', 255)->comment('Tên người đại diện');
            $table->string('position', 255)->comment('Chức vụ');
            $table->string('phone', 20)->nullable()->default(NULL)->comment('Số điện thoại');
            $table->string('email', 255)->nullable()->default(NULL)->comment('Email');
            $table->enum('gender', ['male','female','other'])->default('other')->comment('Giới tính');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('enterprise_representative_translations');
    }
};