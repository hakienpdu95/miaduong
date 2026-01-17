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
        Schema::create('product_award_relations', function (Blueprint $table) {
            $table->unsignedBigInteger('award_id')->comment('Giải thưởng')->constrained('awards')->onDelete('cascade');
            $table->unsignedBigInteger('product_id')->comment('Sản phẩm')->constrained('products')->onDelete('cascade');
            $table->primary(['award_id','product_id']);
            $table->string('certificate_code', 50)->nullable()->default(NULL)->comment('Mã chứng nhận cụ thể cho sản phẩm này')->index();
            $table->date('issued_date')->nullable()->default(NULL)->comment('Ngày cấp');
            $table->date('expiry_date')->nullable()->default(NULL)->comment('Ngày hết hạn');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('product_award_relations');
    }
};