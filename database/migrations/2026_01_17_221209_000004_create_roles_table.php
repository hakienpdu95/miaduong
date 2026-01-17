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
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->string('name', 255)->comment('Tên vai trò (ví dụ: province, commune, enterprise, employee, editor)')->unique();
            $table->text('description')->nullable()->default(NULL)->comment('Mô tả vai trò');
            $table->unsignedTinyInteger('priority')->default(0)->comment('Thứ tự ưu tiên vai trò')->index();
            $table->boolean('is_active')->default(true)->comment('Trạng thái vai trò')->index();
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        

        DB::table('roles')->updateOrInsert( ['name' => 'administrator'], ['name' => 'administrator','description' => 'Quản lý toàn hệ thống - có quyền cài đặt và quản lý người dùng','priority' => '110','is_active' => '1'] );
        DB::table('roles')->updateOrInsert( ['name' => 'province'], ['name' => 'province','description' => 'Quản lý cấp tỉnh - giám sát cấp xã và doanh nghiệp','priority' => '100','is_active' => '1'] );
        DB::table('roles')->updateOrInsert( ['name' => 'commune'], ['name' => 'commune','description' => 'Quản lý cấp xã - giám sát doanh nghiệp trong khu vực','priority' => '90','is_active' => '1'] );
        DB::table('roles')->updateOrInsert( ['name' => 'enterprise'], ['name' => 'enterprise','description' => 'Tài khoản doanh nghiệp - quản lý nhân viên và nội dung doanh nghiệp','priority' => '80','is_active' => '1'] );
        DB::table('roles')->updateOrInsert( ['name' => 'content_manager'], ['name' => 'content_manager','description' => 'Quản lý toàn bộ nội dung bài viết - danh mục và bình luận','priority' => '75','is_active' => '1'] );
        DB::table('roles')->updateOrInsert( ['name' => 'shop_manager'], ['name' => 'shop_manager','description' => 'Quản lý sản phẩm - đơn hàng và báo cáo cửa hàng','priority' => '70','is_active' => '1'] );
        DB::table('roles')->updateOrInsert( ['name' => 'seo_manager'], ['name' => 'seo_manager','description' => 'Quản lý SEO - metadata và tối ưu hóa tìm kiếm','priority' => '65','is_active' => '1'] );
        DB::table('roles')->updateOrInsert( ['name' => 'moderator'], ['name' => 'moderator','description' => 'Quản lý bình luận và hỗ trợ duyệt nội dung','priority' => '60','is_active' => '1'] );
        DB::table('roles')->updateOrInsert( ['name' => 'employee'], ['name' => 'employee','description' => 'Tài khoản nhân viên - thực hiện nhiệm vụ được giao bởi doanh nghiệp','priority' => '50','is_active' => '1'] );
        DB::table('roles')->updateOrInsert( ['name' => 'editor'], ['name' => 'editor','description' => 'Chuyên tạo và chỉnh sửa nội dung - gửi duyệt trước khi xuất bản','priority' => '40','is_active' => '1'] );
        DB::table('roles')->updateOrInsert( ['name' => 'contributor'], ['name' => 'contributor','description' => 'Tạo nội dung nhưng cần duyệt trước khi xuất bản','priority' => '30','is_active' => '1'] );
        DB::table('roles')->updateOrInsert( ['name' => 'subscriber'], ['name' => 'subscriber','description' => 'Người dùng đăng ký - chỉ xem nội dung và quản lý hồ sơ','priority' => '10','is_active' => '1'] );
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};