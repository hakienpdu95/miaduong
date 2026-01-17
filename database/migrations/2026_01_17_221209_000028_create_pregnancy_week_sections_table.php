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
        Schema::create('pregnancy_week_sections', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedBigInteger('pregnancy_week_id')->comment('ID tuần thai tham chiếu')->constrained('pregnancy_weeks')->onDelete('cascade')->index();
            $table->enum('section_type', ['baby_development','mother_changes','symptoms','advice','nutrition','tips','partner_tips','prenatal_care','faq'])->comment('Loại phần phát triển em bé, thay đổi mẹ, triệu chứng, lời khuyên, dinh dưỡng, mẹo, lời khuyên cho partner, chăm sóc tiền sản, FAQ - mở rộng dựa trên tham khảo')->index();
            $table->string('title', 255)->comment('Tiêu đề phần - ví dụ: Sự phát triển của em bé');
            $table->longText('content')->comment('Nội dung phần chi tiết');
            $table->unsignedTinyInteger('sort_order')->default(0)->comment('Thứ tự hiển thị (0-255)')->index();
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('pregnancy_week_sections');
    }
};