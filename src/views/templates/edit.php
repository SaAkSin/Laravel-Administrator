<?php
/**
 * 관리자 상세 편집 폼 템플릿 (최초 오리지널 디자인 100% 보존 및 Alpine.js 이식)
 * 
 * 테일윈드 클래스를 완전히 걷어내고 패키지 최초의 견고한 연그레이 오리지널 폼 클래스들을 복구한 채
 * 데이터 바인딩 동작만 Alpine.js 지시어로 안전하게 포팅했습니다.
 */
?>
<div x-show="loadingItem" class="loading_rows">
	<div><?php echo trans('administrator::administrator.loading') ?></div>
</div>

<form class="edit_form" x-show="!loadingItem" @submit.prevent="saveItem" style="display: none;">
	<h2 x-text="$root[$root.primaryKey] ? '<?php echo trans('administrator::administrator.edit') ?>' : '<?php echo trans('administrator::administrator.createnew') ?>'"></h2>

	<!-- 외부 아이템 다이렉트 링크 (최초 디자인 복원) -->
	<template x-if="$root[$root.primaryKey] && itemLink">
		<a class="item_link" target="_blank" :href="itemLink"
		   x-text="'<?php echo trans('administrator::administrator.viewitem', array('single' => $config->getOption('single'))) ?>'"></a>
	</template>

	<!-- 상단 제어 및 액션 버튼 세트 (is_top_actions 활성화 시) -->
	<?php if($config->checkOption('is_top_actions')) { ?>
		<!-- 커스텀 상단 버튼 그룹 -->
		<template x-if="$root[$root.primaryKey] && actions && actions.length">
			<div class="custom_buttons" style="margin-top: 0; margin-bottom: 10px;">
				<template x-for="(action, idx) in actions" :key="action.action_name || idx">
					<template x-if="action.has_permission && $root.actionPermissions[action.action_name] !== false">
						<input type="button" @click="customAction(true, action.action_name, action.messages, action.confirmation)"
							   :value="action.title" :disabled="freezeForm || freezeActions" />
					</template>
				</template>
			</div>
		</template>

		<!-- 제어 및 액션 버튼 세트 -->
		<div class="control_buttons" style="margin-top: 0; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #e0e0e0;">
			<template x-if="$root[$root.primaryKey]">
				<input type="button" value="<?php echo trans('administrator::administrator.close') ?>"
					   @click="closeItem()" :disabled="freezeForm || freezeActions" />
			</template>

			<template x-if="$root[$root.primaryKey] && actionPermissions.delete">
				<input type="button" value="<?php echo trans('administrator::administrator.delete') ?>"
					   @click="deleteItem()" :disabled="freezeForm || freezeActions" class="remove_button" />
			</template>

			<template x-if="$root[$root.primaryKey] && actionPermissions.update">
				<input type="submit" value="<?php echo trans('administrator::administrator.save') ?>"
					   :disabled="freezeForm || freezeActions" />
			</template>

			<template x-if="!$root[$root.primaryKey]">
				<input type="button" value="<?php echo trans('administrator::administrator.cancel') ?>"
					   @click="closeItem()" :disabled="freezeForm || freezeActions" />
			</template>

			<template x-if="!$root[$root.primaryKey] && actionPermissions.create">
				<input type="submit" value="<?php echo trans('administrator::administrator.create') ?>"
					   :disabled="freezeForm || freezeActions" />
			</template>
		</div>
	<?php } ?>

	<!-- 오리지널 편집 폼 필드 루프 -->
	<template x-for="(field, index) in editFields" :key="field.field_id || index">
		<div x-show="field && ($root[$root.primaryKey] || field.editable) && field.visible" 
			 :class="field.type + ' ' + (field.min_max ? 'min_max' : '')">
			 
			<label :for="field.field_id">
				<span x-text="field.title"></span>
				<span class="required" x-show="field.required">*</span>:
			</label>

			<!-- 필드 디스크립션 -->
			<template x-if="field.description && field.type !== 'bool' && field.type !== 'key'">
				<p class="description" x-text="field.description"></p>
			</template>

			<!-- 1. KEY TYPE -->
			<template x-if="field.type === 'key'">
				<div class="uneditable" x-text="$root[$root.primaryKey]"></div>
			</template>

			<!-- 2. TEXT TYPE -->
			<template x-if="field.type === 'text'">
				<div x-data="{
					get charCount() {
						return ($root[field.field_name] || '').length;
					},
					get charsLeft() {
						return (field.limit || 0) - this.charCount;
					}
				}">
					<template x-if="field.editable && field.limit">
						<div class="characters_left" x-text="charsLeft + ' characters left'"></div>
					</template>
					<template x-if="field.editable">
						<input type="text" :id="field.field_id" :disabled="freezeForm" x-model="$root[field.field_name]" :maxlength="field.limit || null" />
					</template>
					<template x-if="!field.editable">
						<div class="uneditable" x-text="$root[field.field_name]"></div>
					</template>
				</div>
			</template>

			<!-- 3. TEXTAREA TYPE -->
			<template x-if="field.type === 'textarea'">
				<div>
					<template x-if="field.editable">
						<textarea :id="field.field_id" :disabled="freezeForm" x-model="$root[field.field_name]" :maxlength="field.limit || null" :style="{ height: (field.height || 80) + 'px' }"></textarea>
					</template>
					<template x-if="!field.editable">
						<div class="uneditable" x-text="$root[field.field_name]" style="white-space: pre-wrap;"></div>
					</template>
				</div>
			</template>

			<!-- 4. WYSIWYG TYPE (CKEditor 4) -->
			<template x-if="field.type === 'wysiwyg'">
				<div x-init="$nextTick(() => $root.initEditor($el.querySelector('textarea'), field.field_name, 'wysiwyg'))"
					 class="ckeditor-wrapper"
					 style="margin-top: 6px;">
					<textarea :id="field.field_id" :disabled="freezeForm"></textarea>
				</div>
			</template>

			<!-- 4-2. WYSIWYG2 TYPE (Quill Editor) -->
			<template x-if="field.type === 'wysiwyg2'">
				<div x-init="$nextTick(() => $root.initEditor($el.querySelector('.quill-editor-container'), field.field_name, 'wysiwyg2'))"
					 class="quill-wrapper"
					 style="margin-top: 6px;">
					<div class="quill-editor-container" :id="field.field_id" style="min-height: 150px; background: #fff; color: #333;"></div>
				</div>
			</template>

			<!-- 5. MARKDOWN TYPE -->
			<template x-if="field.type === 'markdown'">
				<div>
					<template x-if="field.editable">
						<div class="markdown_container">
							<textarea :id="field.field_id" :disabled="freezeForm" x-model="$root[field.field_name]" :style="{ height: (field.height || 180) + 'px' }"></textarea>
							<div class="preview prose dark:prose-invert prose-sm" x-html="window.markdown ? markdown.toHTML($root[field.field_name] || '') : ($root[field.field_name] || '')"></div>
						</div>
					</template>
					<template x-if="!field.editable">
						<div class="uneditable prose dark:prose-invert prose-sm" x-html="window.markdown ? markdown.toHTML($root[field.field_name] || '') : ($root[field.field_name] || '')"></div>
					</template>
				</div>
			</template>

			<!-- 6. PASSWORD TYPE -->
			<template x-if="field.type === 'password'">
				<div>
					<template x-if="field.editable">
						<input type="password" :id="field.field_id" :disabled="freezeForm" x-model="$root[field.field_name]" />
					</template>
					<template x-if="!field.editable">
						<div class="uneditable" x-text="'********'"></div>
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
			<template x-if="initialized && field.type === 'enum'">
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
										
										<!-- 선택된 값 또는 플레이스홀더 표시 (선택되지 않았을 때는 빈 값으로 출력) -->
										<span class="selected-text" 
											  x-text="(selectedItems.length > 0 && selectedItems[0].id !== '' && selectedItems[0].id != null) ? selectedItems[0].text : ''" 
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
												 x-text="opt.text || opt.name"
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
							<button type="button" @click="$event.target.parentElement.querySelector('input[type=file]').click()"
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
					<template x-if="$root[field.field_name] && !loadingItem">
						<div class="image_container" style="position: relative; display: block; width: 100%; margin-top: 8px; box-sizing: border-box;">
							<template x-if="field.display_raw_value">
								<img :src="$root[field.field_name]" style="max-width: 370px; height: auto; border: 1px solid #ccc; padding: 2px; display: inline-block; vertical-align: top;" />
							</template>
							<template x-if="!field.display_raw_value">
								<img :src="$root.file_url + '?path=' + field.location + $root[field.field_name]" style="max-width: 370px; height: auto; border: 1px solid #ccc; padding: 2px; display: inline-block; vertical-align: top;" />
							</template>
							<template x-if="field.editable">
								<button type="button" class="remove_button" @click="$root[field.field_name] = null" style="position: absolute; top: 0; right: 0; padding: 0 !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; border-radius: 4px !important; width: 24px; height: 24px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); z-index: 10; line-height: 1 !important;" title="삭제">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 13px; height: 13px; display: block; flex-shrink: 0;">
										<path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
									</svg>
								</button>
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
							<button type="button" @click="$event.target.parentElement.querySelector('input[type=file]').click()"
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
							<template x-if="field.display_raw_value">
								<a :href="$root[field.field_name]" :title="$root[field.field_name]" x-text="$root[field.field_name]"></a>
							</template>
							<template x-if="!field.display_raw_value">
								<a :href="$root.file_url + '?path=' + field.location + $root[field.field_name]" :title="$root[field.field_name]" x-text="$root[field.field_name]"></a>
							</template>
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
						<div style="display: inline-flex; align-items: center; gap: 6px;">
							<input type="text" x-model="$root[field.field_name]" :id="field.field_id" style="width: 80px !important; display: inline-block;" />
							<input type="color" x-model="$root[field.field_name]" style="width: 30px; height: 26px; border: 1px solid #ccc; border-radius: 4px; padding: 0; cursor: pointer; background: none;" />
						</div>
					</template>
					<template x-if="!field.editable">
						<div style="display: inline-flex; align-items: center; gap: 6px;">
							<div class="uneditable" x-text="$root[field.field_name] || '-'"></div>
							<div class="color_preview" :style="{ backgroundColor: $root[field.field_name] }" x-show="$root[field.field_name]" style="width: 24px; height: 24px; border: 1px solid #ccc; border-radius: 4px;"></div>
						</div>
					</template>
				</div>
			</template>

			<!-- 12-2. NUMBER TYPE (Alpine.js & accounting.js 연계형 양방향 포맷터 컴포넌트) -->
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
							// 중복 표시를 방지하기 위해 입력 값 포맷팅 시 기호(symbol) 자리를 비워둡니다.
							this.displayValue = accounting.formatMoney(
								raw, 
								'', 
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
						<div style="position: relative; display: flex; align-items: center; width: 100%;">
							<template x-if="field.symbol">
								<span class="symbol" style="position: absolute; left: -15px; top: 50%; transform: translateY(-50%); pointer-events: none; z-index: 10;" x-text="field.symbol"></span>
							</template>
							<input type="text" :id="field.field_id" :disabled="freezeForm" 
								   x-model="displayValue" 
								   @focus="onFocus()" 
								   @blur="onBlur()" 
								   style="padding: 3px; flex: 1; width: 100%;" />
						</div>
					</template>
					<template x-if="!field.editable">
						<div style="position: relative; display: flex; align-items: center; width: 100%;">
							<template x-if="field.symbol">
								<span class="symbol" style="position: absolute; left: -15px; top: 50%; transform: translateY(-50%); pointer-events: none; z-index: 10;" x-text="field.symbol"></span>
							</template>
							<div class="uneditable" x-text="displayValue || '-'" style="flex: 1; width: 100%; margin-right: 0;"></div>
						</div>
					</template>
				</div>
			</template>

			<!-- 13. RELATIONSHIP (belongs_to, belongs_to_many, has_many, relationship) -->
			<template x-if="initialized && ['belongs_to', 'belongs_to_many', 'has_many', 'relationship'].includes(field.type)">
				<div class="relative w-full"
					 x-data="relationSelect({ field: field, type: 'edit', multiple: ['belongs_to_many', 'has_many'].includes(field.type), autocomplete: field.autocomplete })"
					 style="position: relative; width: 100%;">
					
					<template x-if="field.editable">
						<div class="relation-combobox-wrapper" :class="{ 'open': open }" @click.away="open = false" style="position: relative; width: 100%;">
							<!-- 1) 다중 선택 배지 목록 (belongs_to_many, has_many일 경우 노출) -->
							<template x-if="['belongs_to_many', 'has_many'].includes(field.type) && selectedItems.length > 0">
								<div class="selected-badges" style="display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 6px;">
									<template x-for="item in selectedItems" :key="item.id">
										<span class="badge-item">
											<span x-text="item.text"></span>
											<button type="button" @click="removeItem(item)">×</button>
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
									<!-- 단일 선택일 때 유효한 값이 존재할 때만 텍스트를 출력하고, 그렇지 않으면 플레이스홀더를 출력합니다. -->
									<span class="selected-text" 
										  x-text="(!['belongs_to_many', 'has_many'].includes(field.type) && selectedItems.length > 0 && selectedItems[0].id !== '' && selectedItems[0].id != null) ? selectedItems[0].text : '-- 검색 또는 선택 --'" 
										  :style="{
											  fontSize: '12px',
											  color: (!['belongs_to_many', 'has_many'].includes(field.type) && selectedItems.length > 0 && selectedItems[0].id !== '' && selectedItems[0].id != null) ? '#1f2937' : '#9ca3af',
											  whiteSpace: 'nowrap',
											  overflow: 'hidden',
											  textOverflow: 'ellipsis',
											  maxWidth: 'calc(100% - 70px)'
										  }"></span>
									
									<!-- 우측 액션 영역 -->
									<div class="value-actions" style="display: flex; align-items: center; gap: 6px; margin-left: auto;">
										<!-- 값 초기화 x 버튼 (단일 선택이고 유효한 실제 ID가 존재할 때만 활성화) -->
										<span class="clear-btn" 
											  x-show="!['belongs_to_many', 'has_many'].includes(field.type) && selectedItems.length > 0 && selectedItems[0].id !== '' && selectedItems[0].id != null" 
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
							
							<!-- 3) 모던 드롭다운 옵션 보드 -->
							<div class="combobox-dropdown" 
								 x-show="open" 
								 style="position: absolute; left: 0; right: 0; top: 100%; z-index: 9999; border: 1px solid #cbd5e1; border-radius: 6px; background-color: #ffffff; box-shadow: 0 4px 6px rgba(0,0,0,0.08); max-height: 200px; overflow-y: auto; margin-top: 4px; box-sizing: border-box; padding: 4px 0;">
								
								<div class="options-list" x-ref="optionsList">
									<!-- 자동완성 최소 글자 수 가이드 안내 (예: Please enter 1 more character) -->
									<template x-if="field.autocomplete && search.length < 1">
										<div style="padding: 8px 12px; color: #4b5563; font-size: 12px; background-color: #f8fafc;">Please enter 1 more character</div>
									</template>
									
									<!-- 검색 결과 없음 표출 -->
									<template x-if="!(field.autocomplete && search.length < 1) && filteredOptions.length === 0 && !loading">
										<div style="padding: 10px 14px; color: #9ca3af; font-size: 12px; text-align: center;">검색 결과가 없습니다.</div>
									</template>
									
									<template x-for="(opt, index) in filteredOptions" :key="opt.id">
										<div @click="selectItem(opt)" 
											 class="combobox-option-item"
											 :class="{ 'selected-active': selectedItems.some(item => String(item.id) === String(opt.id)), 'focus-active': index === focusedIndex }"
											 x-text="opt.text || opt.name"
											 :style="index === focusedIndex ? 'background-color: #f1f5f9; color: #1e293b;' : ''">
										</div>
									</template>
								</div>
							</div>
						</div>
					</template>

					<template x-if="!field.editable">
						<div class="uneditable" x-text="selectedItems.map(item => item.text).join(', ') || '-'"></div>
					</template>
				</div>
			</template>

			<!-- 설명 추가형 필드 (bool, key 타입 아래 추가 묘사) -->
			<template x-if="field.description && (field.type === 'bool' || field.type === 'key')">
				<p class="description_below" x-text="field.description"></p>
			</template>
		</div>
	</template>

	<!-- 커스텀 하단 버튼 그룹 -->
	<template x-if="$root[$root.primaryKey] && actions && actions.length">
		<div class="custom_buttons">
			<template x-for="(action, idx) in actions" :key="action.action_name || idx">
				<template x-if="action.has_permission && $root.actionPermissions[action.action_name] !== false">
					<input type="button" @click="customAction(true, action.action_name, action.messages, action.confirmation)"
						   :value="action.title" :disabled="freezeForm || freezeActions" />
				</template>
			</template>
		</div>
	</template>

	<!-- 제어 및 액션 버튼 세트 (최초 디자인 복원) -->
	<div class="control_buttons">
		<template x-if="$root[$root.primaryKey]">
			<input type="button" value="<?php echo trans('administrator::administrator.close') ?>"
				   @click="closeItem()" :disabled="freezeForm || freezeActions" />
		</template>

		<template x-if="$root[$root.primaryKey] && actionPermissions.delete">
			<input type="button" value="<?php echo trans('administrator::administrator.delete') ?>"
				   @click="deleteItem()" :disabled="freezeForm || freezeActions" class="remove_button" />
		</template>

		<template x-if="$root[$root.primaryKey] && actionPermissions.update">
			<input type="submit" value="<?php echo trans('administrator::administrator.save') ?>"
				   :disabled="freezeForm || freezeActions" />
		</template>

		<template x-if="!$root[$root.primaryKey]">
			<input type="button" value="<?php echo trans('administrator::administrator.cancel') ?>"
				   @click="closeItem()" :disabled="freezeForm || freezeActions" />
		</template>

		<template x-if="!$root[$root.primaryKey] && actionPermissions.create">
			<input type="submit" value="<?php echo trans('administrator::administrator.create') ?>"
				   :disabled="freezeForm || freezeActions" />
		</template>
		
		<!-- 트랜잭션 진행률 및 알림 -->
		<span class="message" 
			  x-show="statusMessage"
			  :class="{ error: statusMessageType === 'error', success: statusMessageType === 'success' }"
			  x-text="statusMessage"></span>
	</div>
</form>