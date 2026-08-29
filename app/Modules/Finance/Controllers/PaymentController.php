<?php

namespace App\Modules\Finance\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Finance\Traits\GeneratesFinanceNumbers;
use App\Modules\Finance\Requests\PaymentRequest;
use App\Modules\Finance\Services\PaymentServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Modules\Base\Traits\BaseApiResponse;
use App\Modules\Finance\Exceptions\OverPaymentException;
use App\Modules\Finance\Exceptions\InvoiceAlreadyPaidException;
use Illuminate\Database\QueryException;

class PaymentController extends Controller {
    use GeneratesFinanceNumbers;
    use BaseApiResponse;

    protected $service;
    
    public function __construct(PaymentServiceInterface $service) { 
        $this->service = $service; 
    }

    public function store(PaymentRequest $request) {
        Gate::authorize('finance.create');
        $data = $request->validated();
        
        $data['recorded_by'] = auth()->id();
        
        $maxRetries = 3;
        for ($i = 0; $i < $maxRetries; $i++) {
            try {
                if (empty($data['payment_number']) || str_starts_with($data['payment_number'], 'PAY-') || $i > 0) {
                    $data['payment_number'] = $this->generatePaymentNumber();
                }
                
                $payment = $this->service->create($data);
                
                if ($request->wantsJson()) return $this->successResponse($payment, 'Payment recorded', 201);
                return redirect()->route('invoices.show', $payment->invoice_id)->with('success', 'Pembayaran berhasil dicatat.');
            } catch (OverPaymentException $e) {
                if ($request->wantsJson()) return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
                return back()->withInput()->with('error', $e->getMessage());
            } catch (InvoiceAlreadyPaidException $e) {
                if ($request->wantsJson()) return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
                return back()->withInput()->with('error', $e->getMessage());
            } catch (QueryException $e) {
                if ($e->errorInfo[1] == 1062 || $e->getCode() == 23000) {
                    if ($i == $maxRetries - 1) {
                        if ($request->wantsJson()) return response()->json(['success' => false, 'message' => 'Gagal membuat nomor referensi unik setelah beberapa percobaan.'], 500);
                        return back()->withInput()->with('error', 'Sistem sedang sibuk. Silakan coba lagi.');
                    }
                    continue; // Retry on unique constraint violation for payment_number or verification_token
                }
                if ($request->wantsJson()) return response()->json(['success' => false, 'message' => 'Terjadi kesalahan database.'], 500);
                return back()->withInput()->with('error', 'Terjadi kesalahan sistem.');
            } catch (\Exception $e) {
                if ($request->wantsJson()) return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
                return back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
            }
        }
    }

    public function destroy(Request $request, $id) {
        Gate::authorize('finance.delete');
        $this->service->delete($id);
        if ($request->wantsJson()) return $this->successResponse(null, 'Payment deleted');
        return back()->with('success', 'Pembayaran berhasil dihapus.');
    }
}
