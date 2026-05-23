<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    /**
     * 대량 할당이 가능한 속성 정의.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'discount',
    ];

    /**
     * 등급에 속한 모든 사용자(Users) 관계 정의 (One-to-Many).
     * 한 등급에는 여러 사용자가 속할 수 있습니다.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
