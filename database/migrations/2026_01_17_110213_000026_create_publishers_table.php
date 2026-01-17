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
        Schema::create('publishers', function (Blueprint $table) {
            $table->increments('id')->comment('Khóa chính');
            $table->string('name', 255)->comment('Tên nhà xuất bản');
            $table->string('slug', 255)->comment('Slug SEO')->unique()->index();
            $table->string('website', 255)->nullable()->default(NULL)->comment('Website');
            $table->string('logo', 255)->nullable()->default(NULL)->comment('Logo nhà xuất bản');
            $table->boolean('is_active')->default(true)->comment('Trạng thái hoạt động')->index();
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
            $table->index('logo');
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('publishers');
    }
};