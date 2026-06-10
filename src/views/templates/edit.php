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

	<!-- 커스텀 버튼 목록 (상단 액션용) -->
	<?php if($config->checkOption('is_top_actions')) { ?>
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
				<div>
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

			<!-- 4. WYSIWYG TYPE (Quill Editor 모던 포팅) -->
			<template x-if="field.type === 'wysiwyg'">
				<div x-data="{ editor: null }"
					 x-init="
						$nextTick(() => {
							if (window.Quill) {
								let container = $el.querySelector('.quill-editor-container');
								if (container) {
									editor = new Quill(container, {
										theme: 'snow',
										modules: {
											toolbar: [
												[{ 'header': [1, 2, 3, 4, 5, 6, false] }],
												['bold', 'italic', 'underline', 'strike'],
												[{ 'list': 'ordered'}, { 'list': 'bullet' }],
												[{ 'color': [] }, { 'background': [] }],
												[{ 'align': [] }],
												['link', 'image'],
												['clean']
											]
										}
									});

									// 초기 데이터 장착
									editor.root.innerHTML = $root[field.field_name] || '';

									// 에디팅 수정 시 Alpine 상태 모델에 즉각 싱크
									editor.on('text-change', () => {
										let html = editor.root.innerHTML;
										if (html === '<p><br></p>') html = '';
										$root[field.field_name] = html;
									});

									// 외부 모델 강제 변경 시 화면에 동기화
									$watch('$root.' + field.field_name, (newVal) => {
										let currentHTML = editor.root.innerHTML;
										if (currentHTML === '<p><br></p>') currentHTML = '';
										if (newVal !== currentHTML) {
											editor.root.innerHTML = newVal || '';
										}
									});
								}
							}
						});
					 "
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

			<!-- 8. ENUM TYPE (Zero-jQuery 순수 Alpine.js 셀렉트) -->
			<template x-if="field.type === 'enum'">
				<div>
					<template x-if="field.editable">
						<select x-model="$root[field.field_name]" :id="field.field_id" style="width: 100%; padding: 5px; border: 1px solid #ccc; border-radius: 4px; background-color: #fff;">
							<option value="">-- 선택 --</option>
							<template x-for="opt in field.options" :key="opt.id">
								<option :value="opt.id" x-text="opt.text || opt.name" :selected="opt.id == $root[field.field_name]"></option>
							</template>
						</select>
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
							   :id="field.field_id" :disabled="freezeForm" x-model="$root[field.field_name]" style="padding: 3px;" />
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
									:disabled="freezeForm" class="remove_button" style="padding: 4px 10px; font-size: 11px;">
								<?php echo trans('administrator::administrator.uploadimage') ?>
							</button>
							<div class="uploading" x-show="field.uploading"
								 x-text="'<?php echo trans('administrator::administrator.imageuploading') ?>' + field.upload_percentage + '%'"></div>
						</div>
					</template>
					<template x-if="$root[field.field_name] && !loadingItem">
						<div class="image_container" style="margin-top: 8px;">
							<template x-if="field.display_raw_value">
								<img :src="$root[field.field_name]" style="max-height: 100px; border: 1px solid #ccc; padding: 2px;" />
							</template>
							<template x-if="!field.display_raw_value">
								<img :src="$root.file_url + '?path=' + field.location + $root[field.field_name]" style="max-height: 100px; border: 1px solid #ccc; padding: 2px;" />
							</template>
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
							<button type="button" @click="$event.target.parentElement.querySelector('input[type=file]').click()"
									:disabled="freezeForm" class="remove_button" style="padding: 4px 10px; font-size: 11px;">
								<?php echo trans('administrator::administrator.uploadfile') ?>
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
							<template x-if="field.symbol">
								<span class="symbol" x-text="field.symbol"></span>
							</template>
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

			<!-- 13. RELATIONSHIP (belongs_to, belongs_to_many, has_many) -->
			<template x-if="initialized && ['belongs_to', 'belongs_to_many', 'has_many'].includes(field.type)">
				<div class="relative w-full"
					 x-data="relationSelect({ field: field, type: 'edit', multiple: ['belongs_to_many', 'has_many'].includes(field.type), autocomplete: field.autocomplete })"
					 style="position: relative; width: 100%;">
					
					<template x-if="field.editable">
						<div class="relation-combobox-wrapper" style="position: relative; width: 100%;">
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

							<!-- 2) 단일/다중 통합 콤보박스 입력 컨트롤러 (display: flex 제거로 아이콘 이탈 해결) -->
							<div class="combobox-trigger-container" style="position: relative; width: 100%;">
								<input type="text" 
									   placeholder="-- 검색 또는 선택 --"
									   x-model="search"
									   @focus="open = true"
									   @click.away="setTimeout(() => open = false, 200)"
									   @input="if (field.autocomplete) fetchAutocomplete()"
									   class="relation-combobox-input"
									   :value="!['belongs_to_many', 'has_many'].includes(field.type) && selectedItems[0] && !search ? selectedItems[0].text : search" />
								
								<!-- 로딩 인디케이터, 돋보기 검색, 및 트리거 화살표 (우측 끝 정렬) -->
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
								 style="position: absolute; left: 0; right: 0; top: 100%; z-index: 50; background: white; border: 1px solid #e5e7eb; border-radius: 4px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); max-height: 200px; overflow-y: auto; margin-top: 6px; box-sizing: border-box;">
								
								<div class="options-list">
									<!-- 검색 결과 없음 표출 -->
									<template x-if="filteredOptions.length === 0 && !loading">
										<div style="padding: 10px 14px; color: #9ca3af; font-size: 12px; text-align: center;">검색 결과가 없습니다.</div>
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
	<?php if(!$config->checkOption('is_top_actions')) { ?>
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
	<?php } ?>

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