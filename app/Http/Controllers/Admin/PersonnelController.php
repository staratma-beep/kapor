<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Personnel;
use App\Models\Rank;
use App\Models\Satker;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Services\AuditLogger;
use Spatie\Permission\Models\Role;

use App\Models\KaporItem;
use App\Models\KaporSubmission;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PersonnelTemplateExport;
use App\Imports\PersonnelImport;
use Barryvdh\DomPDF\Facade\Pdf;

class PersonnelController extends Controller
{
    public function index(Request $request)
    {
        $query = Personnel::with(['rank', 'satker', 'submissions.kaporItem', 'submissions.kaporSize'])->forCurrentSatker()->latest();

        // Stats Calculation
        $fiscalYear = Setting::getValue('fiscal_year', date('Y'));
        $totalReal = Personnel::forCurrentSatker()->count();

        $submittedCount = Personnel::forCurrentSatker()->whereHas('submissions', function ($q) use ($fiscalYear) {
            $q->where('fiscal_year', $fiscalYear);
        })->count();

        $stats = [
            'total_real' => $totalReal,
            'submitted' => $submittedCount,
            'pending' => $totalReal - $submittedCount,
            'active' => Personnel::forCurrentSatker()->where('is_active', true)->count(),
        ];

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                    ->orWhere('nrp', 'LIKE', "%{$search}%")
                    ->orWhereHas('rank', function ($rq) use ($search) {
                    $rq->where('name', 'LIKE', "%{$search}%");
                }
                )
                    ->orWhere('jabatan', 'LIKE', "%{$search}%")
                    ->orWhere('bagian', 'LIKE', "%{$search}%")
                    ->orWhere('keterangan', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('rank_id')) {
            $query->where('rank_id', $request->rank_id);
        }

        if ($request->filled('satker_id')) {
            $query->where('satker_id', $request->satker_id);
        }

        if ($request->filled('keterangan')) {
            $query->where('keterangan', $request->keterangan);
        }

        // Pagination
        $perPage = $request->get('per_page', 10);
        $personnels = $query->paginate($perPage)->withQueryString();

        $ranks = Rank::orderBy('sort_order')->get();
        $satkers = Satker::orderBy('name')->get();
        $bagians = Personnel::whereNotNull('bagian')->distinct()->pluck('bagian');

        // Fetch Kapor Items for Measurement Modal (Put Tutup Kepala ID 9 first)
        $kaporItems = KaporItem::where('is_active', true)
            ->orderByRaw('CASE WHEN id = 9 THEN 0 ELSE 1 END')
            ->orderBy('id')
            ->with(['sizes' => function ($q) {
            $q->orderBy('sort_order');
        }])->get();

        return view('admin.personnel.index', compact('personnels', 'stats', 'ranks', 'satkers', 'bagians', 'perPage', 'kaporItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nrp' => 'required|string|unique:personnels,nrp',
            'full_name' => 'required|string|max:255',
            'rank_id' => 'required|exists:ranks,id',
            'satker_id' => 'required|exists:satkers,id',
            'jabatan' => 'nullable|string|max:255',
            'bagian' => 'nullable|string|max:255',
            'personnel_type' => 'required|in:Polri,PNS',
            'gender' => 'required|in:L,P',
            'phone' => 'nullable|string|max:20',
            'religion' => 'nullable|string|max:50',
            'golongan' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // 1. Create User Account
            $user = User::create([
                'name' => $validated['full_name'],
                'nrp_nip' => $validated['nrp'],
                'password' => Hash::make($validated['nrp']), // NRP as default password
                'satker_id' => $validated['satker_id'],
                'is_active' => true,
            ]);
            $user->assignRole('personil');

            // 2. Create Personnel Record
            $personnelData = $validated;
            $personnelData['user_id'] = $user->id;
            $personnel = Personnel::create($personnelData);

            // Save Measurements if provided
            if ($request->has('measurements') && is_array($request->measurements)) {
                $fiscalYear = Setting::getValue('fiscal_year', date('Y'));

                foreach ($request->measurements as $itemId => $sizeId) {
                    if (empty($sizeId))
                        continue;

                    KaporSubmission::updateOrCreate(
                    [
                        'personnel_id' => $personnel->id,
                        'kapor_item_id' => intval($itemId),
                        'fiscal_year' => $fiscalYear,
                    ],
                    [
                        'kapor_size_id' => intval($sizeId),
                    ]
                    );
                }
            }

            DB::commit();

            return redirect()->route('admin.personnel.index')->with('success', 'Data personil dan ukuran berhasil ditambahkan.');

        }
        catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan personil: ' . $e->getMessage());
        }
    }

    public function storeMeasurements(Request $request, Personnel $personnel)
    {
        $request->validate([
            'measurements' => 'required|array',
        ]);

        // Filter out empty values
        $measurements = array_filter($request->measurements, function ($v) {
            return !empty($v);
        });

        if (empty($measurements)) {
            return redirect()->back()->with('error', 'Silakan pilih minimal satu ukuran.');
        }

        DB::beginTransaction();
        try {
            $fiscalYear = Setting::getValue('fiscal_year', date('Y'));

            foreach ($measurements as $itemId => $sizeId) {
                // Ensure itemId represents a valid KaporItem to prevent FK error
                if (!KaporItem::where('id', $itemId)->exists())
                    continue;

                KaporSubmission::updateOrCreate(
                [
                    'personnel_id' => $personnel->id,
                    'kapor_item_id' => $itemId,
                    'fiscal_year' => $fiscalYear,
                ],
                [
                    'kapor_size_id' => $sizeId,
                ]
                );
            }

            DB::commit();

            // Clear session to prevent modal from reopening
            session()->forget('open_measurement_modal');

            return redirect()->route('admin.personnel.index')->with('success', 'Data ukuran berhasil disimpan.');
        }
        catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan ukuran: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Personnel $personnel)
    {
        $validated = $request->validate([
            'nrp' => 'required|string|unique:personnels,nrp,' . $personnel->id,
            'full_name' => 'required|string|max:255',
            'rank_id' => 'required|exists:ranks,id',
            'satker_id' => 'required|exists:satkers,id',
            'jabatan' => 'nullable|string|max:255',
            'bagian' => 'nullable|string|max:255',
            'personnel_type' => 'nullable|in:Polri,PNS',
            'gender' => 'nullable|in:L,P',
            'phone' => 'nullable|string|max:20',
            'religion' => 'nullable|string|max:50',
            'golongan' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            // Update Personnel
            $personnel->update($validated);

            // Sync User Account if exists
            if ($personnel->user) {
                $personnel->user->update([
                    'name' => $validated['full_name'],
                    'nrp_nip' => $validated['nrp'],
                    'satker_id' => $validated['satker_id'],
                    'is_active' => $request->has('is_active') ? $request->is_active : $personnel->is_active,
                ]);
            }

            // Update Measurements
            if ($request->has('measurements') && is_array($request->measurements)) {
                $fiscalYear = Setting::getValue('fiscal_year', date('Y'));

                foreach ($request->measurements as $itemId => $sizeId) {
                    if (empty($sizeId))
                        continue;

                    KaporSubmission::updateOrCreate(
                    [
                        'personnel_id' => $personnel->id,
                        'kapor_item_id' => intval($itemId),
                        'fiscal_year' => $fiscalYear,
                    ],
                    [
                        'kapor_size_id' => intval($sizeId),
                    ]
                    );
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Data personil dan akun berhasil diperbarui.');
        }
        catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui: ' . $e->getMessage());
        }
    }

    public function destroy(Personnel $personnel)
    {
        DB::beginTransaction();
        try {
            if ($personnel->user) {
                $personnel->user->delete();
            }
            $personnel->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Personil dan akun terkait berhasil dihapus.');
        }
        catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    /**
     * Download Excel template for personnel import.
     */
    public function downloadTemplate()
    {
        return Excel::download(new PersonnelTemplateExport, 'template_import_personil.xlsx');
    }

    /**
     * Import personnel from Excel/CSV.
     */
    public function import(Request $request)
    {
        set_time_limit(0);

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
            'satker_id' => 'required|exists:satkers,id'
        ]);

        try {
            $import = new PersonnelImport($request->satker_id);
            Excel::import($import, $request->file('file'));

            $results = $import->getResults();
            $successCount = $results['success_count'];
            $errorCount = $results['error_count'];
            $errors = $results['errors'];

            AuditLogger::log('Import Personil Excel', 'Manajemen Personil', null, null, null, 'success', "Berhasil: {$successCount}. Gagal: {$errorCount}");

            if ($errorCount > 0) {
                return redirect()->back()->with('warning', "Berhasil mengimpor {$successCount} data. Gagal: {$errorCount}. Contoh error: " . implode(', ', array_slice($errors, 0, 3)));
            }
            return redirect()->back()->with('success', "Berhasil mengimpor {$successCount} data personil.");
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses file: ' . $e->getMessage());
        }
    }

    /**
     * Print Satker PDF Report.
     */
    public function printSatker(Request $request)
    {
        $request->validate([
            'satker_id' => 'required|exists:satkers,id',
            'fiscal_year' => 'nullable|string',
        ]);

        $satker = Satker::findOrFail($request->satker_id);
        $fiscalYear = $request->get('fiscal_year', Setting::getValue('fiscal_year', date('Y')));

        $personnels = Personnel::with(['rank', 'submissions' => function ($q) use ($fiscalYear) {
            $q->where('fiscal_year', $fiscalYear)->with('kaporSize', 'kaporItem');
        }])
            ->where('satker_id', $satker->id)
            ->where('is_active', true)
            ->get();

        // Sort by Rank sort_order, then by Name
        $personnels = $personnels->sort(function ($a, $b) {
            $rankA = $a->rank->sort_order ?? 999;
            $rankB = $b->rank->sort_order ?? 999;

            if ($rankA != $rankB) {
                return $rankA <=> $rankB;
            }

            return strcasecmp($a->full_name, $b->full_name);
        });

        $kaporItems = KaporItem::where('is_active', true)->orderBy('id')->get();

        $pdf = Pdf::loadView('admin.reports.personnel_satker_pdf', [
            'satker' => $satker,
            'fiscalYear' => $fiscalYear,
            'personnels' => $personnels,
            'kaporItems' => $kaporItems,
            'date' => date('d F Y'),
            'location' => $request->get('location', 'Mataram'),
            'signatory_role' => $request->get('signatory_role', 'KASUBBAG RENMIN KABAG LOG'),
            'signatory_name' => $request->get('signatory_name', '__________________________'),
            'signatory_nrp' => $request->get('signatory_nrp', ''),
        ]);

        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream("Data_Kapor_{$satker->name}_{$fiscalYear}.pdf", [
            'Attachment' => 0,
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"Data_Kapor_{$satker->name}_{$fiscalYear}.pdf\"",
            'X-Content-Type-Options' => 'nosniff'
        ]);
    }

    /**
     * Bulk delete personnel by Satker.
     */
    public function bulkDeleteBySatker(Request $request)
    {
        $request->validate([
            'satker_id' => 'required|exists:satkers,id',
            'confirm_text' => 'required|string'
        ]);

        if (strtoupper($request->confirm_text) !== 'HAPUS') {
            return redirect()->back()->with('error', 'Konfirmasi kata kunci salah. Silakan ketik HAPUS untuk melanjutkan.');
        }

        $satker = Satker::findOrFail($request->satker_id);

        try {
            DB::transaction(function () use ($satker) {
                $personnels = Personnel::where('satker_id', $satker->id)->get();
                $count = $personnels->count();

                foreach ($personnels as $personnel) {
                    // Delete submissions
                    $personnel->submissions()->delete();

                    // Delete user account if it exists
                    if ($personnel->user) {
                        $personnel->user->delete();
                    }

                    // Delete personnel
                    $personnel->delete();
                }

                AuditLogger::log('Hapus Bulk Personil', 'Manajemen Personil', $satker, null, null, 'success', "Berhasil menghapus {$count} personil dari Satker: {$satker->name}");
            });

            return redirect()->back()->with('success', "Berhasil menghapus seluruh data personil dari Satker {$satker->name}.");
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
