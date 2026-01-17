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
        Schema::create('comment_paths', function (Blueprint $table) {
            $table->unsignedBigInteger('ancestor_id')->comment('Bình luận tổ tiên')->constrained('comments')->onDelete('cascade')->index();
            $table->unsignedBigInteger('descendant_id')->comment('Bình luận con cháu')->constrained('comments')->onDelete('cascade')->index();
            $table->unsignedInteger('depth')->comment('Độ sâu (khoảng cách từ ancestor đến descendant)');
            $table->primary(['ancestor_id','descendant_id']);
            
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('comment_paths');
    }
};