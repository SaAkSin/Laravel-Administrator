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
					
					<div class="relation-combobox-wrapper" :class="{ 'open': open }" @click.away="open = false" style="position: relative; width: 100%;">
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

						<!-- 2) 단일/다중 통합 프리미엄 콤보박스 컨테이너 (open 여부에 따라 높이 및 보더 확장) -->
						<div class="combobox-container" 
							 :class="{ 'open': open }"
							 style="position: relative; width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; background-color: #ffffff; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: border-color 0.15s ease, box-shadow 0.15s ease; box-sizing: border-box; display: flex; flex-direction: column; overflow: hidden;">
							
							<!-- 상단 값 노출/트리거 행 -->
							<div class="combobox-value-row" 
								 @click="open = !open"
								 style="height: 30px; display: flex; align-items: center; justify-content: space-between; padding: 0 0 0 10px; cursor: pointer; box-sizing: border-box; user-select: none;">
								
								<!-- 단일 선택일 때의 값 또는 플레이스홀더 표시 -->
								<!-- 단일 선택일 때 유효한 값이 존재할 때만 텍스트를 출력하고, 그렇지 않으면 비워둡니다. -->
								<span class="selected-text" 
									  x-text="(filter.type !== 'belongs_to_many' && selectedItems[0] && selectedItems[0].id !== '' && selectedItems[0].id !== 0 && selectedItems[0].id !== '0' && selectedItems[0].id !== 'null' && selectedItems[0].id !== 'undefined' && selectedItems[0].id !== null && selectedItems[0].id !== undefined && selectedItems[0].text && selectedItems[0].text !== '' && selectedItems[0].text !== 'null' && selectedItems[0].text !== 'undefined') ? selectedItems[0].text : ''" 
									  :style="{
										  fontSize: '12px',
										  color: (filter.type !== 'belongs_to_many' && selectedItems[0] && selectedItems[0].id !== '' && selectedItems[0].id !== 0 && selectedItems[0].id !== '0' && selectedItems[0].id !== 'null' && selectedItems[0].id !== 'undefined' && selectedItems[0].id !== null && selectedItems[0].id !== undefined && selectedItems[0].text && selectedItems[0].text !== '' && selectedItems[0].text !== 'null' && selectedItems[0].text !== 'undefined') ? '#1f2937' : '#9ca3af',
										  whiteSpace: 'nowrap',
										  overflow: 'hidden',
										  textOverflow: 'ellipsis',
										  maxWidth: 'calc(100% - 70px)'
									  }"></span>
								
								<!-- 우측 액션 영역 -->
								<div class="value-actions" style="display: flex; align-items: center; gap: 6px; margin-left: auto;">
									<!-- 값 초기화 x 버튼 (단일 선택이고 유효한 실제 ID가 존재할 때만 활성화) -->
									<span class="clear-btn" 
										  x-show="filter.type !== 'belongs_to_many' && selectedItems[0] && selectedItems[0].id !== '' && selectedItems[0].id !== 0 && selectedItems[0].id !== '0' && selectedItems[0].id !== 'null' && selectedItems[0].id !== 'undefined' && selectedItems[0].id !== null && selectedItems[0].id !== undefined && selectedItems[0].text && selectedItems[0].text !== '' && selectedItems[0].text !== 'null' && selectedItems[0].text !== 'undefined'" 
										  @click.stop="clearSelection()"
										  style="color: #9ca3af; font-size: 16px; font-weight: bold; cursor: pointer; line-height: 1; transition: color 0.1s ease; outline: none; display: inline-block;">×</span>
									
									<!-- 그라데이션 화살표 인디케이터 -->
									<span class="arrow-indicator" 
										  :class="{ 'button-style': !open }"
										  style="display: flex; align-items: center; justify-content: center; width: 20px; height: 20px;">
										<svg xmlns="http://www.w3.org/2000/svg" class="arrow-svg" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="#475569" stroke-width="2.5" style="width: 11px; height: 11px; transition: transform 0.2s ease; transform-origin: center;">
											<path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
										</svg>
									</span>
								</div>
							</div>
							
							<!-- 하단 검색 입력창 행 (open 일 때만 렌더링/노출) -->
							<div class="combobox-search-row" 
								 x-show="open" 
								 style="border-top: 1px solid #edf2f7; padding: 6px 10px; background-color: #f8fafc; box-sizing: border-box; display: flex; align-items: center; position: relative;">
								
								<input type="text" 
									   class="combobox-search-input" 
									   placeholder="검색어를 입력하세요..." 
									   x-model="search"
									   @input="if (filter.autocomplete) fetchAutocomplete()"
									   x-ref="searchInput"
									   style="width: 100% !important; height: 26px !important; border: 1px solid #cbd5e1 !important; border-radius: 4px !important; padding: 2px 28px 2px 8px !important; font-size: 12px !important; box-sizing: border-box !important; outline: none !important; background-color: #ffffff !important;" />
								
								<!-- 돋보기 아이콘 -->
								<svg xmlns="http://www.w3.org/2000/svg" 
									 class="search-icon-svg" 
									 fill="none" 
									 viewBox="0 0 24 24" 
									 stroke="#9ca3af" 
									 stroke-width="2.5" 
									 style="width: 13px; height: 13px; position: absolute; right: 18px; top: 50%; transform: translateY(-50%); pointer-events: none;">
									<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
								</svg>
							</div>
						</div>
						
						<!-- 3) 모던 드롭다운 옵션 보드 -->
						<div class="combobox-dropdown" 
							 x-show="open" 
							 style="position: absolute; left: 0; right: 0; top: 100%; z-index: 9999; border: 1px solid #cbd5e1; border-radius: 6px; background-color: #ffffff; box-shadow: 0 4px 6px rgba(0,0,0,0.08); max-height: 180px; overflow-y: auto; margin-top: 4px; box-sizing: border-box; padding: 4px 0; width: 100%;">
							
							<div class="options-list">
								<!-- 자동완성 최소 글자 수 가이드 안내 (예: Please enter 1 more character) -->
								<template x-if="filter.autocomplete && search.length < 1">
									<div style="padding: 8px 12px; color: #4b5563; font-size: 12px; background-color: #f8fafc;">Please enter 1 more character</div>
								</template>
								
								<!-- 검색 결과 없음 -->
								<template x-if="!(filter.autocomplete && search.length < 1) && filteredOptions.length === 0 && !loading">
									<div style="padding: 10px 14px; color: #9ca3af; font-size: 12px; text-align: center;">결과 없음</div>
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
