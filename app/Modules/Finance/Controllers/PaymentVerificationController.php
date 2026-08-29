<?php

namespace App\Modules\Finance\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Finance\Services\PaymentServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Modules\Base\Traits\BaseApiResponse;
use App\Modules\Finance\Jobs\SendWhatsAppNotificationJob;
use Illuminate\Support\Facades\DB;
use App\Modules\Finance\Exceptions\OverPaymentException;
use App\Modules\Finance\Exceptions\InvoiceAlreadyPaidException;

class PaymentVerificationController extends Controller {
    use BaseApiResponse;

    protected $service;
    
    public function __construct(PaymentServiceInterface $service) { 
        $this->service = $service; 
    }

    public function index(Request $request) {
        Gate::authorize('finance.view');
        $payments = $this->service->getPendingVerifications();
        if ($request->wantsJson()) return $this->successResponse($payments);
        return view('finance::verifications.index', compact('payments'));
    }

    public function verify(Request $request, $id) {
        Gate::authorize('finance.update');
        $request->validate([
            'status' => 'required|in:Verified,Rejected',
            'rejection_reason' => 'required_if:status,Rejected|nullable|string',
        ]);
        
        $data = [
            'status' => $request->status,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'rejection_reason' => $request->status === 'Rejected' ? $request->rejection_reason : null,
        ];
        
        try { 
            $payment = $this->service->update($id, $data);
            
            // Kirim Notifikasi WA
            $payment->load(['invoice', 'invoice.student']);
            $guardian = DB::table('guardians')->where('id', $payment->invoice->student->guardian_id)->first();
            if ($guardian && $guardian->phone_number) {
                $statusText = $request->status === 'Verified' ? 'DITERIMA' : 'DITOLAK';
                $amountText = 'Rp ' . number_format($payment->amount, 0, ',', '.');
                
                $msg = "Yth. Bpk/Ibu {$guardian->guardian_name},\nPembayaran untuk tagihan *{$payment->invoice->title}* sebesar {$amountText} telah *{$statusText}*.\n";
                
                if ($request->status === 'Rejected') {
                    $msg .= "Alasan penolakan: {$request->rejection_reason}\nMohon periksa dan unggah kembali bukti pembayaran yang valid.";
                } else {
                    $msg .= "Terima kasih atas pembayaran Anda.";
                }
                
                SendWhatsAppNotificationJob::dispatch($guardian->phone_number, $msg);
            }
            
            if ($request->wantsJson()) return $this->successResponse($payment, 'Payment verification status updated');
            return back()->with('success', 'Status pembayaran berhasil diperbarui.');
            
        } catch (OverPaymentException $e) { 
            if ($request->wantsJson()) return response()->json(['success' => false, 'message' => $e->getMessage()], 422); 
            return back()->with('error', $e->getMessage()); 
        } catch (InvoiceAlreadyPaidException $e) { 
            if ($request->wantsJson()) return response()->json(['success' => false, 'message' => $e->getMessage()], 422); 
            return back()->with('error', $e->getMessage()); 
        } catch (\Exception $e) {
            if ($request->wantsJson()) return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem.'], 500); 
            return back()->with('error', 'Terjadi kesalahan sistem.');
        }
    }
}
