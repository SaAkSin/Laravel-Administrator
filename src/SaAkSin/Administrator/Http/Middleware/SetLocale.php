<?php namespace SaAkSin\Administrator\Http\Middleware;

use Closure;

class SetLocale {

	/**
	 * 현재 관리자 요청의 세션 로케일을 적용한다.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$locale = $request->session()->get('administrator_locale');
		$allowedLocales = config('administrator.locales', array());

		if (!is_string($locale) || !in_array($locale, $allowedLocales, true))
		{
			$locale = config('app.locale');
		}

		app()->setLocale($locale);

		return $next($request);
	}

}
