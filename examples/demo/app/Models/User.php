<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * 대량 할당이 가능한 속성 정의.
     * 모든 테스트 필드 타입을 등록합니다.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'grade_id',
        'bio_textarea',
        'bio_wysiwyg',
        'bio_markdown',
        'is_active',
        'gender',
        'birth_date',
        'work_start_time',
        'last_login_at',
        'age',
        'favorite_color',
        'avatar',
        'resume',
    ];

    /**
     * JSON 직렬화 시 숨겨야 할 속성들.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * 형변환(Casts) 속성 정의.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'birth_date' => 'date',
        'last_login_at' => 'datetime',
    ];

    /**
     * 사용자가 속한 등급(Grade) 관계 정의 (BelongsTo).
     */
    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    /**
     * 사용자가 가진 역할(Roles) 관계 정의 (BelongsToMany).
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * 사용자가 작성한 게시글(Posts) 관계 정의 (HasMany).
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
