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
				<div x-data="{ editor: null }"
					 x-init="
						$nextTick(() => {
							if (window.CKEDITOR) {
								// 중복 초기화 방지를 위해 기존 인스턴스 제거
								if (CKEDITOR.instances[field.field_id]) {
									CKEDITOR.instances[field.field_id].destroy(true);
								}
								editor = CKEDITOR.replace(field.field_id, {
									toolbar: [
										{ name: 'document', items: [ 'Source' ] },
										{ name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
										{ name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll' ] },
										{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
										{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
										{ name: 'links', items: [ 'Link', 'Unlink' ] },
										{ name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar' ] },
										{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
										{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
										{ name: 'tools', items: [ 'Maximize' ] }
									]
								});

								// Alpine.js 모델에서 초기 데이터 로드
								editor.setData($root[field.field_name] || '');

								// CKEditor의 변경 사항을 Alpine.js 상태 모델에 동기화
								editor.on('change', () => {
									$root[field.field_name] = editor.getData();
								});

								// Alpine.js 상태 변경을 감시하여 CKEditor 데이터 업데이트
								$watch('$root.' + field.field_name, (newVal) => {
									if (newVal !== editor.getData()) {
										editor.setData(newVal || '');
									}
								});
							}
						});
					 "
					 class="ckeditor-wrapper"
					 style="margin-top: 6px;">
					<textarea :id="field.field_id" :disabled="freezeForm"></textarea>
				</div>
			</template>

			<!-- 3-2. WYSIWYG2 TYPE (Quill Editor) -->
			<template x-if="field.type === 'wysiwyg2'">
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

									// 초기 데이터 설정
									editor.root.innerHTML = $root[field.field_name] || '';

									// 에디팅 수정 시 Alpine 상태 모델에 즉각 동기화
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
						<select x-model="$root[field.field_name]" :id="field.field_id" style="width: 100%;"
								x-init="
									$nextTick(() => {
										jQuery($el).select2().on('change', function() {
											$root[field.field_name] = jQuery($el).val();
										});
									});
								">
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
							<button type="button" @click="document.getElementById(field.field_name + '_uploader').click()"
									:disabled="freezeForm" class="remove_button" style="padding: 4px 10px; font-size: 11px;">
								<?php echo trans('administrator::administrator.uploadimage') ?>
							</button>
							<div class="uploading" x-show="field.uploading"
								 x-text="'<?php echo trans('administrator::administrator.imageuploading') ?>' + field.upload_percentage + '%'"></div>
						</div>
					</template>
					<template x-if="$root[field.field_name]">
						<div class="image_container" style="margin-top: 8px;">
							<img :src="file_url + '?path=' + field.location + $root[field.field_name]" style="max-height: 100px; border: 1px solid #ccc; padding: 2px;" />
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
									:disabled="freezeForm" class="remove_button" style="padding: 4px 10px; font-size: 11px;">
								<?php echo trans('administrator::administrator.uploadfile') ?>
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
