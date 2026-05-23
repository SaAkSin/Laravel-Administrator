<?php
/**
 * 관리자 메인 테이블 템플릿 (최초 오리지널 디자인 100% 보존 및 Alpine.js 이식)
 * 
 * 인위적인 테일윈드 클래스를 완전히 걷어내고 원래 최초의 오리지널 CSS와 격자 구조를 복구한 채
 * 데이터 바인딩 동작만 Alpine.js 지시어로 안전하게 포팅했습니다.
 */
?>
<div class="table_container" :style="{ marginRight: (activeItem !== null || loadingItem) ? (expandWidth + 5) + 'px' : '290px' }">

	<div class="results_header">
		<h2 x-text="modelTitle"></h2>

		<div class="actions">
			<!-- 글로벌 커스텀 액션 루프 (Alpine.js 변환) -->
			<template x-if="globalActions && globalActions.length">
				<div class="inline-block">
					<template x-for="(action, idx) in globalActions" :key="action.action_name || idx">
						<template x-if="action.has_permission">
							<input type="button" 
								   @click="customAction(false, action.action_name, action.messages, action.confirmation)" 
								   :value="action.title"
								   :disabled="freezeForm || freezeActions" />
						</template>
					</template>
				</div>
			</template>

			<!-- 신규 추가 버튼 -->
			<template x-if="actionPermissions && actionPermissions.create">
				<a class="new_item"
				   :href="baseUrl + modelName + '/new'"
				   @click.prevent="addNewItem()"
				   x-text="'<?php echo trans('administrator::administrator.new') ?> ' + modelSingle"></a>
			</template>
		</div>

		<!-- 글로벌 메시지 피드백 영역 -->
		<div class="action_message" 
			 x-show="globalStatusMessage"
			 :class="{ error: globalStatusMessageType === 'error', success: globalStatusMessageType === 'success' }"
			 x-text="globalStatusMessage"></div>
	</div>

	<!-- 페이지네이션 컨트롤러 -->
	<div class="page_container">
		<div class="per_page">
			<select x-model="rowsPerPage"
					style="width: 60px; font-size: 11px; padding: 2px; border: 1px solid #ccc; border-radius: 3px; background-color: #fff;">
				<template x-for="option in rowsPerPageOptions" :key="option.id">
					<option :value="option.id" x-text="option.text" :selected="option.id == rowsPerPage"></option>
				</template>
			</select>
			<span> <?php echo trans('administrator::administrator.itemsperpage') ?></span>
		</div>
		<div class="paginator">
			<input type="button" value="<?php echo trans('administrator::administrator.previous') ?>"
				   :disabled="isFirstPage || !pagination.last || !initialized" 
				   @click="page('prev')" />
			<input type="button" value="<?php echo trans('administrator::administrator.next') ?>"
				   :disabled="isLastPage || !pagination.last || !initialized" 
				   @click="page('next')" />
			<input type="text" :disabled="pagination.last === 0 || !initialized" x-model.lazy="pagination.page" @change="page(pagination.page)" />
			<span x-text="' / ' + pagination.last"></span>
		</div>
	</div>

	<!-- 오리지널 격자 테이블 표출 -->
	<table class="results" border="0" cellspacing="0" id="customers" cellpadding="0">
		<thead>
			<tr>
				<template x-for="column in columns" :key="column.column_name">
					<th x-show="column.visible"
						@click="column.sortable && setSortOptions(column.sort_field ? column.sort_field : column.column_name)"
						:class="{
							sortable: column.sortable,
							'sorted-asc': (column.column_name == sortOptions.field || column.sort_field == sortOptions.field) && sortOptions.direction === 'asc',
							'sorted-desc': (column.column_name == sortOptions.field || column.sort_field == sortOptions.field) && sortOptions.direction === 'desc'
						}">
						<div x-text="column.title"></div>
					</th>
				</template>
			</tr>
		</thead>
		<tbody>
			<template x-for="(row, index) in rows" :key="(row[primaryKey] && row[primaryKey].raw) ? row[primaryKey].raw : index">
				<tr @click="if (row[primaryKey]) { clickItem(row[primaryKey].raw); } return true"
					:class="{
						result: true, 
						even: index % 2 === 1, 
						odd: index % 2 !== 1,
						selected: (row[primaryKey] && row[primaryKey].raw) == itemLoadingId
					}">
					<template x-for="column in columns" :key="column.column_name">
						<td x-show="column.visible" x-html="row[column.column_name] ? row[column.column_name].rendered : ''"></td>
					</template>
				</tr>
			</template>
		</tbody>
	</table>

	<!-- 로딩창 -->
	<div class="loading_rows" x-show="loadingRows">
		<div><?php echo trans('administrator::administrator.loading') ?></div>
	</div>

	<!-- 빈 상태 가이드 -->
	<div class="no_results" x-show="pagination.last === 0">
		<div><?php echo trans('administrator::administrator.noresults') ?></div>
	</div>
</div>

<!-- 상세 조작 및 서랍 컨테이너 (오리지널 레이아웃 100% 보존) -->
<div class="item_edit_container" 
	 x-show="initialized && (activeItem !== null || loadingItem)"
	 style="display: none;"
	 :class="{ active: activeItem !== null || loadingItem }"
	 :style="{ 
	 	width: expandWidth + 'px', 
	 	position: 'absolute',
	 	top: '35px',
	 	bottom: '0',
	 	right: '0',
	 	overflow: 'hidden',
	 	pointerEvents: (activeItem !== null || loadingItem) ? 'auto' : 'none'
	 }">
	<div class="item_edit" 
		 :style="{ 
		 	width: (expandWidth - 27) + 'px', 
		 	marginLeft: (activeItem !== null || loadingItem) ? '0' : expandWidth + 'px',
		 	transition: 'margin-left 0.25s cubic-bezier(0.4, 0, 0.2, 1)',
		 	boxShadow: 'none',
		 	borderLeft: '1px solid #cccccc',
		 	minHeight: '100%',
		 	marginBottom: '0',
		 	paddingLeft: '27px',
		 	paddingTop: '25px',
		 	position: 'relative'
		 }">
		<?php echo view("administrator::templates.edit", array('config' => $config))?>
	</div>
</div>