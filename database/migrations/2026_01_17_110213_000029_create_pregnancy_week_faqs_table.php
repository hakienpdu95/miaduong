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
        Schema::create('pregnancy_week_faqs', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedBigInteger('pregnancy_week_id')->comment('ID tuần thai tham chiếu')->constrained('pregnancy_weeks')->onDelete('cascade')->index();
            $table->string('question', 255)->comment('Câu hỏi');
            $table->text('answer')->comment('Câu trả lời');
            $table->unsignedTinyInteger('sort_order')->default(0)->comment('Thứ tự hiển thị')->index();
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('pregnancy_week_faqs');
    }
};