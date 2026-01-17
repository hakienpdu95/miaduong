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
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Khóa chính');
            $table->unsignedBigInteger('user_id')->comment('Người dùng')->constrained('users')->onDelete('cascade');
            $table->string('provider', 50)->comment('Provider mạng xã hội (google, facebook, v.v.)')->index();
            $table->string('provider_id', 255)->comment('ID duy nhất từ provider')->unique()->index();
            $table->text('token')->nullable()->default(NULL)->comment('Access token từ provider');
            $table->text('refresh_token')->nullable()->default(NULL)->comment('Refresh token từ provider');
            $table->timestamp('expires_at')->nullable()->default(NULL)->comment('Thời gian hết hạn token');
            $table->string('avatar', 255)->nullable()->default(NULL)->comment('URL ảnh đại diện từ mạng xã hội');
            $table->timestamp('created_at')->nullable()->default(NULL)->comment('Thời gian tạo');
            $table->timestamp('updated_at')->nullable()->default(NULL)->comment('Thời gian cập nhật');
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('social_accounts');
    }
};