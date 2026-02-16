<?php

namespace App\Http\Controllers;

use App\Models\KaporItem;
use App\Models\KaporSubmission;
use App\Models\Personnel;
use App\Models\Satker;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Route to the appropriate dashboard based on user role.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('superadmin')) {
            return $this->superadminDashboard($request);
        }

        if ($user->hasRole('admin')) {
            return $this->adminDashboard();
        }

        if ($user->hasRole('admin_satker')) {
            return $this->adminSatkerDashboard($user);
        }

        return $this->personilDashboard($user);
    }

    private function superadminDashboard(Request $request)
    {
        $defaultYear = Setting::getValue('fiscal_year', date('Y'));
        $fiscalYear = $request->get('year', $defaultYear);

        // Get available years for filter
        $availableYears = KaporSubmission::select('fiscal_year')
            ->distinct()
            ->orderBy('fiscal_year', 'desc')
            ->pluck('fiscal_year')
            ->toArray();

        // Ensure current active year and selected year are in the list
        if (!in_array($defaultYear, $availableYears))
            $availableYears[] = $defaultYear;
        if (!in_array($fiscalYear, $availableYears))
            $availableYears[] = $fiscalYear;
        rsort($availableYears);

        $totalPolri = Satker::sum('polri_count');
        $totalPns = Satker::sum('pns_count');
        $totalPersonnel = $totalPolri + $totalPns;

        $submittedCount = KaporSubmission::where('fiscal_year', $fiscalYear)->count();
        $pendingCount = $totalPersonnel - $submittedCount;
        $fillRate = $totalPersonnel > 0 ? round(($submittedCount / $totalPersonnel) * 100, 1) : 0;

        $stats = [
            'total_users' => User::count(),
            'total_personnel' => $totalPersonnel,
            'total_polri' => $totalPolri,
            'total_pns' => $totalPns,
            'total_satkers' => Satker::count(),
            'total_submissions' => $submittedCount,
            'personnel_submitted' => $submittedCount,
            'personnel_pending' => $pendingCount,
            'fill_rate' => $fillRate,
            'total_kapor_items' => KaporItem::where('is_active', true)->count(),
            'fiscal_year' => $fiscalYear,
            'is_locked' => Setting::getValue('is_system_locked', 'false') === 'true',
        ];

        // Fill rate per satker (top-level)
        $poldaId = Satker::where('code', 'POLDA-NTB')->value('id');
        $satkerStats = Satker::query()
            ->selectRaw('satkers.*, (satkers.polri_count + satkers.pns_count) as total_personnel')
            ->withCount(['personnels as submitted_count' => function (\Illuminate\Database\Eloquent\Builder $q) use ($fiscalYear) {
            $q->whereHas('submissions', fn($sq) => $sq->where('fiscal_year', $fiscalYear));
        }])
            ->where(function ($query) use ($poldaId) {
            $query->whereNull('parent_id')->orWhere('parent_id', $poldaId);
        })
            ->orderBy('sort_order')
            ->get();

        // Recent users
        $recentUsers = User::with(['roles', 'satker'])
            ->latest()
            ->limit(8)
            ->get();

        return view('dashboard.superadmin', compact('stats', 'satkerStats', 'recentUsers', 'availableYears', 'fiscalYear', 'defaultYear'));
    }

    private function adminDashboard()
    {
        $fiscalYear = Setting::getValue('fiscal_year', date('Y'));

        $stats = [
            'total_personnel' => Personnel::count(),
            'total_satkers' => Satker::count(),
            'total_submissions' => KaporSubmission::where('fiscal_year', $fiscalYear)->count(),
            'personnel_submitted' => Personnel::whereHas('submissions', fn($q) => $q->where('fiscal_year', $fiscalYear))->count(),
            'personnel_pending' => Personnel::whereDoesntHave('submissions', fn($q) => $q->where('fiscal_year', $fiscalYear))->count(),
            'fiscal_year' => $fiscalYear,
        ];

        return view('dashboard.admin', compact('stats'));
    }

    private function adminSatkerDashboard(User $user)
    {
        $fiscalYear = Setting::getValue('fiscal_year', date('Y'));
        $satkerId = $user->satker_id;
        $satker = Satker::find($satkerId);

        $totalPersonnel = Personnel::where('satker_id', $satkerId)->count();
        $submittedCount = Personnel::where('satker_id', $satkerId)
            ->whereHas('submissions', fn($q) => $q->where('fiscal_year', $fiscalYear))
            ->count();

        $stats = [
            'satker_name' => $satker->name ?? '-',
            'total_personnel' => $totalPersonnel,
            'submitted' => $submittedCount,
            'pending' => $totalPersonnel - $submittedCount,
            'fill_rate' => $totalPersonnel > 0 ? round(($submittedCount / $totalPersonnel) * 100) : 0,
            'fiscal_year' => $fiscalYear,
        ];

        $pendingPersonnel = Personnel::with(['user', 'rank'])
            ->where('satker_id', $satkerId)
            ->whereDoesntHave('submissions', fn($q) => $q->where('fiscal_year', $fiscalYear))
            ->limit(20)
            ->get();

        return view('dashboard.admin-satker', compact('stats', 'pendingPersonnel'));
    }

    private function personilDashboard(User $user)
    {
        $fiscalYear = Setting::getValue('fiscal_year', date('Y'));
        $personnel = $user->personnel;

        $submissions = [];
        $hasSubmitted = false;

        if ($personnel) {
            $submissions = KaporSubmission::with(['kaporItem', 'kaporSize'])
                ->where('personnel_id', $personnel->id)
                ->where('fiscal_year', $fiscalYear)
                ->get();
            $hasSubmitted = $submissions->isNotEmpty();
        }

        return view('dashboard.personil', compact('user', 'personnel', 'submissions', 'hasSubmitted', 'fiscalYear'));
    }
}
