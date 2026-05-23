<?php
/**
 * 관리자 필터 템플릿 (최초 오리지널 디자인 100% 보존 및 Alpine.js 이식)
 * 
 * 인위적인 테일윈드 클래스를 완전히 걷어내고 원래 최초의 오리지널 CSS와 격자 구조를 복구한 채
 * 데이터 바인딩 동작만 Alpine.js 지시어로 안전하게 포팅했습니다.
 */
?>
<h2><?php echo trans('administrator::administrator.filters') ?></h2>
<div class="filters">

	<template x-for="(filter, index) in filters" :key="filter.field_id">
		<div x-show="filter.visible" :class="filter.type + ' ' + (filter.min_max ? 'min_max' : '')">
			<label :for="filter.field_id" x-text="filter.title + ':'"></label>

			<template x-if="filter.description">
				<p class="description" x-text="filter.description"></p>
			</template>

			<!-- 1. key, text, text_quick, fulltext_mysql, color -->
			<template x-if="['key', 'text', 'text_quick', 'fulltext_mysql', 'color'].includes(filter.type)">
				<input type="text" x-model="filter.value" :id="filter.field_id" />
			</template>

			<!-- 2. number -->
			<template x-if="filter.type === 'number'">
				<div class="inline-block" style="display: inline-flex; align-items: center; gap: 4px;">
					<span class="symbol" x-text="filter.symbol"></span>
					<input type="text" x-model="filter.min_value" :id="filter.field_id + '_min'" style="width: 70px;" />
					<span>-</span>
					<input type="text" x-model="filter.max_value" :id="filter.field_id + '_max'" style="width: 70px;" />
				</div>
			</template>

			<!-- 3. bool -->
			<template x-if="filter.type === 'bool'">
				<select x-model="filter.value" :id="filter.field_id" style="width: 100%;"
						x-init="
							$nextTick(() => {
								jQuery($el).select2({minimumResultsForSearch: -1}).on('change', function() {
									filter.value = jQuery($el).val();
								});
							});
						">
					<option value="">-- 전체 --</option>
					<template x-for="opt in boolOptions" :key="opt.id">
						<option :value="opt.id" x-text="opt.text" :selected="opt.id == filter.value"></option>
					</template>
				</select>
			</template>

			<!-- 4. enum -->
			<template x-if="filter.type === 'enum'">
				<select x-model="filter.value" :id="filter.field_id" style="width: 100%;"
						x-init="
							$nextTick(() => {
								jQuery($el).select2().on('change', function() {
									filter.value = jQuery($el).val();
								});
							});
						">
					<option value="">-- 전체 --</option>
					<template x-for="opt in filter.options" :key="opt.id">
						<option :value="opt.id" x-text="opt.text || opt.name" :selected="opt.id == filter.value"></option>
					</template>
				</select>
			</template>

			<!-- 5. date, time, datetime (네이티브 피커 혹은 슬림 플랫) -->
			<template x-if="['date', 'time', 'datetime'].includes(filter.type)">
				<div class="inline-block" style="display: inline-flex; align-items: center; gap: 4px;">
					<input :type="filter.type === 'date' ? 'date' : (filter.type === 'time' ? 'time' : 'datetime-local')" 
						   x-model="filter.min_value" :id="filter.field_id + '_min'" style="width: 100px; padding: 2px;" />
					<span>-</span>
					<input :type="filter.type === 'date' ? 'date' : (filter.type === 'time' ? 'time' : 'datetime-local')" 
						   x-model="filter.max_value" :id="filter.field_id + '_max'" style="width: 100px; padding: 2px;" />
				</div>
			</template>

			<!-- 6. belongs_to, belongs_to_many -->
			<template x-if="['belongs_to', 'belongs_to_many'].includes(filter.type)">
				<div class="loader-container" style="position: relative; width: 100%;">
					<div class="loader" x-show="filter.loadingOptions" style="position: absolute; right: 5px; top: 5px;"></div>
					<!-- select2 연동을 안전하게 수행하는 hidden input -->
					<input type="hidden" :id="filter.field_id" :multiple="filter.type === 'belongs_to_many'"
						   x-init="
							 $nextTick(() => {
								let $el = jQuery('#' + filter.field_id);
								
								// 1. 값 변화 감시를 설정하여 Select2 UI에 동기화
								$watch('filter.value', (newVal) => {
									let currentVal = $el.val();
									let targetVal = Array.isArray(newVal) ? newVal.join(',') : (newVal || '');
									if (currentVal !== targetVal) {
										$el.val(targetVal).trigger('change.select2');
									}
								});

								// 2. 초기값 주입
								let initialVal = filter.value;
								if (Array.isArray(initialVal)) {
									initialVal = initialVal.join(',');
								}
								$el.val(initialVal || '');

								// 3. Select2 플러그인 마운트
								if (filter.autocomplete) {
									$el.select2Remote({
										field: filter.field_name,
										type: 'filter',
										multiple: filter.type === 'belongs_to_many' || filter.type === 'has_many',
										filterIndex: index
									}).on('change', function(e) {
										filter.value = $el.val();
									});
								} else {
									let resultsData = $root.listOptions[filter.field_name] || [];
									$el.select2({
										data: { results: resultsData },
										multiple: filter.type === 'belongs_to_many' || filter.type === 'has_many'
									}).on('change', function(e) {
										filter.value = $el.val();
									});
								}
								
								// 4. 초기 마운트 이후 변경 유발
								if (initialVal) {
									$el.trigger('change.select2');
								}
							 });
						   " />
				</div>
			</template>

		</div>
	</template>

</div>
