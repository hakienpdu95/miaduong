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
        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->string('queue', 255)->comment('Tên queue')->index();
            $table->longText('payload')->comment('Nội dung job');
            $table->unsignedTinyInteger('attempts')->default(0)->comment('Số lần thử');
            $table->unsignedInteger('reserved_at')->nullable()->default(NULL)->comment('Thời gian reserved');
            $table->unsignedInteger('available_at')->default(0)->comment('Thời gian available')->index();
            $table->unsignedInteger('created_at')->default(0)->comment('Thời gian tạo')->index();
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};