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
        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->comment('Tên tag')->index();
            $table->string('slug', 100)->comment('Slug SEO cho tag')->unique()->index();
            $table->boolean('is_active')->default(true)->comment('Trạng thái hoạt động')->index();
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            $table->timestamp('deleted_at')->nullable()->default(NULL)->comment('Thời gian xóa mềm');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};