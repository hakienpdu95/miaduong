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
        Schema::create('event_category', function (Blueprint $table) {
            $table->unsignedBigInteger('event_id')->comment('Sự kiện')->constrained('events')->onDelete('cascade');
            $table->unsignedBigInteger('category_id')->comment('Danh mục')->constrained('event_categories')->onDelete('cascade');
            $table->primary(['event_id','category_id']);
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
            $table->index('event_id');
            $table->index('category_id');
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('event_category');
    }
};