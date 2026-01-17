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
        Schema::create('sections', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->string('name', 255)->nullable()->default(NULL)->comment('Tên khối');
            $table->string('type', 50)->nullable()->default(NULL)->comment('Loại')->index();
            $table->boolean('affects_type')->default(true)->comment('Affects Type')->index();
            $table->unsignedBigInteger('limit')->default(5)->comment('số items max');
            $table->unsignedBigInteger('sort_order')->default(0)->comment('thứ tự trên trang chủ')->index();
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            $table->timestamp('deleted_at')->nullable()->default(NULL)->comment('Thời gian xóa mềm');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};