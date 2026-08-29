<?php
namespace App\Modules\Finance\Traits;
use Illuminate\Support\Facades\DB;

trait GeneratesFinanceNumbers {
    
    /**
     * Generate format: INV-YYYY-MM-XXXXXX
     */
    protected function generateInvoiceNumber() {
        $prefix = 'INV-' . date('Y-m') . '-';
        $last = DB::table('invoices')
            ->where('invoice_number', 'like', $prefix . '%')
            ->orderBy('invoice_number', 'desc')
            ->first();
            
        $next = $last ? ((int) substr($last->invoice_number, -6)) + 1 : 1;
        return $prefix . str_pad($next, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Generate format: RCP-YYYY-MM-XXXXXX
     */
    protected function generatePaymentNumber() {
        $prefix = 'RCP-' . date('Y-m') . '-';
        $last = DB::table('payments')
            ->where('payment_number', 'like', $prefix . '%')
            ->orderBy('payment_number', 'desc')
            ->first();
            
        $next = $last ? ((int) substr($last->payment_number, -6)) + 1 : 1;
        return $prefix . str_pad($next, 6, '0', STR_PAD_LEFT);
    }
}
