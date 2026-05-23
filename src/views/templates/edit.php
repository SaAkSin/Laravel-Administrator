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
					<div class="quill-editor-container" :id="field.field_id" style="min-height: 150px; background: #fff; border: 1px solid #ccc; border-radius: 4px; color: #333;"></div>
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

			<!-- 13. RELATIONSHIP (belongs_to, belongs_to_many) -->
			<template x-if="['belongs_to', 'belongs_to_many', 'has_many'].includes(field.type)">
				<div class="relative w-full"
					 x-data="{
						open: false,
						search: '',
						options: [],
						loading: false,
						
						init() {
							if (field.autocomplete) {
								const autoKey = field.field_name + '_autocomplete';
								const autoData = $root.autocompleteData[autoKey] || {};
								this.options = Object.values(autoData);
							} else {
								this.options = $root.listOptions[field.field_name] || [];
								this.$watch('$root.listOptions.' + field.field_name, (newVal) => {
									this.options = newVal || [];
								});
							}
						}
					 }"
					 x-init="init()"
					 style="position: relative; width: 100%;">
					
					<template x-if="field.editable">
						<input type="hidden" :id="field.field_id" x-model="$root[field.field_name]" :multiple="field.type === 'belongs_to_many'"
							   x-init="
								 $nextTick(() => {
									let $el = jQuery('#' + field.field_id);
									if (field.autocomplete) {
										$el.select2Remote({
											field: field.field_name,
											type: 'edit',
											multiple: field.type === 'belongs_to_many',
											filterIndex: index
										}).on('change', function(e) {
											$root[field.field_name] = $el.val();
										});
									} else {
										let resultsData = listOptions[field.field_name] || [];
										$el.select2({
											data: { results: resultsData },
											multiple: field.type === 'belongs_to_many'
										}).on('change', function(e) {
											$root[field.field_name] = $el.val();
										});
									}
								 });
							   " />
					</template>

					<template x-if="!field.editable">
						<div class="uneditable" x-text="$root[field.field_name] || '-'"></div>
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