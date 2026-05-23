<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 마이그레이션을 실행합니다.
     * 모든 필드 타입을 다채롭게 테스트할 수 있는 마스터 스키마를 구성합니다.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // key 타입 테스트용
            $table->string('name'); // text 타입
            $table->string('email')->unique(); // text 타입
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password'); // password 타입
            
            // 추가적인 어드민 테스트용 필드 타입들
            $table->unsignedBigInteger('grade_id')->nullable(); // belongs_to 관계 테스트용
            $table->text('bio_textarea')->nullable(); // textarea 타입
            $table->text('bio_wysiwyg')->nullable(); // wysiwyg 타입
            $table->text('bio_markdown')->nullable(); // markdown 타입
            $table->boolean('is_active')->nullable()->default(true); // bool 타입
            $table->enum('gender', ['M', 'F'])->nullable(); // enum 타입
            $table->date('birth_date')->nullable(); // date 타입
            $table->time('work_start_time')->nullable(); // time 타입
            $table->dateTime('last_login_at')->nullable(); // datetime 타입
            $table->integer('age')->nullable(); // number 타입
            $table->string('favorite_color')->nullable(); // color 타입
            $table->string('avatar')->nullable(); // image 타입
            $table->string('resume')->nullable(); // file 타입

            $table->rememberToken();
            $table->timestamps();

            // 외래키 설정 (Grade가 삭제되어도 복구 가능하도록 null 처리)
            $table->foreign('grade_id')->references('id')->on('grades')->onDelete('set null');
        });
    }

    /**
     * 마이그레이션을 되돌립니다.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
