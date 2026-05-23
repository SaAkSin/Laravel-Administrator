<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 마이그레이션을 실행합니다.
     * 게시글(Posts) 테이블을 생성합니다. (HasMany 관계 테스트용)
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // 작성자 ID
            $table->string('title'); // 글 제목
            $table->text('content'); // 글 본문
            $table->timestamps();

            // 외래키 설정 및 Cascade 삭제 제약
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * 마이그레이션을 되돌립니다.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
