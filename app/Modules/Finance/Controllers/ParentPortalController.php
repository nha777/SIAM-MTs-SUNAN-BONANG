<?php
namespace App\Modules\Finance\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Finance\Traits\GeneratesFinanceNumbers;
use App\Modules\Finance\Models\Invoice;
use App\Modules\Finance\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ParentPortalController extends Controller {
    use GeneratesFinanceNumbers;
    
    public function invoices(Request $request) {
        // Gate::authorize('parent.view'); // Assuming we have a role/permission for this, or just rely on auth middleware
        
        $user = auth()->user();
        
        // Fetch invoices for students linked to this user's guardian profile
        // Since models might not be fully wired up yet, we'll do it manually:
        $guardian = \DB::table('guardians')->where('user_id', $user->id)->first();
        
        if (!$guardian) {
            // For testing/demo, if not a guardian, maybe show an empty state or error
            // Or if admin, show all? Let's just show an error message
            return view('finance::portal.no_guardian');
        }

        $studentIds = \DB::table('students')->where('guardian_id', $guardian->id)->pluck('id');
        
        $invoices = Invoice::with(['student', 'billingCategory'])
            ->whereIn('student_id', $studentIds)
            ->latest()
            ->paginate(15);
            
        return view('finance::portal.invoices', compact('invoices'));
    }

    public function showInvoice($id) {
        $user = auth()->user();
        $guardian = \DB::table('guardians')->where('user_id', $user->id)->first();
        if (!$guardian) abort(403);
        
        $studentIds = \DB::table('students')->where('guardian_id', $guardian->id)->pluck('id')->toArray();
        
        $invoice = Invoice::with(['student', 'billingCategory', 'payments', 'payments.verifiedBy'])->findOrFail($id);
        
        if (!in_array($invoice->student_id, $studentIds)) {
            abort(403, 'Unauthorized action.');
        }

        return view('finance::portal.show_invoice', compact('invoice'));
    }

    public function submitPaymentProof(Request $request, $id) {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'reference_number' => 'required|string',
            'proof' => 'required|image|max:2048', // 2MB Max
        ]);

        $invoice = Invoice::findOrFail($id);
        
        $remaining = $invoice->amount - $invoice->paid_amount;
        if ($request->amount > $remaining) {
            return back()->withInput()->with('error', 'Nominal pembayaran tidak boleh melebihi sisa tagihan (Rp ' . number_format($remaining, 0, ',', '.') . ').');
        }
        
        // Security check
        $user = auth()->user();
        $guardian = \DB::table('guardians')->where('user_id', $user->id)->first();
        if (!$guardian) abort(403);
        $studentIds = \DB::table('students')->where('guardian_id', $guardian->id)->pluck('id')->toArray();
        if (!in_array($invoice->student_id, $studentIds)) {
            abort(403);
        }

        $path = $request->file('proof')->store('payment_proofs', 'public');

        Payment::create([
            'invoice_id' => $invoice->id,
            'payment_number' => $this->generatePaymentNumber(),
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'reference_number' => $request->reference_number,
            'proof_of_payment' => $path,
            'status' => 'Pending',
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diunggah dan sedang menunggu verifikasi.');
    }
}

