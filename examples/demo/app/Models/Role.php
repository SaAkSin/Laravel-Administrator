<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * 대량 할당이 가능한 속성 정의.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * 이 역할을 소유한 모든 사용자(Users) 관계 정의 (Many-to-Many).
     * 하나의 역할은 여러 사용자에게 할당될 수 있습니다.
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
