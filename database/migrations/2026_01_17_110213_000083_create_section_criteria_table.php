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
        Schema::create('section_criteria', function (Blueprint $table) {
            $table->increments('id')->comment('Khóa chính');
            $table->unsignedBigInteger('section_id')->comment('Thuộc section')->constrained('sections')->onDelete('cascade')->index();
            $table->string('criterion_type', 255)->nullable()->default(NULL)->comment('Loại (featured,priority,type,item_type,category)')->index();
            $table->string('value', 255)->nullable()->default(NULL)->comment('Giá trị (true cho featured, 50 cho priority, recipe review cho item_type)');
            $table->string('operator', 255)->nullable()->default(NULL)->comment('Giá trị toán tử');
            $table->unsignedBigInteger('sort_order')->default(0)->comment('thứ tự')->index();
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            $table->timestamp('deleted_at')->nullable()->default(NULL)->comment('Thời gian xóa mềm');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('section_criteria');
    }
};