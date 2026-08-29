<?php
namespace App\Modules\Finance\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Finance\Models\BillingCategory;
use App\Modules\Finance\Models\Invoice;
use App\Modules\Finance\Traits\GeneratesFinanceNumbers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BatchInvoiceController extends Controller {
    
    use GeneratesFinanceNumbers;

    public function create() {
        Gate::authorize('finance.create');
        
        $categories = BillingCategory::orderBy('name')->get();
        $classes = DB::table('classes')->orderBy('name')->get();
        $academicYears = DB::table('academic_years')->orderBy('start_year', 'desc')->get();
        $activeYear = DB::table('academic_years')->where('is_active', true)->first();
        $students = DB::table('students')->select('id', 'name', 'nisn', 'class_id', 'status')->orderBy('name')->get();
        
        return view('finance::batch_invoices.create', compact('categories', 'classes', 'academicYears', 'activeYear', 'students'));
    }

    public function preview(Request $request) {
        Gate::authorize('finance.create');
        
        $request->validate([
            'billing_category_id' => 'required|exists:billing_categories,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'target_type' => 'required|string|in:all,selected,class,level,gender,status,alumni,scholarship',
            'selected_students' => 'nullable|array',
            'class_id' => 'nullable|exists:classes,id',
            'gender' => 'nullable|string|in:L,P',
            'status' => 'nullable|string',
            'due_date' => 'required|date',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duplicate_action' => 'required|string|in:skip,overwrite,abort',
        ]);

        $category = BillingCategory::findOrFail($request->billing_category_id);
        
        $query = DB::table('students');
        
        switch ($request->target_type) {
            case 'all':
                $query->where('status', 'aktif');
                break;
            case 'selected':
                if (empty($request->selected_students)) {
                    return back()->withInput()->withErrors(['selected_students' => 'Pilih setidaknya satu siswa.']);
                }
                $query->whereIn('id', $request->selected_students);
                break;
            case 'class':
                if (empty($request->class_id)) {
                    return back()->withInput()->withErrors(['class_id' => 'Pilih kelas.']);
                }
                $query->where('class_id', $request->class_id)->where('status', 'aktif');
                break;
            case 'gender':
                if (empty($request->gender)) {
                    return back()->withInput()->withErrors(['gender' => 'Pilih jenis kelamin.']);
                }
                $query->where('gender', $request->gender)->where('status', 'aktif');
                break;
            case 'status':
                if (empty($request->status)) {
                    return back()->withInput()->withErrors(['status' => 'Pilih status.']);
                }
                $query->where('status', $request->status);
                break;
            case 'alumni':
                $query->where('status', 'lulus');
                break;
            case 'scholarship':
                $query->where('is_scholarship', true)->where('status', 'aktif');
                break;
            case 'level':
                // Assuming levels are part of class names or we have a level column. We'll skip for now or use pattern
                $query->where('status', 'aktif');
                break;
        }
        
        $students = $query->get();
        if ($students->isEmpty()) {
            return back()->withInput()->withErrors(['target_type' => 'Tidak ada siswa yang ditemukan dengan filter tersebut.']);
        }
        
        $studentIds = $students->pluck('id')->toArray();
        $existingInvoices = DB::table('invoices')
            ->whereIn('student_id', $studentIds)
            ->where('academic_year_id', $request->academic_year_id)
            ->whereRaw('LOWER(title) = ?', [strtolower($request->title)])
            ->pluck('student_id')
            ->toArray();
            
        if ($request->duplicate_action === 'abort' && count($existingInvoices) > 0) {
            return back()->withInput()->withErrors(['duplicate_action' => 'Ditemukan ' . count($existingInvoices) . ' tagihan duplikat. Proses dibatalkan sesuai pengaturan.']);
        }

        $previewData = [];
        $duplicateCount = 0;
        
        foreach ($students as $student) {
            $isDuplicate = in_array($student->id, $existingInvoices);
            if ($isDuplicate) $duplicateCount++;
            
            $previewData[] = [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'nisn' => $student->nisn,
                'amount' => $category->default_amount,
                'is_duplicate' => $isDuplicate,
            ];
        }

        return view('finance::batch_invoices.preview', [
            'request_data' => $request->all(),
            'category' => $category,
            'previewData' => $previewData,
            'duplicateCount' => $duplicateCount,
            'duplicateAction' => $request->duplicate_action,
        ]);
    }

    public function store(Request $request) {
        Gate::authorize('finance.create');
        
        $request->validate([
            'billing_category_id' => 'required',
            'academic_year_id' => 'required',
            'title' => 'required',
            'due_date' => 'required',
            'duplicate_action' => 'required',
            'preview_data' => 'required|json'
        ]);

        $category = BillingCategory::findOrFail($request->billing_category_id);
        $previewData = json_decode($request->preview_data, true);
        
        $insertData = [];
        $now = now();
        $count = 0;
        $overwrittenCount = 0;
        $studentsToOverwrite = [];
        
        DB::beginTransaction();
        
        // Base prefix for batch
        $prefix = 'INV-' . date('Y-m') . '-';
        $last = DB::table('invoices')->where('invoice_number', 'like', $prefix . '%')->lockForUpdate()->orderBy('invoice_number', 'desc')->first();
        $nextNum = $last ? ((int) substr($last->invoice_number, -6)) + 1 : 1;
        try {
            foreach ($previewData as $data) {
                if ($data['is_duplicate']) {
                    if ($request->duplicate_action === 'skip') {
                        continue;
                    } elseif ($request->duplicate_action === 'overwrite') {
                        $studentsToOverwrite[] = $data['student_id'];
                    }
                }
                
                $invoiceNumber = $prefix . str_pad($nextNum++, 6, '0', STR_PAD_LEFT);
                
                $insertData[] = [
                    'id' => (string) Str::uuid(),
                    'student_id' => $data['student_id'],
                    'academic_year_id' => $request->academic_year_id,
                    'billing_category_id' => $category->id,
                    'invoice_number' => $invoiceNumber,
                    'title' => $request->title,
                    'description' => $request->description ?? null,
                    'amount' => $category->default_amount,
                    'paid_amount' => 0,
                    'status' => 'Unpaid',
                    'due_date' => $request->due_date,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $count++;
            }

            if (!empty($studentsToOverwrite)) {
                $overwrittenCount = DB::table('invoices')
                    ->whereIn('student_id', $studentsToOverwrite)
                    ->where('academic_year_id', $request->academic_year_id)
                    ->whereRaw('LOWER(title) = ?', [strtolower($request->title)])
                    ->where('status', 'Unpaid')
                    ->delete();
            }

            if (!empty($insertData)) {
                foreach (array_chunk($insertData, 500) as $chunk) {
                    DB::table('invoices')->insert($chunk);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('batch-invoices.create')->with('error', 'Gagal membuat tagihan: ' . $e->getMessage());
        }

        return redirect()->route('invoices.index')->with('success', "Berhasil membuat $count tagihan. " . ($overwrittenCount > 0 ? "($overwrittenCount tagihan lama ditimpa)" : ""));
    }
}
