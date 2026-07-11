<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Prevent database exceptions before settings table is migrated
        if (!\Illuminate\Support\Facades\Schema::hasTable('settings')) {
            return $next($request);
        }

        // 1. Check if maintenance mode is enabled
        $maintenanceActive = Setting::getValue('maintenance_mode', '0') === '1';

        if ($maintenanceActive) {
            // 2. Allow administrative staff to bypass maintenance mode
            $user = Auth::user();
            $isStaff = $user && $user->hasAnyRole(['Super Admin', 'Admin', 'Project Manager', 'Field Manager']);

            if (!$isStaff) {
                // 3. Define bypass paths to prevent redirect loops and allow authentication assets
                $allowedPaths = [
                    'maintenance',
                    'login',
                    'logout',
                    'auth/*',
                    'favicon.ico',
                    'favicon.png',
                    'images/*',
                    'build/*',
                    'livewire/*',
                ];

                $isAllowed = false;
                foreach ($allowedPaths as $path) {
                    if ($request->is($path)) {
                        $isAllowed = true;
                        break;
                    }
                }

                if (!$isAllowed) {
                    return redirect()->route('public.maintenance');
                }
            }
        }

        return $next($request);
    }
}
