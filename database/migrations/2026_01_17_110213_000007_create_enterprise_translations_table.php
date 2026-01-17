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
        Schema::create('enterprise_translations', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedBigInteger('enterprise_id')->comment('Thuộc doanh nghiệp')->constrained('enterprises')->onDelete('cascade')->index();
            $table->char('locale', 10)->nullable()->default(NULL)->comment('Mã ngôn ngữ (vi,en,ko,..)')->index();
            $table->string('name', 255)->nullable()->default(NULL)->comment('Tên doanh nghiệp');
            $table->string('international_name', 255)->nullable()->default(NULL)->comment('Tên quốc tế của doanh nghiệp');
            $table->string('short_name', 255)->nullable()->default(NULL)->comment('Tên viết tắt của doanh nghiệp');
            $table->text('address')->nullable()->default(NULL)->comment('Địa chỉ');
            $table->longText('brand_story')->nullable()->default(NULL)->comment('Câu chuyện thương hiệu');
            $table->longText('media_content')->nullable()->default(NULL)->comment('Truyền thông quảng bá');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('enterprise_translations');
    }
};