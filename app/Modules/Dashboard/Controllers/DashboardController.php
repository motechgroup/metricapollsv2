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
        // Gather analytics statistics from live database models
        $totalUsers = User::count();
        $totalPanelists = User::role('Panelist')->count();
        $totalClients = User::role('Client')->count();
        
        $totalProjects = class_exists(\App\Modules\Projects\Models\Project::class) 
            ? \App\Modules\Projects\Models\Project::count() 
            : 0;
            
        $totalOrganizations = class_exists(\App\Modules\CRM\Models\ClientOrganization::class) 
            ? \App\Modules\CRM\Models\ClientOrganization::count() 
            : 0;
            
        $totalSurveys = class_exists(\App\Modules\SurveyEngine\Models\Survey::class) 
            ? \App\Modules\SurveyEngine\Models\Survey::count() 
            : 0;
        
        // Finances
        $totalBilled = class_exists(\App\Models\Invoice::class) 
            ? \App\Models\Invoice::sum('amount') 
            : 0;
            
        $totalPaid = class_exists(\App\Models\Invoice::class) 
            ? \App\Models\Invoice::where('status', 'paid')->sum('amount') 
            : 0;
            
        $totalPayouts = class_exists(\App\Modules\Wallet\Models\Transaction::class) 
            ? abs(\App\Modules\Wallet\Models\Transaction::where('type', 'withdrawal')->sum('amount')) 
            : 0;

        $stats = [
            'total_users' => $totalUsers,
            'total_panelists' => $totalPanelists,
            'total_clients' => $totalClients,
            'total_roles' => Role::count(),
            'total_permissions' => Permission::count(),
            'active_surveys' => $totalSurveys,
            'total_projects' => $totalProjects,
            'total_organizations' => $totalOrganizations,
            'total_billed' => $totalBilled,
            'total_paid' => $totalPaid,
            'total_payouts' => $totalPayouts,
        ];

        return view('Dashboard::index', compact('stats'));
    }
}
