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
        Schema::create('post_recipe_nutritions', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedBigInteger('post_recipe_id')->comment('ID công thức tham chiếu')->constrained('post_recipes')->onDelete('cascade')->index();
            $table->unsignedInteger('calories')->nullable()->default(NULL)->comment('Lượng calo (kcal)');
            $table->decimal('fat', 8, 2)->nullable()->default(NULL)->comment('Chất béo (g)');
            $table->decimal('saturated_fat', 8, 2)->nullable()->default(NULL)->comment('Chất béo bão hòa (g)');
            $table->decimal('carbohydrates', 8, 2)->nullable()->default(NULL)->comment('Carbohydrate (g)');
            $table->decimal('sugar', 8, 2)->nullable()->default(NULL)->comment('Đường (g)');
            $table->decimal('protein', 8, 2)->nullable()->default(NULL)->comment('Protein (g)');
            $table->decimal('fiber', 8, 2)->nullable()->default(NULL)->comment('Chất xơ (g)');
            $table->decimal('sodium', 8, 2)->nullable()->default(NULL)->comment('Natri (mg)');
            $table->decimal('cholesterol', 8, 2)->nullable()->default(NULL)->comment('Cholesterol (mg)');
            $table->string('serving_size', 100)->nullable()->default(NULL)->comment('Kích thước khẩu phần (ví dụ: 1 serving)');
            $table->json('additional_info')->nullable()->default(NULL)->comment('Thông tin dinh dưỡng bổ sung (JSON cho các fields khác)');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('post_recipe_nutritions');
    }
};