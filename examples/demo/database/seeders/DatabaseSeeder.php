<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Grade;
use App\Models\Role;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * 데모 애플리케이션의 데이터베이스 시딩을 가동합니다.
     * 모든 필드 타입이 완벽하게 동작하는 모습을 테스트할 수 있도록 다채롭고 풍성한 데이터를 구성합니다.
     */
    public function run(): void
    {
        // 1. 등급(Grades) 시딩
        $grades = [
            Grade::create(['name' => 'Bronze Member', 'discount' => 0]),
            Grade::create(['name' => 'Silver Member', 'discount' => 5]),
            Grade::create(['name' => 'Gold Member', 'discount' => 10]),
            Grade::create(['name' => 'VIP Elite Member', 'discount' => 20]),
        ];

        // 2. 역할(Roles) 시딩
        $roles = [
            Role::create(['name' => 'Super Administrator']),
            Role::create(['name' => 'General Administrator']),
            Role::create(['name' => 'Content Manager']),
            Role::create(['name' => 'Support Agent']),
            Role::create(['name' => 'Regular Member']),
        ];

        // 3. 사용자(Users) 데이터 30개 생성 루프
        $names = ['김민수', '이서연', '박준서', '최지우', '정우진', '강다은', '조현우', '윤소희', '장민재', '임채원', 'Michael', 'Emily', 'David', 'Sarah', 'James'];
        $domains = ['naver.com', 'gmail.com', 'daum.net', 'kakao.com', 'outlook.com'];
        $genders = ['M', 'F'];
        $colors = ['#ef4444', '#f97316', '#f59e0b', '#10b981', '#3b82f6', '#6366f1', '#8b5cf6', '#ec4899', '#6b7280'];

        for ($i = 1; $i <= 30; $i++) {
            $name = $names[array_rand($names)] . ' ' . $i;
            $email = 'user' . $i . '@' . $domains[array_rand($domains)];
            $gender = $genders[array_rand($genders)];
            
            // 유저 신규 생성
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password'), // password 타입 대응
                'grade_id' => $grades[array_rand($grades)]->id, // belongs_to 대응
                'bio_textarea' => "안녕하세요, {$name}입니다.\n현재 어드민 패키지의 textarea 필드 타입을 완벽하게 테스트하기 위해 긴 줄의 소개 텍스트를 작성하고 있습니다. 줄바꿈이 깔끔하게 잘 동작하길 기대합니다.", // textarea 대응
                'bio_wysiwyg' => "<p>안녕하세요! <strong>{$name}</strong>의 프로필 소개글입니다.</p><p>여기는 <em>WYSIWYG 에디터</em>의 HTML 태그가 정상 렌더링되는지 확인하는 필드입니다. <span style=\"color: #3b82f6;\">블루 색상의 텍스트</span>도 함께 출력됩니다.</p>", // wysiwyg 대응
                'bio_markdown' => "### {$name}의 마크다운 프로필\n\n- **취미**: 개발, 등산, 독서\n- **기술 스택**: PHP, Laravel, Javascript, Alpine.js\n\n> Laravel Administrator 패키지는 마크다운 컴포넌트도 완벽하게 지원합니다! 👍", // markdown 대응
                'is_active' => (rand(0, 10) > 2), // bool 대응 (대부분 활성화)
                'gender' => $gender, // enum 대응
                'birth_date' => date('Y-m-d', strtotime('-' . rand(20, 45) . ' years -' . rand(1, 300) . ' days')), // date 대응
                'work_start_time' => sprintf('%02d:%02d:00', rand(8, 10), rand(0, 59)), // time 대응
                'last_login_at' => date('Y-m-d H:i:s', strtotime('-' . rand(0, 10) . ' days -' . rand(0, 23) . ' hours')), // datetime 대응
                'age' => rand(20, 50), // number 대응
                'favorite_color' => $colors[array_rand($colors)], // color 대응
                'avatar' => '', // image 대응 (물리 파일 미부재로 인한 displayFile 500 방지)
                'resume' => '', // file 대응 (물리 파일 미부재로 인한 displayFile 500 방지)
            ]);

            // 4. 다대다 역할(BelongsToMany) 임의 연결 (1~2개 역할 할당)
            $randomRoleKeys = (array) array_rand($roles, rand(1, 2));
            $assignedRoleIds = [];
            foreach ($randomRoleKeys as $key) {
                $assignedRoleIds[] = $roles[$key]->id;
            }
            $user->roles()->sync($assignedRoleIds);

            // 5. 일대다 게시글(HasMany) 생성 (사용자당 2개 게시글 등록)
            for ($j = 1; $j <= 2; $j++) {
                Post::create([
                    'user_id' => $user->id,
                    'title' => "{$name}님이 작성한 {$j}번째 게시글 테스트 제목",
                    'content' => "이것은 일대다(HasMany) 관계 모델인 Post를 어드민 상에서 완벽하게 편집 및 조회하기 위해 데이터 시더로 생성한 더미 글 본문입니다. ID: {$user->id} 가 작성하였습니다.",
                ]);
            }
        }
    }
}
