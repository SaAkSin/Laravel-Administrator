<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    /**
     * 대량 할당이 가능한 속성 정의.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'content',
    ];

    /**
     * 이 게시글을 작성한 사용자(User) 관계 정의 (Many-to-One).
     * 게시글은 한 명의 사용자에 의해 쓰여집니다.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
