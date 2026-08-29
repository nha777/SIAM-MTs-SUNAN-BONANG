<?php

namespace App\Modules\Finance\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Finance\Traits\GeneratesFinanceNumbers;
use App\Modules\Finance\Requests\InvoiceRequest;
use App\Modules\Finance\Services\InvoiceServiceInterface;
use App\Modules\Student\Models\Student;
use App\Modules\Academic\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Modules\Base\Traits\BaseApiResponse;
use Illuminate\Database\QueryException;

class InvoiceController extends Controller {
    use GeneratesFinanceNumbers;
    use BaseApiResponse;

    protected $service;
    
    public function __construct(InvoiceServiceInterface $service) { 
        $this->service = $service; 
    }

    public function index(Request $request) {
        Gate::authorize('finance.view');
        $invoices = $this->service->getAll();
        if ($request->wantsJson()) return $this->successResponse($invoices);
        return view('finance::invoices.index', compact('invoices'));
    }

    public function create() {
        Gate::authorize('finance.create');
        $students = Student::orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('start_year', 'desc')->get();
        return view('finance::invoices.create', compact('students', 'academicYears'));
    }

    public function store(InvoiceRequest $request) {
        Gate::authorize('finance.create');
        $data = $request->validated();
        
        if (!isset($data['status'])) $data['status'] = 'Unpaid';
        
        $maxRetries = 3;
        for ($i = 0; $i < $maxRetries; $i++) {
            try {
                if (empty($data['invoice_number']) || $i > 0) {
                    $data['invoice_number'] = $this->generateInvoiceNumber();
                }
                
                $invoice = $this->service->create($data);
                
                if ($request->wantsJson()) return $this->successResponse($invoice, 'Invoice created', 201);
                return redirect()->route('invoices.index')->with('success', 'Tagihan berhasil dibuat.');
            } catch (QueryException $e) {
                if ($e->errorInfo[1] == 1062 || $e->getCode() == 23000) { // Unique constraint
                    if ($i == $maxRetries - 1) {
                        if ($request->wantsJson()) return response()->json(['success' => false, 'message' => 'Gagal membuat nomor referensi unik.'], 500);
                        return back()->withInput()->with('error', 'Sistem sedang sibuk membuat nomor invoice, silakan coba lagi.');
                    }
                    continue;
                }
                if ($request->wantsJson()) return response()->json(['success' => false, 'message' => 'Kesalahan database.'], 500);
                return back()->withInput()->with('error', 'Terjadi kesalahan sistem.');
            } catch (\Exception $e) {
                if ($request->wantsJson()) return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
                return back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
            }
        }
    }

    public function show($id, Request $request) {
        Gate::authorize('finance.view');
        $invoice = $this->service->getById($id);
        if ($request->wantsJson()) return $this->successResponse($invoice);
        return view('finance::invoices.show', compact('invoice'));
    }

    public function edit($id) {
        Gate::authorize('finance.update');
        $invoice = $this->service->getById($id);
        $students = Student::orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('start_year', 'desc')->get();
        return view('finance::invoices.edit', compact('invoice', 'students', 'academicYears'));
    }

    public function update(InvoiceRequest $request, $id) {
        Gate::authorize('finance.update');
        $invoice = $this->service->update($id, $request->validated());
        if ($request->wantsJson()) return $this->successResponse($invoice, 'Invoice updated');
        return redirect()->route('invoices.index')->with('success', 'Tagihan berhasil diperbarui.');
    }

    public function destroy(Request $request, $id) {
        Gate::authorize('finance.delete');
        $this->service->delete($id);
        if ($request->wantsJson()) return $this->successResponse(null, 'Invoice deleted');
        return redirect()->route('invoices.index')->with('success', 'Tagihan berhasil dihapus.');
    }
}
