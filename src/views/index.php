<?php
/**
 * 관리자 메인 진입 뷰 (최초 오리지널 디자인 복원 및 Alpine.js 연동 최적화)
 * 
 * 최초 오리지널 마크업 구조와 클래스를 100% 보존하면서, 데이터 바인딩만 Alpine.js로 대체하여
 * 비주얼 무결성과 현대화된 데이터 처리 효율을 동시에 충족시킵니다.
 */
?>
<div id="admin_page" class="with_sidebar" x-data="adminController">
	<div id="sidebar">
		<div class="panel sidebar_section" id="filters_sidebar_section">
			<?php echo view("administrator::templates.filters")?>
		</div>
	</div>
	<div id="content">
		<?php echo view("administrator::templates.admin", array('config' => $config))?>
	</div>
</div>

<script type="text/javascript">
	var site_url = "<?php echo url('/') ?>",
		base_url = "<?php echo $baseUrl ?>/",
		asset_url = "<?php echo $assetUrl ?>",
		file_url = "<?php echo route('admin_display_file', array($config->getOption('name'))) ?>",
		rows_per_page_url = "<?php echo route('admin_rows_per_page', array($config->getOption('name'))) ?>",
		route = "<?php echo $route ?>",
		csrf = "<?php echo csrf_token() ?>",
		language = "<?php echo config('app.locale') ?>",
		adminData = {
			primary_key: "<?php echo $primaryKey ?>",
			<?php if ($itemId !== null) {?>
				id: "<?php echo $itemId ?>",
			<?php } ?>
			rows: <?php echo json_encode($rows) ?>,
			rows_per_page: <?php echo $dataTable->getRowsPerPage() ?>,
			sortOptions: <?php echo json_encode($dataTable->getSort()) ?>,
			model_name: "<?php echo $config->getOption('name') ?>",
			model_title: "<?php echo $config->getOption('title') ?>",
			model_single: "<?php echo $config->getOption('single') ?>",
			expand_width: <?php echo $formWidth ?>,
			actions: <?php echo json_encode($actions) ?>,
			global_actions: <?php echo json_encode($globalActions) ?>,
			filters: <?php echo json_encode($filters) ?>,
			edit_fields: <?php echo json_encode($arrayFields) ?>,
			data_model: <?php echo json_encode($dataModel) ?>,
			column_model: <?php echo json_encode($columnModel) ?>,
			action_permissions: <?php echo json_encode($actionPermissions) ?>,
			languages: <?php echo json_encode(trans('administrator::knockout')) ?>
		};
</script>

<style type="text/css">
	div.item_edit form.edit_form select, div.item_edit form.edit_form input[type=hidden], div.item_edit form.edit_form .select2-container {
		width: <?php echo $formWidth - 59 ?>px !important;
	}

	div.item_edit form.edit_form .cke {
		width: <?php echo $formWidth - 67 ?>px !important;
	}

	div.item_edit form.edit_form div.markdown textarea {
		width: <?php echo intval(($formWidth - 75) / 2) - 12 ?>px !important;
		max-width: <?php echo intval(($formWidth - 75) / 2) - 12 ?>px !important;
	}

	div.item_edit form.edit_form div.markdown div.preview {
		width: <?php echo intval(($formWidth - 75) / 2) ?>px !important;
	}

	div.item_edit form.edit_form input[type="text"], div.item_edit form.edit_form input[type="password"], div.item_edit form.edit_form textarea {
		max-width: <?php echo $formWidth - 75 ?>px !important;
		width: <?php echo $formWidth - 75 ?>px !important;
	}

	div.item_edit form.edit_form > div.image img, div.item_edit form.edit_form > div.image div.image_container {
		max-width: <?php echo $formWidth - 65 ?>px;
	}

	#admin_page.with_sidebar #content div.table_container {
		margin-right: 270px;
	}
</style>

<input type="hidden" name="_token" value="<?php echo csrf_token()?>" />
