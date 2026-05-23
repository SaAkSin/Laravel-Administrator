<?php

/**
 * User 모델의 종합 관리자 설정 파일입니다.
 * 패키지가 지원하는 모든 필드 타입을 한눈에 테스트할 수 있도록 마스터 명세서를 완벽히 구성합니다.
 */
function users()
{
	return array(
		'title' => 'Users',
		'single' => 'user',
		'model' => 'App\Models\User',
		'form_width' => 600, // 상세 편집 창의 가로폭을 복잡한 필드도 넉넉히 수용할 수 있게 넓힙니다.

		/**
		 * 메인 그리드 테이블에 출력될 컬럼 정의
		 */
		'columns' => array(
			'id' => array(
				'title' => 'ID',
			),
			'avatar' => array(
				'title' => 'Avatar',
				'output' => function ($value) {
					if ($value) {
						return '<img src="/uploads/avatars/' . $value . '" style="height: 35px; width: 35px; border-radius: 50%; object-fit: cover;">';
					}
					return '<span style="color: #9ca3af;">No Image</span>';
				}
			),
			'name' => array(
				'title' => 'Name',
			),
			'email' => array(
				'title' => 'Email',
			),
			'grade' => array(
				'title' => 'Grade',
				'relationship' => 'grade',
				'select' => '(:table).name',
			),
			'is_active' => array(
				'title' => 'Status',
				'output' => function ($value) {
					return $value ? '<span style="color: #10b981; font-weight: bold;">Active</span>' : '<span style="color: #ef4444;">Inactive</span>';
				}
			),
			'favorite_color' => array(
				'title' => 'Color',
				'output' => function ($value) {
					if ($value) {
						return '<div style="background-color: ' . $value . '; width: 25px; height: 25px; border-radius: 4px; border: 1px solid #d1d5db;"></div>';
					}
					return '-';
				}
			),
		),

		/**
		 * 우측 필터 사이드바에서 사용할 검색 필터 정의
		 */
		'filters' => array(
			'id' => array(
				'title' => 'ID',
				'type' => 'key',
			),
			'name' => array(
				'title' => 'Name',
				'type' => 'text',
			),
			'email' => array(
				'title' => 'Email',
				'type' => 'text',
			),
			'grade' => array(
				'title' => 'Grade',
				'type' => 'relationship',
				'name_field' => 'name',
			),
			'is_active' => array(
				'title' => 'Active Status',
				'type' => 'bool',
			),
			'gender' => array(
				'title' => 'Gender',
				'type' => 'enum',
				'options' => array(
					'M' => 'Male',
					'F' => 'Female',
				)
			),
		),

		/**
		 * 추가 및 수정 폼에서 입력받을 18가지 필드 속성 정의
		 */
		'edit_fields' => array(
			'id' => array(
				'title' => 'ID',
				'type' => 'key',
			),
			'name' => array(
				'title' => 'Name',
				'type' => 'text',
			),
			'email' => array(
				'title' => 'Email',
				'type' => 'text',
			),
			'password' => array(
				'title' => 'Password',
				'type' => 'password',
			),
			
			// 1. BelongsTo 관계형 필드
			'grade' => array(
				'title' => 'Grade (BelongsTo)',
				'type' => 'relationship',
				'name_field' => 'name',
			),

			// 2. BelongsToMany 다대다 관계형 필드
			'roles' => array(
				'title' => 'Roles (BelongsToMany)',
				'type' => 'relationship',
				'name_field' => 'name',
			),

			// 3. HasMany 일대다 관계형 필드
			'posts' => array(
				'title' => 'Posts (HasMany)',
				'type' => 'relationship',
				'name_field' => 'title',
			),

			// 4. 일반 Textarea 필드
			'bio_textarea' => array(
				'title' => 'Introduction (Textarea)',
				'type' => 'textarea',
				'height' => 100,
			),

			// 5. WYSIWYG 풍부한 웹에디터 필드
			'bio_wysiwyg' => array(
				'title' => 'Intro HTML (WYSIWYG)',
				'type' => 'wysiwyg',
				'height' => 150,
			),

			// 6. 마크다운 에디터 필드
			'bio_markdown' => array(
				'title' => 'Intro Markdown (Markdown)',
				'type' => 'markdown',
				'height' => 150,
			),

			// 7. Boolean 토글 스위치 필드
			'is_active' => array(
				'title' => 'Account Active (Boolean)',
				'type' => 'bool',
			),

			// 8. Enum 선택 콤보박스 필드
			'gender' => array(
				'title' => 'Gender (Enum)',
				'type' => 'enum',
				'options' => array(
					'M' => 'Male',
					'F' => 'Female',
				),
			),

			// 9. 날짜(Date) 선택 픽커 필드
			'birth_date' => array(
				'title' => 'Birth Date (Date)',
				'type' => 'date',
			),

			// 10. 시간(Time) 선택 픽커 필드
			'work_start_time' => array(
				'title' => 'Work Start Time (Time)',
				'type' => 'time',
			),

			// 11. 날짜 시간(Datetime) 선택 픽커 필드
			'last_login_at' => array(
				'title' => 'Last Login At (Datetime)',
				'type' => 'datetime',
			),

			// 12. 숫자(Number) 입력 필드
			'age' => array(
				'title' => 'Age (Number)',
				'type' => 'number',
			),

			// 13. 컬러(Color) 픽커 필드
			'favorite_color' => array(
				'title' => 'Favorite Color (Color)',
				'type' => 'color',
			),

			// 14. 이미지 업로드 필드
			'avatar' => array(
				'title' => 'Avatar Photo (Image)',
				'type' => 'image',
				'location' => public_path() . '/uploads/avatars/',
				'naming' => 'random',
				'length' => 20,
				'size_limit' => 2, // 2MB 제한
				'sizes' => array(
					array(100, 100, 'crop', public_path() . '/uploads/avatars/thumbs/', 100),
				)
			),

			// 15. 파일 업로드 필드
			'resume' => array(
				'title' => 'Resume Document (File)',
				'type' => 'file',
				'location' => public_path() . '/uploads/resumes/',
				'naming' => 'random',
				'length' => 20,
				'size_limit' => 5, // 5MB 제한
				'mimes' => 'pdf,doc,docx,txt',
			),
		),
	);
}
