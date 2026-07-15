<?php namespace SaAkSin\Administrator\Http\Middleware;

use Closure;

class ValidateModel {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		// 서비스 프로바이더에 한 번 등록된 요청별 설정을 현재 생명주기에서 해석한다.
		app('itemconfig');

		return $next($request);
	}

}
