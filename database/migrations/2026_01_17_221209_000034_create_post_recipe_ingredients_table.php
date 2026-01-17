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
        Schema::create('post_recipe_ingredients', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedBigInteger('post_recipe_id')->comment('ID công thức tham chiếu')->constrained('post_recipes')->onDelete('cascade')->index();
            $table->string('name', 255)->comment('Tên nguyên liệu (ví dụ: Bột mì)');
            $table->string('quantity', 50)->nullable()->default(NULL)->comment('Số lượng (ví dụ: 200, hỗ trợ fraction như 1/2)');
            $table->string('unit', 50)->nullable()->default(NULL)->comment('Đơn vị (ví dụ: g, cốc, muỗng)')->index();
            $table->text('note')->nullable()->default(NULL)->comment('Ghi chú thêm (ví dụ: hữu cơ, tùy chọn)');
            $table->unsignedInteger('sort_order')->default(0)->comment('Thứ tự sắp xếp (cho hiển thị)')->index();
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('post_recipe_ingredients');
    }
};