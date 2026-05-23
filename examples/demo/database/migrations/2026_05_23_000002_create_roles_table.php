<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 마이그레이션을 실행합니다.
     * 역할(Roles) 및 사용자-역할 다대다(BelongsToMany) 매핑 피벗 테이블을 생성합니다.
     */
    public function up(): void
    {
        // 1. Roles 테이블 생성
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 역할명 (예: Administrator, Editor, Guest)
            $table->timestamps();
        });

        // 2. Pivot 테이블 (role_user) 생성
        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('role_id');
            $table->timestamps();

            // 외래키 설정 및 Cascade 삭제 제약
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    /**
     * 마이그레이션을 되돌립니다.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('roles');
    }
};
