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
        Schema::create('enterprise_documents', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedBigInteger('enterprise_id')->nullable()->default(NULL)->comment('Thuộc doanh nghiệp')->constrained('enterprises')->onDelete('cascade');
            $table->unsignedBigInteger('product_id')->nullable()->default(NULL)->comment('Liên kết với sản phẩm (nếu có)')->constrained('products')->onDelete('cascade');
            $table->enum('type', ['business_license','production_certificate','food_safety','barcode','traceability','intellectual_property','geographical_indication','brand','award'])->comment('Loại tài liệu');
            $table->string('file_path', 255)->comment('Đường dẫn tệp');
            $table->string('file_name', 255)->comment('Tên tệp gốc');
            $table->boolean('is_notarized')->default(false)->comment('Có công chứng hay không')->index();
            $table->date('issued_date')->nullable()->default(NULL)->comment('Ngày cấp tài liệu');
            $table->date('expiry_date')->nullable()->default(NULL)->comment('Ngày hết hạn tài liệu');
            $table->text('description')->nullable()->default(NULL)->comment('Mô tả tài liệu (ví dụ: tên chứng nhận, tổ chức cấp)');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            $table->timestamp('deleted_at')->nullable()->default(NULL)->comment('Thời gian xóa mềm');
            
            $table->index(['enterprise_id','product_id','type']);
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('enterprise_documents');
    }
};