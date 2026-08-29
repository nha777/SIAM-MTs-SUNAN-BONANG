<?php
namespace App\Modules\Finance\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Finance\Models\Invoice;
use App\Modules\Finance\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FinancialDashboardController extends Controller {
    public function index() {
        // Use Spatie permission check via the user model to ensure permission resolution
        if (! auth()->check() || ! auth()->user()->can('finance.view')) {
            abort(403);
        }
        
        $totals = Invoice::selectRaw('SUM(amount) as total_amount, SUM(paid_amount) as total_paid')->first();
        
        $stats = [
            'total_invoices_amount' => $totals->total_amount ?? 0,
            'total_paid_amount' => $totals->total_paid ?? 0,
            'total_unpaid_amount' => ($totals->total_amount ?? 0) - ($totals->total_paid ?? 0),
            
            'count_unpaid' => Invoice::where('status', 'Unpaid')->count(),
            'count_partial' => Invoice::where('status', 'Partial')->count(),
            'count_paid' => Invoice::where('status', 'Paid')->count(),
            
            'pending_verifications' => Payment::where('status', 'Pending')->count(),
        ];
        
        // Recent payments
        $recentPayments = Payment::with(['invoice', 'invoice.student'])
            ->where('status', 'Verified')
            ->latest()
            ->take(5)
            ->get();
            
        return view('finance::dashboard.index', compact('stats', 'recentPayments'));
    }
}
