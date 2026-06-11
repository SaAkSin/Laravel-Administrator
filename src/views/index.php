<?php
/**
 * 관리자 메인 진입 뷰 (최초 오리지널 디자인 복원 및 Alpine.js 연동 최적화)
 * 
 * 최초 오리지널 마크업 구조와 클래스를 100% 보존하면서, 데이터 바인딩만 Alpine.js로 대체하여
 * 비주얼 무결성과 현대화된 데이터 처리 효율을 동시에 충족시킵니다.
 */
?>
<div id="admin_page" class="with_sidebar" x-data="adminController" style="position: relative; min-height: 500px;">
	<!-- 1. 스켈레톤 로더 (Alpine.js 및 데이터 초기 마운트 전 노출) -->
	<div x-show="!initialized" 
		 style="width: 100%; display: flex; gap: 20px; padding: 20px; box-sizing: border-box; background-color: #f8fafc;">
		
		<!-- 사이드바 필터 스켈레톤 영역 -->
		<div style="width: 250px; flex-shrink: 0; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; box-sizing: border-box; display: flex; flex-direction: column; gap: 15px; height: fit-content;">
			<div class="skeleton-shimmer" style="height: 18px; width: 60%; border-radius: 4px;"></div>
			<div style="display: flex; flex-direction: column; gap: 15px; margin-top: 10px;">
				<div style="display: flex; flex-direction: column; gap: 6px;">
					<div class="skeleton-shimmer" style="height: 12px; width: 40%; border-radius: 3px;"></div>
					<div class="skeleton-shimmer" style="height: 30px; border-radius: 4px;"></div>
				</div>
				<div style="display: flex; flex-direction: column; gap: 6px;">
					<div class="skeleton-shimmer" style="height: 12px; width: 50%; border-radius: 3px;"></div>
					<div class="skeleton-shimmer" style="height: 30px; border-radius: 4px;"></div>
				</div>
				<div style="display: flex; flex-direction: column; gap: 6px;">
					<div class="skeleton-shimmer" style="height: 12px; width: 30%; border-radius: 3px;"></div>
					<div class="skeleton-shimmer" style="height: 30px; border-radius: 4px;"></div>
				</div>
			</div>
		</div>

		<!-- 메인 그리드 리스트 스켈레톤 영역 -->
		<div style="flex-grow: 1; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; box-sizing: border-box; display: flex; flex-direction: column; gap: 20px;">
			<!-- 상단 헤더 스켈레톤 -->
			<div style="display: flex; justify-content: space-between; align-items: center;">
				<div class="skeleton-shimmer" style="height: 24px; width: 180px; border-radius: 4px;"></div>
				<div style="display: flex; gap: 8px;">
					<div class="skeleton-shimmer" style="height: 30px; width: 80px; border-radius: 4px;"></div>
					<div class="skeleton-shimmer" style="height: 30px; width: 80px; border-radius: 4px;"></div>
				</div>
			</div>
			
			<!-- 그리드 헤더 스크롤 바디 스켈레톤 -->
			<div style="display: flex; gap: 15px; border-bottom: 2px solid #f1f5f9; padding-bottom: 12px; margin-top: 10px;">
				<div class="skeleton-shimmer" style="height: 14px; width: 5%; border-radius: 3px;"></div>
				<div class="skeleton-shimmer" style="height: 14px; width: 35%; border-radius: 3px;"></div>
				<div class="skeleton-shimmer" style="height: 14px; width: 20%; border-radius: 3px;"></div>
				<div class="skeleton-shimmer" style="height: 14px; width: 15%; border-radius: 3px;"></div>
				<div class="skeleton-shimmer" style="height: 14px; width: 25%; border-radius: 3px;"></div>
			</div>

			<!-- 그리드 개별 레코드 행 스켈레톤 -->
			<div style="display: flex; flex-direction: column; gap: 16px;">
				<div style="display: flex; gap: 15px; align-items: center; border-bottom: 1px solid #f8fafc; padding-bottom: 12px;">
					<div class="skeleton-shimmer" style="height: 12px; width: 5%; border-radius: 3px;"></div>
					<div class="skeleton-shimmer" style="height: 12px; width: 35%; border-radius: 3px;"></div>
					<div class="skeleton-shimmer" style="height: 12px; width: 20%; border-radius: 3px;"></div>
					<div class="skeleton-shimmer" style="height: 12px; width: 15%; border-radius: 3px;"></div>
					<div class="skeleton-shimmer" style="height: 12px; width: 25%; border-radius: 3px;"></div>
				</div>
				<div style="display: flex; gap: 15px; align-items: center; border-bottom: 1px solid #f8fafc; padding-bottom: 12px;">
					<div class="skeleton-shimmer" style="height: 12px; width: 5%; border-radius: 3px;"></div>
					<div class="skeleton-shimmer" style="height: 12px; width: 35%; border-radius: 3px;"></div>
					<div class="skeleton-shimmer" style="height: 12px; width: 20%; border-radius: 3px;"></div>
					<div class="skeleton-shimmer" style="height: 12px; width: 15%; border-radius: 3px;"></div>
					<div class="skeleton-shimmer" style="height: 12px; width: 25%; border-radius: 3px;"></div>
				</div>
				<div style="display: flex; gap: 15px; align-items: center; border-bottom: 1px solid #f8fafc; padding-bottom: 12px;">
					<div class="skeleton-shimmer" style="height: 12px; width: 5%; border-radius: 3px;"></div>
					<div class="skeleton-shimmer" style="height: 12px; width: 35%; border-radius: 3px;"></div>
					<div class="skeleton-shimmer" style="height: 12px; width: 20%; border-radius: 3px;"></div>
					<div class="skeleton-shimmer" style="height: 12px; width: 15%; border-radius: 3px;"></div>
					<div class="skeleton-shimmer" style="height: 12px; width: 25%; border-radius: 3px;"></div>
				</div>
				<div style="display: flex; gap: 15px; align-items: center; border-bottom: 1px solid #f8fafc; padding-bottom: 12px;">
					<div class="skeleton-shimmer" style="height: 12px; width: 5%; border-radius: 3px;"></div>
					<div class="skeleton-shimmer" style="height: 12px; width: 35%; border-radius: 3px;"></div>
					<div class="skeleton-shimmer" style="height: 12px; width: 20%; border-radius: 3px;"></div>
					<div class="skeleton-shimmer" style="height: 12px; width: 15%; border-radius: 3px;"></div>
					<div class="skeleton-shimmer" style="height: 12px; width: 25%; border-radius: 3px;"></div>
				</div>
				<div style="display: flex; gap: 15px; align-items: center; border-bottom: 1px solid #f8fafc; padding-bottom: 12px;">
					<div class="skeleton-shimmer" style="height: 12px; width: 5%; border-radius: 3px;"></div>
					<div class="skeleton-shimmer" style="height: 12px; width: 35%; border-radius: 3px;"></div>
					<div class="skeleton-shimmer" style="height: 12px; width: 20%; border-radius: 3px;"></div>
					<div class="skeleton-shimmer" style="height: 12px; width: 15%; border-radius: 3px;"></div>
					<div class="skeleton-shimmer" style="height: 12px; width: 25%; border-radius: 3px;"></div>
				</div>
			</div>
		</div>
	</div>

	<!-- 2. 실제 데이터 렌더링 영역 (인스턴스 초기 구성이 완비되면 페이드인 노출) -->
	<div x-show="initialized" x-cloak style="display: flex; width: 100%;" class="fade-in-content">
		<div id="sidebar">
			<div class="panel sidebar_section" id="filters_sidebar_section">
				<?php echo view("administrator::templates.filters")?>
			</div>
		</div>
		<div id="content">
			<?php echo view("administrator::templates.admin", array('config' => $config))?>
		</div>
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
	/* 1. 스켈레톤 로더용 Shimmer 애니메이션 및 클래스 선언 */
	@keyframes shimmer {
		0% {
			background-position: -200% 0;
		}
		100% {
			background-position: 200% 0;
		}
	}
	.skeleton-shimmer {
		background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
		background-size: 200% 100%;
		animation: shimmer 1.5s infinite linear;
	}
	[x-cloak] {
		display: none !important;
	}
	.fade-in-content {
		animation: fadeInContent 0.25s ease-out forwards;
	}
	@keyframes fadeInContent {
		from {
			opacity: 0;
		}
		to {
			opacity: 1;
		}
	}

	div.item_edit form.edit_form select, div.item_edit form.edit_form input[type=hidden], div.item_edit form.edit_form .select2-container {
		width: <?php echo $formWidth - 75 ?>px !important;
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

	div.item_edit form.edit_form input[type="text"], div.item_edit form.edit_form input[type="password"], div.item_edit form.edit_form textarea, div.item_edit form.edit_form .relation-combobox-wrapper {
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
