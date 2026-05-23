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

			<!-- 3. bool (Zero-jQuery 순수 Alpine.js 셀렉트) -->
			<template x-if="filter.type === 'bool'">
				<select x-model="filter.value" :id="filter.field_id" style="width: 100%; padding: 4px; border: 1px solid #ccc; border-radius: 3px; font-size: 12px; background-color: #fff;">
					<option value="">-- 전체 --</option>
					<template x-for="opt in boolOptions" :key="opt.id">
						<option :value="opt.id" x-text="opt.text" :selected="opt.id == filter.value"></option>
					</template>
				</select>
			</template>

			<!-- 4. enum (Zero-jQuery 순수 Alpine.js 셀렉트) -->
			<template x-if="filter.type === 'enum'">
				<select x-model="filter.value" :id="filter.field_id" style="width: 100%; padding: 4px; border: 1px solid #ccc; border-radius: 3px; font-size: 12px; background-color: #fff;">
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
			<template x-if="initialized && ['belongs_to', 'belongs_to_many'].includes(filter.type)">
				<div class="relative w-full"
					 x-data="relationSelect({ field: filter, type: 'filter', multiple: filter.type === 'belongs_to_many', autocomplete: filter.autocomplete, filterIndex: index })"
					 style="position: relative; width: 100%;">
					
					<div class="relation-combobox-wrapper" style="position: relative; width: 100%;">
						<!-- 1) 다중 선택 배지 목록 (belongs_to_many일 경우 노출) -->
						<template x-if="filter.type === 'belongs_to_many' && selectedItems.length > 0">
							<div class="selected-badges" style="display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 6px; width: 100%;">
								<template x-for="item in selectedItems" :key="item.id">
									<span class="badge-item" style="display: inline-flex; align-items: center; background: #e0e7ff; color: #3730a3; padding: 2px 8px; border-radius: 4px; font-size: 11px; gap: 4px;">
										<span x-text="item.text"></span>
										<button type="button" @click="removeItem(item)" style="border: none; background: none; cursor: pointer; color: #3730a3; font-weight: bold;">×</button>
									</span>
								</template>
							</div>
						</template>

						<!-- 2) 단일/다중 통합 콤보박스 입력 컨트롤러 (display: flex 제거로 아이콘 이탈 원천 차단) -->
						<div class="combobox-trigger-container" style="position: relative; width: 100%;">
							<input type="text" 
								   placeholder="-- 전체 --"
								   x-model="search"
								   @focus="open = true"
								   @click.away="setTimeout(() => open = false, 200)"
								   @input="if (filter.autocomplete) fetchAutocomplete()"
								   class="relation-combobox-input"
								   :value="filter.type !== 'belongs_to_many' && selectedItems[0] && !search ? selectedItems[0].text : search" />
							
							<!-- 로딩 및 트리거 화살표 (돋보기 포함 우측 정렬) -->
							<div class="icons" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); display: flex; align-items: center; gap: 6px; pointer-events: none;">
								<div class="spinner" x-show="loading" style="width: 12px; height: 12px; border: 2px solid #ccc; border-top-color: #6366f1; border-radius: 50%; animation: spin 0.6s linear infinite; pointer-events: auto;"></div>
								<!-- 미려한 돋보기(검색) SVG 아이콘 탑재 -->
								<svg xmlns="http://www.w3.org/2000/svg" 
									 class="search-icon" 
									 fill="none" 
									 viewBox="0 0 24 24" 
									 stroke="currentColor"
									 style="width: 13px; height: 13px; color: #9ca3af;">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
								</svg>
								<!-- 미려한 회전식 Chevron Down SVG 아이콘 탑재 -->
								<svg xmlns="http://www.w3.org/2000/svg" 
									 class="chevron-icon" 
									 :class="{ 'rotate-180': open }"
									 @click="open = !open" 
									 fill="none" 
									 viewBox="0 0 24 24" 
									 stroke="currentColor"
									 style="width: 13px; height: 13px; color: #9ca3af; cursor: pointer; transition: transform 0.2s ease; pointer-events: auto;">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
								</svg>
							</div>
						</div>

						<!-- 3) 모던 드롭다운 옵션 보드 -->
						<div class="combobox-dropdown" 
							 x-show="open" 
							 style="position: absolute; left: 0; right: 0; top: 100%; z-index: 50; background: white; border: 1px solid #e5e7eb; border-radius: 4px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); max-height: 180px; overflow-y: auto; margin-top: 6px; box-sizing: border-box; width: 100%;">
							
							<div class="options-list">
								<!-- 검색 결과 없음 -->
								<template x-if="filteredOptions.length === 0 && !loading">
									<div style="padding: 10px 14px; color: #9ca3af; font-size: 12px; text-align: center;">결과 없음</div>
								</template>
								
								<!-- '전체' 옵션 추가 (단일 선택 필터에만 표출) -->
								<template x-if="filter.type !== 'belongs_to_many'">
									<div @click="selectItem({ id: '', text: '-- 전체 --' })"
										 class="combobox-option-item"
										 style="color: #6b7280 !important; font-style: italic !important; border-bottom: 1px solid #f3f4f6 !important;"
										 x-text="'-- 전체 --'">
									</div>
								</template>
								
								<template x-for="opt in filteredOptions" :key="opt.id">
									<div @click="selectItem(opt)" 
										 class="combobox-option-item"
										 :class="{ 'selected-active': selectedItems.some(item => String(item.id) === String(opt.id)) }"
										 x-text="opt.text || opt.name">
									</div>
								</template>
							</div>
						</div>
					</div>
				</div>
			</template>

		</div>
	</template>

</div>
