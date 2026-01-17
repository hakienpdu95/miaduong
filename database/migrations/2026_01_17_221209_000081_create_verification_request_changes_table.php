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
        Schema::create('verification_request_changes', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedBigInteger('request_id')->comment('Thuộc request')->constrained('verification_requests')->onDelete('cascade');
            $table->string('field_name', 255)->comment('Tên field (e.g., name, tax_code, file_path)')->index();
            $table->text('old_value')->nullable()->default(NULL)->comment('Giá trị cũ');
            $table->text('new_value')->comment('Giá trị mới');
            $table->enum('value_type', ['string','date','boolean','file','int','float'])->default('string')->comment('Type cho parsing/validate')->index();
            $table->boolean('is_required')->default(false)->comment('Flag highlight required')->index();
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            
            $table->index('request_id');
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_request_changes');
    }
};