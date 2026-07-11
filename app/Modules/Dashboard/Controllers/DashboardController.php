<?php

namespace App\Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Redirect users based on their primary role.
     */
    public function redirect()
    {
        $user = Auth::user();

        if ($user->hasAnyRole(['Super Admin', 'Admin', 'Project Manager', 'Field Manager'])) {
            return redirect()->route('admin.index');
        }

        if ($user->hasRole('Client')) {
            return redirect()->route('client.index');
        }

        if ($user->hasRole('Panelist')) {
            return redirect()->route('panelist.index');
        }

        return redirect()->route('corporate.index');
    }

    /**
     * Admin Dashboard Home page.
     */
    public function index()
    {
        // Gather analytics statistics
        $stats = [
            'total_users' => User::count(),
            'total_roles' => Role::count(),
            'total_permissions' => Permission::count(),
            'active_surveys' => 12,      // Simulated for Phase 3
            'response_rate' => '84.6%',  // Simulated for Phase 3
            'active_projects' => 4       // Simulated for Phase 2
        ];

        return view('Dashboard::index', compact('stats'));
    }
}
