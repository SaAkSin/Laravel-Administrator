<?php
/**
 * 관리자 메인 테이블 템플릿 (Alpine.js 및 Tailwind CSS v3 기반)
 * 
 * 이 파일은 기존의 Knockout.js 바인딩 구문을 완전히 제거하고, Modern한 Alpine.js 디렉티브로 교체하였으며
 * Tailwind CSS v3를 통해 현대적이고 태블릿/모바일 반응형을 지원하며, 세련된 카드 레이아웃과
 * 부드러운 마이크로 애니메이션이 가미된 프리미엄 스타일 테이블 뷰를 렌더링합니다.
 */
?>
<div class="relative w-full overflow-hidden bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border border-slate-200/60 dark:border-slate-800/80 rounded-2xl shadow-xl shadow-slate-100 dark:shadow-none transition-all duration-300"
     x-data="{ 
        // 필요한 경우 Alpine.js 로컬 상태를 선언할 수 있으나, 보통 상위 컴포넌트의 상태를 사용합니다.
     }">

	<!-- 헤더 영역: 테이블 타이틀 및 글로벌 액션 / 생성 버튼 -->
	<div class="p-6 border-b border-slate-200/60 dark:border-slate-800/80 flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-slate-50/20 dark:bg-slate-900/20">
		<div>
			<h2 class="text-xl font-bold text-slate-800 dark:text-slate-100 tracking-tight" x-text="modelTitle"></h2>
			<p class="text-xs text-slate-400 dark:text-slate-500 mt-1">
				<?php echo trans('administrator::administrator.active_model') ?? '데이터 모델의 실시간 리스트 관리 화면입니다.' ?>
			</p>
		</div>

		<!-- 액션 제어 영역 -->
		<div class="flex flex-wrap items-center gap-2.5">
			<!-- 글로벌 액션 렌더링 -->
			<template x-if="globalActions && globalActions.length">
				<div class="flex flex-wrap gap-2">
					<template x-for="(action, idx) in globalActions" :key="action.action_name || idx">
						<template x-if="action.has_permission">
							<button type="button"
									@click="customAction(false, action.action_name, action.messages, action.confirmation)"
									:value="action.title"
									:disabled="freezeForm || freezeActions"
									x-text="action.title"
									class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold rounded-lg text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200/80 dark:bg-slate-800 dark:hover:bg-slate-700/80 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 border border-slate-200/50 dark:border-slate-700/50 active:scale-95">
							</button>
						</template>
					</template>
				</div>
			</template>

			<!-- 새 항목 추가 버튼 (권한 확인 필요) -->
			<template x-if="actionPermissions && actionPermissions.create">
				<a :href="baseUrl + modelName + '/new'"
				   class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-600 dark:hover:bg-indigo-500 transition-all duration-200 shadow-md shadow-indigo-100 dark:shadow-none hover:shadow-indigo-200 active:scale-95">
					<!-- 플러스 아이콘 -->
					<svg class="w-4.5 h-4.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
					</svg>
					<span x-text="'<?php echo trans('administrator::administrator.new') ?> ' + modelSingle"></span>
				</a>
			</template>
		</div>
	</div>

	<!-- 글로벌 알림 및 상태 메시지 영역 -->
	<template x-if="globalStatusMessage">
		<div class="mx-6 mt-4 p-4 rounded-xl border text-sm flex items-center justify-between transition-all duration-300"
			 x-show="globalStatusMessage"
			 x-transition:enter="transition ease-out duration-300"
			 x-transition:enter-start="opacity-0 -translate-y-2"
			 x-transition:enter-end="opacity-100 translate-y-0"
			 x-transition:leave="transition ease-in duration-200"
			 x-transition:leave-start="opacity-100 translate-y-0"
			 x-transition:leave-end="opacity-0 -translate-y-2"
			 :class="{
				 'bg-red-50/80 text-red-800 border-red-100 dark:bg-red-950/30 dark:text-red-400 dark:border-red-900/50': globalStatusMessageType === 'error',
				 'bg-green-50/80 text-green-800 border-green-100 dark:bg-green-950/30 dark:text-green-400 dark:border-green-900/50': globalStatusMessageType === 'success'
			 }">
			<div class="flex items-center space-x-2">
				<!-- 에러 아이콘 -->
				<template x-if="globalStatusMessageType === 'error'">
					<svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
					</svg>
				</template>
				<!-- 성공 아이콘 -->
				<template x-if="globalStatusMessageType === 'success'">
					<svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
					</svg>
				</template>
				<span class="font-medium" x-text="globalStatusMessage"></span>
			</div>
			<!-- 상태창 닫기 버튼 -->
			<button @click="globalStatusMessage = ''" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
				</svg>
			</button>
		</div>
	</template>

	<!-- 페이지네이션 컨트롤러 영역 -->
	<div class="p-4 border-b border-slate-200/60 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-900/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
		<!-- 페이지당 아이템 개수 설정 -->
		<div class="flex items-center space-x-2.5 text-xs text-slate-500 dark:text-slate-400">
			<span class="font-semibold text-slate-600 dark:text-slate-400"><?php echo trans('administrator::administrator.itemsperpage') ?></span>
			<div class="relative inline-block">
				<select x-model="rowsPerPage"
						class="appearance-none pl-3 pr-8 py-1.5 text-xs font-semibold rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 dark:focus:border-indigo-500 transition-all duration-200 cursor-pointer shadow-sm">
					<template x-for="option in rowsPerPageOptions" :key="option">
						<option :value="option" x-text="option" :selected="option == rowsPerPage"></option>
					</template>
				</select>
				<!-- 셀렉트 박스 화살표 디자인 -->
				<div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
					<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
					</svg>
				</div>
			</div>
		</div>

		<!-- 이전/다음 페이징 제어 장치 -->
		<div class="flex items-center space-x-2">
			<!-- 이전 페이지 버튼 -->
			<button type="button"
					@click="page('prev')"
					:disabled="pagination.isFirst || !pagination.last || !initialized"
					class="inline-flex items-center px-3.5 py-1.5 text-xs font-semibold rounded-lg text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 disabled:opacity-40 disabled:hover:bg-white dark:disabled:hover:bg-slate-800 disabled:cursor-not-allowed transition-all duration-150 shadow-sm active:scale-95">
				<svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
				</svg>
				<?php echo trans('administrator::administrator.previous') ?>
			</button>

			<!-- 페이지 다이렉트 이동 인풋 -->
			<div class="flex items-center space-x-1.5">
				<input type="number"
					   x-model.number="pagination.page"
					   :disabled="pagination.last === 0 || !initialized"
					   class="w-12 text-center py-1.5 px-1.5 text-xs font-semibold rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 dark:focus:border-indigo-500 transition-all [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none shadow-sm" />
				<span class="text-xs text-slate-400 dark:text-slate-500 font-semibold" x-text="'/ ' + pagination.last"></span>
			</div>

			<!-- 다음 페이지 버튼 -->
			<button type="button"
					@click="page('next')"
					:disabled="pagination.isLast || !pagination.last || !initialized"
					class="inline-flex items-center px-3.5 py-1.5 text-xs font-semibold rounded-lg text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 disabled:opacity-40 disabled:hover:bg-white dark:disabled:hover:bg-slate-800 disabled:cursor-not-allowed transition-all duration-150 shadow-sm active:scale-95">
				<?php echo trans('administrator::administrator.next') ?>
				<svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
				</svg>
			</button>
		</div>
	</div>

	<!-- 프리미엄 스타일 테이블 컨테이너 (반응형 터치 스크롤 지원) -->
	<div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200 dark:scrollbar-thumb-slate-800">
		<table class="w-full text-left border-collapse min-w-[768px]">
			<thead>
				<tr class="bg-slate-50/80 dark:bg-slate-900/80 border-b border-slate-200/60 dark:border-slate-800/80 select-none">
					<template x-for="column in columns" :key="column.column_name">
						<th x-show="column.visible"
							@click="column.sortable && setSortOptions(column.sort_field ? column.sort_field : column.column_name)"
							:class="column.sortable ? 'cursor-pointer hover:bg-slate-100/50 dark:hover:bg-slate-800/50 transition-colors duration-150' : ''"
							class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 tracking-wider uppercase relative">
							
							<div class="flex items-center space-x-1.5">
								<span x-text="column.title" class="font-bold"></span>
								
								<!-- 정렬 방향 표시 마이크로 인터랙션 아이콘 -->
								<template x-if="column.sortable">
									<span class="inline-flex flex-col justify-center">
										<!-- 오름차순 (ASC) 활성화 상태 -->
										<svg class="w-3 h-3 transition-all duration-200" 
											 :class="(column.column_name == sortOptions.field || column.sort_field == sortOptions.field) && sortOptions.direction === 'asc' ? 'text-indigo-600 dark:text-indigo-400 scale-110' : 'text-slate-300 dark:text-slate-600 opacity-40'" 
											 fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"></path>
										</svg>
										<!-- 내림차순 (DESC) 활성화 상태 -->
										<svg class="w-3 h-3 -mt-1 transition-all duration-200" 
											 :class="(column.column_name == sortOptions.field || column.sort_field == sortOptions.field) && sortOptions.direction === 'desc' ? 'text-indigo-600 dark:text-indigo-400 scale-110' : 'text-slate-300 dark:text-slate-600 opacity-40'" 
											 fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
										</svg>
									</span>
								</template>
							</div>
						</th>
					</template>
				</tr>
			</thead>
			<tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
				<!-- 테이블의 로우 루프 (Alpine.js dynamic x-for 사용) -->
				<template x-for="(row, rowIndex) in rows" :key="row[primaryKey] ? row[primaryKey].raw : rowIndex">
					<tr @click="clickItem(row[primaryKey].raw)"
						:class="{
							'bg-slate-50/20 dark:bg-slate-800/10': rowIndex % 2 === 1,
							'bg-white dark:bg-slate-900': rowIndex % 2 !== 1,
							'bg-indigo-50/65 dark:bg-indigo-950/20 border-indigo-200/80 dark:border-indigo-900/50 shadow-inner font-medium': row[primaryKey].raw == itemLoadingId
						}"
						class="cursor-pointer hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-all duration-200 group">
						
						<!-- 행의 각 셀 렌더링 -->
						<template x-for="column in columns" :key="column.column_name">
							<td x-show="column.visible"
								x-html="row[column.column_name].rendered"
								class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300 group-hover:text-slate-800 dark:group-hover:text-slate-100 transition-colors duration-150">
							</td>
						</template>
					</tr>
				</template>
			</tbody>
		</table>
	</div>

	<!-- 로딩 상태 모달 오버레이 -->
	<div class="absolute inset-0 z-10 flex flex-col items-center justify-center bg-white/75 dark:bg-slate-950/75 backdrop-blur-sm transition-all duration-300"
		 x-show="loadingRows"
		 x-transition:enter="transition ease-out duration-200"
		 x-transition:enter-start="opacity-0"
		 x-transition:enter-end="opacity-100"
		 x-transition:leave="transition ease-in duration-200"
		 x-transition:leave-start="opacity-100"
		 x-transition:leave-end="opacity-0">
		<div class="relative flex items-center justify-center">
			<!-- 외부 정적 로딩 서클 -->
			<div class="w-12 h-12 rounded-full border-4 border-slate-100 dark:border-slate-850"></div>
			<!-- 액티브 회전 인디케이터 -->
			<div class="absolute w-12 h-12 rounded-full border-4 border-indigo-600 dark:border-indigo-500 border-t-transparent animate-spin"></div>
		</div>
		<span class="mt-4 text-xs font-semibold text-slate-600 dark:text-slate-400 animate-pulse tracking-wider uppercase">
			<?php echo trans('administrator::administrator.loading') ?>
		</span>
	</div>

	<!-- 검색 결과가 전혀 없는 경우 (빈 상태 피드백 화면) -->
	<div class="flex flex-col items-center justify-center p-16 text-center bg-white dark:bg-slate-900 border-t border-slate-200/60 dark:border-slate-800/80"
		 x-show="pagination.last === 0"
		 x-transition:enter="transition ease-out duration-300"
		 x-transition:enter-start="opacity-0 translate-y-4"
		 x-transition:enter-end="opacity-100 translate-y-0">
		<div class="w-16 h-16 rounded-2xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center mb-4 text-slate-400 dark:text-slate-500 border border-slate-150 dark:border-slate-700/50 shadow-inner">
			<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
			</svg>
		</div>
		<h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300">
			<?php echo trans('administrator::administrator.noresults') ?>
		</h3>
		<p class="text-xs text-slate-400 dark:text-slate-500 mt-1 max-w-[280px]">
			검색 결과 데이터가 비어 있습니다. 필터를 제거하거나 새로운 데이터를 생성해 보세요.
		</p>
	</div>
</div>

<!-- 사이드 상세 편집 패널 (Glassmorphism 슬라이드오버 컨테이너) -->
<div class="fixed inset-y-0 right-0 z-50 flex max-w-full pl-10 transition-all duration-500 ease-in-out transform"
     x-show="activeItem !== null || loadingItem"
     x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
     x-transition:enter-start="translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="translate-x-full"
     :style="{ width: expandWidth + 'px' }">
	
	<!-- 딤드 처리 오버레이 배경 -->
	<div class="absolute inset-0 bg-slate-900/30 dark:bg-slate-950/40 backdrop-blur-sm -left-full -right-10 pointer-events-none transition-opacity duration-500"
		 x-show="activeItem !== null || loadingItem"
		 x-transition:enter="ease-in-out duration-500"
		 x-transition:enter-start="opacity-0"
		 x-transition:enter-end="opacity-100"
		 x-transition:leave="ease-in-out duration-500"
		 x-transition:leave-start="opacity-100"
		 x-transition:leave-end="opacity-0"></div>

	<!-- 편집 컨테이너 카드 -->
	<div class="w-screen h-full bg-white dark:bg-slate-900 border-l border-slate-200 dark:border-slate-800 shadow-2xl flex flex-col"
		 :style="{ width: (expandWidth - 27) + 'px' }">
		
		<!-- 사이드 헤더 영역 -->
		<div class="px-6 py-4.5 border-b border-slate-200/60 dark:border-slate-800/80 flex items-center justify-between bg-slate-50/50 dark:bg-slate-900/50">
			<h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 flex items-center">
				<svg class="w-4.5 h-4.5 mr-2.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
				</svg>
				<span>항목 상세조회 및 편집</span>
			</h3>
			<button type="button" 
					@click="activeItem = null" 
					class="rounded-lg p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
				</svg>
			</button>
		</div>
		
		<!-- 편집용 Form 템플릿 마운트 -->
		<div class="flex-1 overflow-y-auto p-6" x-html="itemFormTemplate">
			<!-- 여기에 Alpine.js의 templates.edit 내용이 동적으로 삽입되어 편집 작업을 제어합니다. -->
		</div>
	</div>
</div>