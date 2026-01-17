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
        Schema::create('enterprise_contacts', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedBigInteger('enterprise_id')->comment('Thuộc doanh nghiệp')->constrained('enterprises')->onDelete('cascade');
            $table->string('name', 255)->comment('Tên người liên hệ');
            $table->string('phone', 20)->comment('Số điện thoại');
            $table->char('province_code', 2)->nullable()->default(NULL)->comment('Thuộc tỉnh')->references('province_code')->on('provinces')->onDelete('set null');
            $table->char('ward_code', 5)->nullable()->default(NULL)->comment('Thuộc xã')->references('ward_code')->on('wards')->onDelete('set null');
            $table->string('address', 255)->nullable()->default(NULL)->comment('Địa chỉ');
            $table->string('email', 255)->nullable()->default(NULL)->comment('Email');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
            $table->index(['enterprise_id','province_code','ward_code']);
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('enterprise_contacts');
    }
};