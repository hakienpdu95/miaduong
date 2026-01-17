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
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->string('uuid', 255)->nullable()->default(NULL)->comment('UUID')->unique();
            $table->text('connection')->comment('Lưu driver queue');
            $table->text('queue')->comment('Tên queue');
            $table->longText('payload')->comment('Nội dung job');
            $table->longText('exception')->comment('Lưu lỗi khi fail');
            $table->timestamp('failed_at')->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('failed_jobs');
    }
};