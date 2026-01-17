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
        Schema::create('post_schools', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->string('telephone', 255)->nullable()->default(NULL)->comment('Số điện thoại');
            $table->string('email', 255)->nullable()->default(NULL)->comment('Email liên hệ');
            $table->string('website', 255)->nullable()->default(NULL)->comment('Website');
            $table->string('fees_breakdown_link', 255)->nullable()->default(NULL)->comment('Liên kết đến học phí');
            $table->string('grades', 255)->nullable()->default(NULL)->comment('Cấp độ đào tạo');
            $table->string('starting_age', 50)->nullable()->default(NULL)->comment('Tuổi bắt đầu')->index();
            $table->unsignedSmallInteger('year_founded')->nullable()->default(NULL)->comment('Năm thành lập')->index();
            $table->string('language_instruction', 100)->nullable()->default(NULL)->comment('Ngôn ngữ giảng dạy');
            $table->string('foreign_languages', 255)->nullable()->default(NULL)->comment('Ngôn ngữ ngoại ngữ dạy');
            $table->unsignedInteger('population')->nullable()->default(NULL)->comment('Số lượng học sinh hiện tại')->index();
            $table->text('max_class_size')->nullable()->default(NULL)->comment('Kích thước lớp tối đa');
            $table->text('school_hours')->nullable()->default(NULL)->comment('Giờ học');
            $table->text('demographic_breakdown')->nullable()->default(NULL)->comment('Phân tích dân số');
            $table->boolean('edutrust_certified')->default(false)->comment('Chứng nhận EduTrust')->index();
            $table->text('teacher_student_ratio')->nullable()->default(NULL)->comment('Tỷ lệ giáo viên:học sinh');
            $table->boolean('admissions_interview')->default(false)->comment('Phỏng vấn nhập học')->index();
            $table->longText('facilities')->nullable()->default(NULL)->comment('Cơ sở vật chất');
            $table->longText('term_dates')->nullable()->default(NULL)->comment('Lịch học kỳ');
            $table->longText('sets_apart')->nullable()->default(NULL)->comment('Điều làm trường khác biệt');
            $table->longText('new_developments')->nullable()->default(NULL)->comment('Phát triển mới');
            $table->longText('school_culture')->nullable()->default(NULL)->comment('Văn hóa trường');
            $table->longText('annual_tuition_fee')->nullable()->default(NULL)->comment('Học phí hàng năm');
            $table->string('application_fee', 255)->nullable()->default(NULL)->comment('Phí nộp đơn');
            $table->boolean('application_fee_refundable')->default(false)->comment('Phí nộp đơn hoàn lại')->index();
            $table->string('enrolment_fee', 255)->nullable()->default(NULL)->comment('Phí nhập học');
            $table->string('deposit', 255)->nullable()->default(NULL)->comment('Tiền đặt cọc');
            $table->string('building_fee', 255)->nullable()->default(NULL)->comment('Phí xây dựng/cơ sở');
            $table->string('parents_association_fee', 255)->nullable()->default(NULL)->comment('Phí hội phụ huynh');
            $table->text('other_fees')->nullable()->default(NULL)->comment('Phí khác');
            $table->text('discounts')->nullable()->default(NULL)->comment('Giảm giá');
            $table->text('extra_curricular_activities')->nullable()->default(NULL)->comment('Hoạt động ngoại khóa');
            $table->text('enrichment_activities')->nullable()->default(NULL)->comment('Hoạt động làm giàu');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('post_schools');
    }
};