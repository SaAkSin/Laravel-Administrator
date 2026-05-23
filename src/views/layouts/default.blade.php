<!DOCTYPE html>
<html lang="<?php echo config('application.language') ?>">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width">
	<title>{{ config('administrator.title') }}</title>

	<!-- 오리지널 패키지 스타일시트 및 컴파일된 에셋들을 순차적으로 로드합니다. -->
	@foreach ($css as $url)
		<link href="{{$url}}" media="all" type="text/css" rel="stylesheet">
	@endforeach

	<!--[if lte IE 9]>
		<link href="{{asset('packages/saaksin/administrator/css/browsers/lte-ie9.css')}}" media="all" type="text/css" rel="stylesheet">
	<![endif]-->

</head>
<body>
	<div id="wrapper">
		@include('administrator::partials.header')

		{!! $content !!}

		@include('administrator::partials.footer')
	</div>

	<!-- 현대적인 Alpine.js 코어와 바닐라 라이브러리 스크립트들을 마운트합니다. -->
	@foreach ($js as $key => $url)
		@if ($key === 'vite-app' || strpos($url, '/dist/js/app') !== false)
			<script type="module" src="{{$url}}"></script>
		@else
			<script src="{{$url}}"></script>
		@endif
	@endforeach
</body>
</html>