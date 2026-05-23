<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 마이그레이션을 실행합니다.
     * 등급(Grades) 테이블을 생성합니다. (BelongsTo 관계 테스트용)
     */
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 등급명 (예: Bronze, Silver, Gold, VIP)
            $table->integer('discount')->default(0); // 할인율 (%)
            $table->timestamps();
        });
    }

    /**
     * 마이그레이션을 되돌립니다.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
