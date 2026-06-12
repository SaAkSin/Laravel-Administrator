<div id="settings_page" class="with_sidebar" x-data="adminController" style="position: relative; min-height: 500px;">
	<!-- 1. 스켈레톤 로더 (Alpine.js 및 데이터 초기 마운트 전 노출) -->
	<div x-show="!initialized" 
		 style="width: 100%; padding: 20px; box-sizing: border-box; background-color: #f8fafc;">
		<div style="width: 100%; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; box-sizing: border-box; display: flex; flex-direction: column; gap: 20px;">
			<div class="skeleton-shimmer" style="height: 24px; width: 180px; border-radius: 4px;"></div>
			<div class="skeleton-shimmer" style="height: 150px; width: 100%; border-radius: 4px;"></div>
		</div>
	</div>

	<!-- 2. 실제 데이터 렌더링 영역 (인스턴스 초기 구성이 완비되면 페이드인 노출) -->
	<div x-show="initialized" x-cloak class="fade-in-content" style="display: none; width: 100%;" :style="initialized ? 'display: flex !important;' : 'display: none !important;'">
		<div id="content" style="width: 100%;">
			<?php echo view("administrator::templates.settings")?>
		</div>
	</div>
</div>

<script type="text/javascript">
	var site_url = "<?php echo Request::url() ?>",
		base_url = "<?php echo $baseUrl ?>/",
		asset_url = "<?php echo $assetUrl ?>",
		save_url = "<?php echo route('admin_settings_save', array($config->getOption('name'))) ?>",
		custom_action_url = "<?php echo route('admin_settings_custom_action', array($config->getOption('name'))) ?>",
		file_url = "<?php echo route('admin_settings_display_file', array($config->getOption('name'))) ?>",
		route = "<?php echo $route ?>",
		csrf = "<?php echo csrf_token() ?>",
		language = "<?php echo config('app.locale') ?>",
		adminData = {
			name: "<?php echo $config->getOption('name') ?>",
			title: "<?php echo $config->getOption('title') ?>",
			data: <?php echo json_encode($config->getDataModel()) ?>,
			actions: <?php echo json_encode($actions) ?>,
			edit_fields: <?php echo json_encode($arrayFields) ?>,
			languages: <?php echo json_encode(trans('administrator::knockout')) ?>
		};
</script>

<style type="text/css">
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
	/* settings form style */
	.settings_form select, .settings_form input[type=hidden], .settings_form .select2-container {
		width: 100% !important;
	}
	.settings_form input[type="text"], .settings_form input[type="password"], .settings_form textarea, .settings_form .relation-combobox-wrapper {
		width: 100% !important;
	}
</style>

<input type="hidden" name="_token" value="<?php echo csrf_token()?>" />
