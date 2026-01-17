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
        Schema::create('ocop_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key', 255)->comment('Key duy nhất của danh mục')->unique();
            $table->string('image', 255)->nullable()->default(NULL)->comment('Đường dẫn hình ảnh');
            $table->unsignedBigInteger('parent_id')->nullable()->default(NULL)->comment('ID của danh mục cha')->constrained('ocop_categories')->onDelete('cascade');
            $table->string('code', 50)->nullable()->default(NULL)->comment('Mã code từ file CSV (I, II, a, b, 1, 2, v.v.)');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
            $table->index('key');
            $table->index('code');
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('ocop_categories');
    }
};