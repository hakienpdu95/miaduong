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
        Schema::create('product_quality_standards', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedBigInteger('product_id')->comment('Liên kết sản phẩm')->constrained('products')->onDelete('cascade')->index();
            $table->string('standard_code', 50)->comment('Mã tiêu chuẩn (ví dụ: TCVN 12850)')->index();
            $table->enum('certification_type', ['self_declared','certified','regulated','co','test_report','country_specific_test'])->default('self_declared')->comment('Loại chứng nhận (tự công bố, chứng nhận theo luật)')->index();
            $table->date('certification_date')->nullable()->default(NULL)->comment('Ngày công bố/chứng nhận');
            $table->date('expiry_date')->nullable()->default(NULL)->comment('Ngày hết hạn');
            $table->string('issued_by', 255)->nullable()->default(NULL)->comment('Tổ chức cấp');
            $table->unsignedBigInteger('document_id')->nullable()->default(NULL)->comment('Liên kết tài liệu chứng nhận')->constrained('enterprise_documents')->onDelete('set null')->index();
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('product_quality_standards');
    }
};