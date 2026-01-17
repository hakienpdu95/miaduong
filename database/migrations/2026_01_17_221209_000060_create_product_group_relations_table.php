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
        Schema::create('product_group_relations', function (Blueprint $table) {
            $table->unsignedBigInteger('product_group_id')->comment('Nhóm sản phẩm')->constrained('product_groups')->onDelete('cascade');
            $table->unsignedBigInteger('product_id')->comment('Sản phẩm')->constrained('products')->onDelete('cascade');
            $table->primary(['product_group_id','product_id']);
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('product_group_relations');
    }
};