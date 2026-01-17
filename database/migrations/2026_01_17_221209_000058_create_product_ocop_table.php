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
        Schema::create('product_ocop', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedBigInteger('product_id')->nullable()->default(NULL)->comment('Sản phẩm')->constrained('products')->onDelete('cascade');
            $table->enum('ocop_rating', ['3','4','5','potential_5'])->default('3')->comment('Xếp hạng OCOP')->index();
            $table->string('ocop_certificate', 255)->nullable()->default(NULL)->comment('Mã chứng nhận OCOP');
            $table->date('certificate_issued_date')->nullable()->default(NULL)->comment('Ngày cấp chứng nhận OCOP');
            $table->date('certificate_expiry_date')->nullable()->default(NULL)->comment('Ngày hết hạn (36 tháng kể từ ngày Quyết định có hiệu lực)');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
            $table->index('product_id');
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('product_ocop');
    }
};