<?php
/**
 * 환경 설정 편집 폼 템플릿 (최초 오리지널 디자인 100% 보존 및 Alpine.js 이식)
 * 
 * 테일윈드 클래스를 완전히 걷어내고 패키지 최초의 견고한 연그레이 오리지널 폼 클래스들을 복구한 채
 * 데이터 바인딩 동작만 Alpine.js 지시어로 안전하게 포팅했습니다.
 */
?>
<form class="settings_form" @submit.prevent="save">
	<h2 x-text="$root.settingsTitle"></h2>

	<!-- 오리지널 설정 폼 필드 루프 -->
	<template x-for="(field, index) in editFields" :key="field.field_id || index">
		<div x-show="field && field.editable && field.visible" :class="field.type">
			<label :for="field.field_id" x-text="field.title + ':'"></label>

			<!-- 1. TEXT TYPE -->
			<template x-if="field.type === 'text'">
				<div>
					<template x-if="field.editable">
						<template x-if="field.limit">
							<div class="characters_left" x-text="(field.limit - ($root[field.field_name] ? $root[field.field_name].length : 0)) + '자 남음'"></div>
						</template>
						<input type="text" :id="field.field_id" :disabled="freezeForm" x-model="$root[field.field_name]" :maxlength="field.limit || null" />
					</template>
					<template x-if="!field.editable">
						<div class="uneditable" x-text="$root[field.field_name] || ''"></div>
					</template>
				</div>
			</template>

			<!-- 2. TEXTAREA TYPE -->
			<template x-if="field.type === 'textarea'">
				<div>
					<template x-if="field.editable">
						<template x-if="field.limit">
							<div class="characters_left" x-text="(field.limit - ($root[field.field_name] ? $root[field.field_name].length : 0)) + '자 남음'"></div>
						</template>
						<textarea :id="field.field_id" :disabled="freezeForm" x-model="$root[field.field_name]" :maxlength="field.limit || null" :style="{ height: (field.height || 80) + 'px' }"></textarea>
					</template>
					<template x-if="!field.editable">
						<div class="uneditable" x-text="$root[field.field_name] || ''"></div>
					</template>
				</div>
			</template>

			<!-- 3. WYSIWYG TYPE (CKEditor 4) -->
			<template x-if="field.type === 'wysiwyg'">
				<div x-init="$nextTick(() => $root.initEditor($el.querySelector('textarea'), field.field_name, 'wysiwyg'))"
					 class="ckeditor-wrapper"
					 style="margin-top: 6px;">
					<textarea :id="field.field_id" :disabled="freezeForm"></textarea>
				</div>
			</template>

			<!-- 3-2. WYSIWYG2 TYPE (Quill Editor) -->
			<template x-if="field.type === 'wysiwyg2'">
				<div x-init="$nextTick(() => $root.initEditor($el.querySelector('.quill-editor-container'), field.field_name, 'wysiwyg2'))"
					 class="quill-wrapper"
					 style="margin-top: 6px;">
					<div class="quill-editor-container" :id="field.field_id" style="min-height: 150px; background: #fff; border: 1px solid #ccc; border-radius: 4px; color: #333;"></div>
				</div>
			</template>

			<!-- 4. MARKDOWN TYPE -->
			<template x-if="field.type === 'markdown'">
				<div>
					<template x-if="field.editable">
						<div class="markdown_container" :style="{ height: (field.height || 180) + 'px' }">
							<template x-if="field.limit">
								<div class="characters_left" x-text="(field.limit - ($root[field.field_name] ? $root[field.field_name].length : 0)) + '자 남음'"></div>
							</template>
							<textarea :id="field.field_id" :disabled="freezeForm" x-model="$root[field.field_name]" :maxlength="field.limit || null"></textarea>
							<div class="preview prose dark:prose-invert prose-sm" x-html="window.markdown ? markdown.toHTML($root[field.field_name] || '') : ($root[field.field_name] || '')"></div>
						</div>
					</template>
					<template x-if="!field.editable">
						<div class="uneditable prose dark:prose-invert prose-sm" x-html="window.markdown ? markdown.toHTML($root[field.field_name] || '') : ($root[field.field_name] || '')"></div>
					</template>
				</div>
			</template>

			<!-- 5. PASSWORD TYPE -->
			<template x-if="field.type === 'password'">
				<div>
					<template x-if="field.editable">
						<template x-if="field.limit">
							<div class="characters_left" x-text="(field.limit - ($root[field.field_name] ? $root[field.field_name].length : 0)) + '자 남음'"></div>
						</template>
						<input type="password" :id="field.field_id" :disabled="freezeForm" x-model="$root[field.field_name]" :maxlength="field.limit || null" />
					</template>
					<template x-if="!field.editable">
						<div class="uneditable" x-text="'********'"></div>
					</template>
				</div>
			</template>

			<!-- 6. NUMBER TYPE (Alpine.js & accounting.js 연계형 양방향 포맷터 컴포넌트) -->
			<template x-if="field.type === 'number'">
				<div x-data="{
					displayValue: '',
					init() {
						this.updateDisplay();
						this.$watch('$root.' + field.field_name, (newVal) => {
							this.updateDisplay();
						});
					},
					updateDisplay() {
						let raw = $root[field.field_name];
						if (raw === null || raw === undefined || raw === '') {
							this.displayValue = '';
							return;
						}
						if (window.accounting) {
							this.displayValue = accounting.formatMoney(
								raw, 
								field.symbol || '', 
								field.decimals !== undefined ? parseInt(field.decimals) : 0, 
								field.thousands_separator !== undefined ? field.thousands_separator : ',', 
								field.decimal_separator !== undefined ? field.decimal_separator : '.'
							);
						} else {
							this.displayValue = raw;
						}
					},
					onFocus() {
						let raw = $root[field.field_name];
						this.displayValue = (raw === null || raw === undefined) ? '' : String(raw);
					},
					onBlur() {
						let val = this.displayValue.trim();
						if (val === '') {
							$root[field.field_name] = null;
							this.displayValue = '';
							return;
						}
						let thousandSep = field.thousands_separator || ',';
						let decimalSep = field.decimal_separator || '.';
						
						let parsed = val.split(thousandSep).join('');
						if (decimalSep !== '.') {
							parsed = parsed.split(decimalSep).join('.');
						}
						let floatVal = parseFloat(parsed);
						if (isNaN(floatVal)) {
							$root[field.field_name] = null;
							this.displayValue = '';
						} else {
							$root[field.field_name] = floatVal;
							this.updateDisplay();
						}
					}
				}" x-init="init()" style="width: 100%;">
					<template x-if="field.editable">
						<div style="display: inline-flex; align-items: center; gap: 6px;">
							<input type="text" :id="field.field_id" :disabled="freezeForm" 
								   x-model="displayValue" 
								   @focus="onFocus()" 
								   @blur="onBlur()" 
								   style="padding: 3px;" />
						</div>
					</template>
					<template x-if="!field.editable">
						<div class="uneditable" x-text="displayValue || '-'"></div>
					</template>
				</div>
			</template>

			<!-- 7. BOOL TYPE -->
			<template x-if="field.type === 'bool'">
				<div>
					<template x-if="field.editable">
						<input type="checkbox" :id="field.field_id" :disabled="freezeForm" x-model="$root[field.field_name]" />
					</template>
					<template x-if="!field.editable">
						<span x-text="parseInt($root[field.field_name]) ? 'yes' : 'no'"></span>
					</template>
				</div>
			</template>

			<!-- 8. ENUM TYPE -->
			<template x-if="field.type === 'enum'">
				<div>
					<template x-if="field.editable">
						<div class="relative w-full"
							 x-data="relationSelect({ field: field, type: 'edit', multiple: false, autocomplete: false })"
							 style="position: relative; width: 100%; box-sizing: border-box;">
							
							<div class="relation-combobox-wrapper" :class="{ 'open': open }" @click.away="open = false" style="position: relative; width: 100%;">
								
								<!-- 단일/다중 통합 프리미엄 콤보박스 컨테이너 -->
								<div class="combobox-container" 
									 :class="{ 'open': open }"
									 style="position: relative; width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; background-color: #ffffff; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: border-color 0.15s ease, box-shadow 0.15s ease; box-sizing: border-box; display: flex; flex-direction: column; overflow: hidden;">
									
									<!-- 상단 값 노출/트리거 행 -->
									<div class="combobox-value-row" 
										 @click="open = !open"
										 style="height: 30px; display: flex; align-items: center; justify-content: space-between; padding: 0 0 0 10px; cursor: pointer; box-sizing: border-box; user-select: none;">
										
										<!-- 선택된 값 또는 플레이스홀더 표시 (선택되지 않았을 때는 플레이스홀더 출력) -->
										<span class="selected-text" 
											  x-text="(selectedItems.length > 0 && selectedItems[0].id !== '' && selectedItems[0].id != null) ? selectedItems[0].text : '-- Select Some Options --'" 
											  :style="{
												  fontSize: '12px',
												  color: (selectedItems.length > 0 && selectedItems[0].id !== '' && selectedItems[0].id != null) ? '#1f2937' : '#9ca3af',
												  whiteSpace: 'nowrap',
												  overflow: 'hidden',
												  textOverflow: 'ellipsis',
												  maxWidth: 'calc(100% - 70px)'
											  }"></span>
										
										<!-- 우측 액션 영역 -->
										<div class="value-actions" style="display: flex; align-items: center; gap: 6px; margin-left: auto;">
											<!-- 값 초기화 x 버튼 -->
											<span class="clear-btn" 
												  x-show="selectedItems.length > 0 && selectedItems[0].id !== '' && selectedItems[0].id != null" 
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
											   @keydown.up.prevent="moveFocus(-1)"
											   @keydown.down.prevent="moveFocus(1)"
											   @keydown.enter.prevent="selectFocused()"
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
								
								<!-- 모던 드롭다운 옵션 보드 -->
								<div class="combobox-dropdown" 
									 x-show="open" 
									 style="position: absolute; left: 0; right: 0; top: 100%; z-index: 9999; border: 1px solid #cbd5e1; border-radius: 6px; background-color: #ffffff; box-shadow: 0 4px 6px rgba(0,0,0,0.08); max-height: 180px; overflow-y: auto; margin-top: 4px; box-sizing: border-box; padding: 4px 0; width: 100%;">
									
									<div class="options-list" x-ref="optionsList">
										<!-- 검색 결과 없음 -->
										<template x-if="filteredOptions.length === 0">
											<div style="padding: 10px 14px; color: #9ca3af; font-size: 12px; text-align: center;">결과 없음</div>
										</template>
										
										<template x-for="(opt, index) in filteredOptions" :key="opt.id">
											<div @click="selectItem(opt)" 
												 class="combobox-option-item"
												 :class="{ 'selected-active': selectedItems.some(item => String(item.id) === String(opt.id)), 'focus-active': index === focusedIndex }"
												 x-html="highlight(opt.text || opt.name)"
												 :style="index === focusedIndex ? 'background-color: #f1f5f9; color: #1e293b;' : ''">
											</div>
										</template>
									</div>
								</div>
							</div>
						</div>
					</template>
					<template x-if="!field.editable">
						<div class="uneditable" x-text="(field.options.find(x => String(x.id) === String($root[field.field_name])) || {}).text || $root[field.field_name] || '-'"></div>
					</template>
				</div>
			</template>

			<!-- 9. DATE, TIME, DATETIME -->
			<template x-if="['date', 'time', 'datetime'].includes(field.type)">
				<div>
					<template x-if="field.editable">
						<input :type="field.type === 'date' ? 'date' : (field.type === 'time' ? 'time' : 'datetime-local')" 
							   :id="field.field_id" :disabled="freezeForm" x-model="$root[field.field_name]" 
							   :step="['time', 'datetime'].includes(field.type) ? '60' : null"
							   style="padding: 3px;" />
					</template>
					<template x-if="!field.editable">
						<div class="uneditable" x-text="$root[field.field_name] || '-'"></div>
					</template>
				</div>
			</template>

			<!-- 10. IMAGE TYPE -->
			<template x-if="field.type === 'image'">
				<div>
					<template x-if="field.editable">
						<div class="upload_container" :id="field.field_id">
							<input type="file" :id="field.field_name + '_uploader'" 
								   @change="uploadFile($event, field)" :disabled="freezeForm" style="display: none;" />
							<button type="button" @click="document.getElementById(field.field_name + '_uploader').click()"
									:disabled="freezeForm" style="padding: 4px 10px; font-size: 11px; display: inline-flex !important; align-items: center; justify-content: center; gap: 4px; line-height: 1 !important; white-space: nowrap !important;">
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 12px; height: 12px; flex-shrink: 0; display: block;">
									<path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
								</svg>
								<span style="display: inline-block; line-height: 1; vertical-align: middle; white-space: nowrap !important;"><?php echo trans('administrator::administrator.uploadimage') ?></span>
							</button>
							<div class="uploading" x-show="field.uploading"
								 x-text="'<?php echo trans('administrator::administrator.imageuploading') ?>' + field.upload_percentage + '%'"></div>
						</div>
					</template>
					<template x-if="$root[field.field_name]">
						<div class="image_container" style="margin-top: 8px;">
							<img :src="file_url + '?path=' + field.location + $root[field.field_name]" style="max-width: 370px; height: auto; border: 1px solid #ccc; padding: 2px;" />
							<template x-if="field.editable">
								<input type="button" class="remove_button" @click="$root[field.field_name] = null" value="x" style="margin-left: 5px; vertical-align: top;" />
							</template>
						</div>
					</template>
					<template x-if="!$root[field.field_name] && !field.editable">
						<div class="uneditable"><?php echo trans('administrator::administrator.no_image_uploaded') ?></div>
					</template>
				</div>
			</template>

			<!-- 11. FILE TYPE -->
			<template x-if="field.type === 'file'">
				<div>
					<template x-if="field.editable">
						<div class="upload_container" :id="field.field_id">
							<input type="file" :id="field.field_name + '_uploader'" 
								   @change="uploadFile($event, field)" :disabled="freezeForm" style="display: none;" />
							<button type="button" @click="document.getElementById(field.field_name + '_uploader').click()"
									:disabled="freezeForm" style="padding: 4px 10px; font-size: 11px; display: inline-flex !important; align-items: center; justify-content: center; gap: 4px; line-height: 1 !important; white-space: nowrap !important;">
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 12px; height: 12px; flex-shrink: 0; display: block;">
									<path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
								</svg>
								<span style="display: inline-block; line-height: 1; vertical-align: middle; white-space: nowrap !important;"><?php echo trans('administrator::administrator.uploadfile') ?></span>
							</button>
							<div class="uploading" x-show="field.uploading"
								 x-text="'<?php echo trans('administrator::administrator.fileuploading') ?>' + field.upload_percentage + '%'"></div>
						</div>
					</template>
					<template x-if="$root[field.field_name]">
						<div class="file_container" style="margin-top: 8px;">
							<a :href="file_url + '?path=' + field.location + $root[field.field_name]" :title="$root[field.field_name]" x-text="$root[field.field_name]"></a>
							<template x-if="field.editable">
								<input type="button" class="remove_button" @click="$root[field.field_name] = null" value="x" style="margin-left: 5px;" />
							</template>
						</div>
					</template>
					<template x-if="!$root[field.field_name] && !field.editable">
						<div class="uneditable"><?php echo trans('administrator::administrator.no_file_uploaded') ?></div>
					</template>
				</div>
			</template>

			<!-- 12. COLOR TYPE -->
			<template x-if="field.type === 'color'">
				<div>
					<template x-if="field.editable">
						<input type="text" x-model="$root[field.field_name]" :id="field.field_id" />
					</template>
					<template x-if="!field.editable">
						<div class="uneditable" x-text="$root[field.field_name] || ''"></div>
					</template>
					<div class="color_preview" :style="{ backgroundColor: $root[field.field_name] }" x-show="$root[field.field_name]"></div>
				</div>
			</template>
		</div>
	</template>

	<!-- 제어 버튼 그룹 -->
	<div class="control_buttons">
		<input type="submit" value="<?php echo trans('administrator::administrator.save') ?>"
			:disabled="freezeForm || freezeActions" />

		<!-- 커스텀 액션 버튼 루프 -->
		<template x-if="actions && actions.length">
			<template x-for="(action, idx) in actions" :key="action.action_name || idx">
				<template x-if="action.has_permission">
					<input type="button" @click="customAction(false, action.action_name, action.messages, action.confirmation)" :value="action.title"
						   :disabled="freezeForm || freezeActions" />
				</template>
			</template>
		</template>

		<!-- 상태 메시지 노티피케이션 -->
		<span class="message" 
			  x-show="statusMessage"
			  :class="{ error: statusMessageType === 'error', success: statusMessageType === 'success' }"
			  x-text="statusMessage"></span>
	</div>
</form>
