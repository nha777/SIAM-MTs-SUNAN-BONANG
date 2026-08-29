<?php
namespace App\Modules\Finance\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Finance\Models\Payment;
use App\Modules\Finance\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class FinancialReportController extends Controller {
    
    public function index(Request $request) {
        Gate::authorize('finance.view');
        
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        
        // Income by Payment Method
        $incomeByMethod = Payment::where('status', 'Verified')
            ->whereMonth('payment_date', $month)
            ->whereYear('payment_date', $year)
            ->select('payment_method', DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->get();
            
        // Income by Date
        $incomeByDate = Payment::where('status', 'Verified')
            ->whereMonth('payment_date', $month)
            ->whereYear('payment_date', $year)
            ->select('payment_date', DB::raw('SUM(amount) as total'))
            ->groupBy('payment_date')
            ->orderBy('payment_date')
            ->get();
            
        $totalIncome = $incomeByMethod->sum('total');
            
        return view('finance::reports.index', compact('month', 'year', 'incomeByMethod', 'incomeByDate', 'totalIncome'));
    }

    public function exportCsv(Request $request) {
        Gate::authorize('finance.view');
        
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        
        $payments = Payment::with(['invoice', 'invoice.student'])
            ->where('status', 'Verified')
            ->whereMonth('payment_date', $month)
            ->whereYear('payment_date', $year)
            ->orderBy('payment_date')
            ->get();
            
        $filename = "Laporan_Keuangan_SIAM_{$year}_{$month}.csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        
        $callback = function() use($payments) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Tanggal', 'No Pembayaran', 'Siswa', 'Tagihan', 'Metode', 'Nominal']);
            
            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->payment_date->format('Y-m-d'),
                    $payment->payment_number,
                    $payment->invoice->student->name ?? '-',
                    $payment->invoice->title ?? '-',
                    $payment->payment_method,
                    $payment->amount
                ]);
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
