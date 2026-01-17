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
        Schema::create('post_school_school_type', function (Blueprint $table) {
            $table->unsignedBigInteger('post_school_id')->comment('ID trường')->constrained('post_schools')->onDelete('cascade')->index();
            $table->unsignedBigInteger('school_type_id')->comment('ID loại hình')->constrained('school_types')->onDelete('cascade')->index();
            $table->primary(['post_school_id','school_type_id']);
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('post_school_school_type');
    }
};