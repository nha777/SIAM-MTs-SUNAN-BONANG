<?php
namespace App\Modules\Finance\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Finance\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReceiptController extends Controller {
    
    public function show($id) {
        $payment = Payment::with(['invoice', 'invoice.student', 'verifiedBy'])->findOrFail($id);
        
        // Ensure user is authorized to view
        // If it's a parent, they should only see their student's receipt
        $user = auth()->user();
        if ($user->can('finance.view')) {
            // Admin can view all
        } else {
            // Parent check
            $guardian = \DB::table('guardians')->where('user_id', $user->id)->first();
            if (!$guardian) abort(403);
            
            $studentIds = \DB::table('students')->where('guardian_id', $guardian->id)->pluck('id')->toArray();
            if (!in_array($payment->invoice->student_id, $studentIds)) {
                abort(403, 'Unauthorized');
            }
        }
        
        if ($payment->status !== 'Verified') {
            abort(404, 'Receipt only available for verified payments.');
        }

        // Generate a simple validation URL to encode in QR
        $validationUrl = route('receipt.verify', $payment->verification_token);
        
        return view('finance::receipts.show', compact('payment', 'validationUrl'));
    }

    public function verify($token) {
        // Publicly accessible page to verify receipt authenticity via QR
        $payment = Payment::with(['invoice', 'invoice.student'])->where('verification_token', $token)->firstOrFail();
        
        return view('finance::receipts.verify', compact('payment'));
    }
}
