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
        Schema::create('test_table', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('tiny_number')->default(0)->comment('Số nhỏ');
            $table->smallInteger('small_number')->default(0)->comment('Số trung bình');
            $table->mediumInteger('medium_number')->default(0)->comment('Số trung');
            $table->bigInteger('big_number')->default(0)->comment('Số lớn');
            $table->decimal('price', 10, 2)->default(0.00)->comment('Giá tiền');
            $table->float('weight')->default(0.0)->comment('Trọng lượng');
            $table->double('precision')->default(0.0)->comment('Độ chính xác');
            $table->char('fixed_text', 50)->comment('Chuỗi cố định');
            $table->longText('long_description')->nullable()->default(NULL)->comment('Mô tả dài');
            $table->binary('binary_data')->nullable()->default(NULL)->comment('Dữ liệu nhị phân');
            $table->date('event_date')->nullable()->default(NULL)->comment('Ngày sự kiện');
            $table->time('event_time')->nullable()->default(NULL)->comment('Thời gian sự kiện');
            $table->year('event_year')->nullable()->default(NULL)->comment('Năm sự kiện');
            $table->enum('status', ['active','inactive','pending'])->default('active')->comment('Trạng thái');
            $table->set('roles', ['admin','editor','user'])->default('user')->comment('Vai trò');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('test_table');
    }
};