<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Applies contact details and social links edited in Admin → Settings over
 * config/artform.php for the current request.
 */
class ApplySiteSettings
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            foreach (Setting::values() as $key => $value) {
                config(["artform.{$key}" => $value !== '' ? $value : null]);
            }
        } catch (Throwable) {
            // Database not migrated yet — keep the config defaults.
        }

        return $next($request);
    }
}
