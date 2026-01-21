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
        Schema::create('serial_counters', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->char('prefix', 8)->nullable()->default(NULL)->comment('Serial Number')->unique()->index();
            $table->unsignedBigInteger('last_number')->nullable()->default(NULL)->comment('Last Number');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('serial_counters');
    }
};