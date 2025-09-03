<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CekSessionLogin
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->has('login_role')) {
            return redirect()->route('login')->withErrors([
                'msg' => 'Silakan login terlebih dahulu'
            ]);
        }

        return $next($request);
    }
}