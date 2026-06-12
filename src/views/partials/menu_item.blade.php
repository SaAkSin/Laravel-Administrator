@if (is_array($item))
	@if (isset($isMobile) && $isMobile)
		<li class="menu" x-data="{ open: false }">
			<span @click="open = !open" style="cursor: pointer;">{{$key}}</span>
			<ul x-show="open" x-cloak :class="{ 'shown': open }">
				@foreach ($item as $k => $subitem)
					<?php echo view("administrator::partials.menu_item", array(
						'item' => $subitem,
						'key' => $k,
						'settingsPrefix' => $settingsPrefix,
						'pagePrefix' => $pagePrefix,
						'isMobile' => true
					))?>
				@endforeach
			</ul>
		</li>
	@else
		<li class="menu">
			<span>{{$key}}</span>
			<ul>
				@foreach ($item as $k => $subitem)
					<?php echo view("administrator::partials.menu_item", array(
						'item' => $subitem,
						'key' => $k,
						'settingsPrefix' => $settingsPrefix,
						'pagePrefix' => $pagePrefix,
						'isMobile' => false
					))?>
				@endforeach
			</ul>
		</li>
	@endif
@else
	<li class="item">
		@if (strpos($key, $settingsPrefix) === 0)
			<a href="{{route('admin_settings', array(substr($key, strlen($settingsPrefix))))}}">{{$item}}</a>
		@elseif (strpos($key, $pagePrefix) === 0)
			<a href="{{route('admin_page', array(substr($key, strlen($pagePrefix))))}}">{{$item}}</a>
		@else
			<a href="{{route('admin_index', array($key))}}">{{$item}}</a>
		@endif
	</li>
@endif
