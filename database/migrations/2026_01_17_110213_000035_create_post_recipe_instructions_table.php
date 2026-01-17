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
        Schema::create('post_recipe_instructions', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedBigInteger('post_recipe_id')->comment('ID công thức tham chiếu')->constrained('post_recipes')->onDelete('cascade')->index();
            $table->unsignedInteger('step_number')->default(1)->comment('Số thứ tự bước (bắt đầu từ 1)')->index();
            $table->text('description')->comment('Mô tả bước (ví dụ: Trộn bột với nước...)');
            $table->string('image', 255)->nullable()->default(NULL)->comment('Đường dẫn hình ảnh minh họa bước');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('post_recipe_instructions');
    }
};