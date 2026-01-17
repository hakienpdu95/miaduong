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
        Schema::create('category_sort_configs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('category_id')->comment('ID danh mục')->constrained('categories')->onDelete('cascade');
            $table->enum('field', ['published_at','priority','meta.week_number','meta.chapter'])->default('published_at')->comment('Trường sắp xếp (cố định để tránh lỗi)');
            $table->enum('direction', ['asc','desc'])->default('asc')->comment('Hướng sắp xếp');
            $table->enum('type', ['numeric','date','string'])->default('string')->comment('Kiểu dữ liệu');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
            $table->index('category_id');
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('category_sort_configs');
    }
};