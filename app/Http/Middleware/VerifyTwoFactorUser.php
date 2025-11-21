<?php

namespace App\Http\Middleware;

use Closure;
use Phaseolies\Http\Request;
use Phaseolies\Http\Response;
use Phaseolies\Middleware\Contracts\Middleware;

class VerifyTwoFactorUser implements Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure(\Phaseolies\Http\Request): Response $next
     * @return Response
     */
    public function __invoke(Request $request, Closure $next): Response
    {
        if (!session()->has('2fa:user:id')) {
            return redirect('/login')->withError('Please login first');
        }

        $timestamp = session()->get('2fa:timestamp');
        if ($timestamp && (time() - $timestamp) > 300) {
            session()->forget('2fa:user:id');
            session()->forget('2fa:timestamp');

            return redirect('/login')->withError('Two-factor authentication session expired');
        }

        return $next($request);
    }
}
