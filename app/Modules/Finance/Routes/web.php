<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Finance\Controllers\InvoiceController;
use App\Modules\Finance\Controllers\PaymentController;
use App\Modules\Finance\Controllers\PaymentVerificationController;
use App\Modules\Finance\Controllers\FinancialDashboardController;
use App\Modules\Finance\Controllers\ParentPortalController;
use App\Modules\Finance\Controllers\FinancialReportController;
use App\Modules\Finance\Controllers\BatchInvoiceController;
use App\Modules\Finance\Controllers\ReceiptController;
use App\Modules\Finance\Controllers\BillingCategoryController;

Route::middleware(['web', 'auth'])->group(function () {
    
    Route::get('finance-dashboard', [FinancialDashboardController::class, 'index'])->name('financial-dashboard.index');

    Route::get('financial-reports', [FinancialReportController::class, 'index'])->name('financial-reports.index');
    Route::get('financial-reports/export-csv', [FinancialReportController::class, 'exportCsv'])->name('financial-reports.export');
    
    
    
    Route::get('receipts/{id}', [ReceiptController::class, 'show'])->name('receipts.show');
    Route::get('verify-receipt/{payment_number}', [ReceiptController::class, 'verify'])->name('receipt.verify'); // Public verification route
    
    Route::resource('invoices', InvoiceController::class);

    Route::get('batch-invoices/create', [BatchInvoiceController::class, 'create'])->name('batch-invoices.create');
    Route::post('batch-invoices/preview', [BatchInvoiceController::class, 'preview'])->name('batch-invoices.preview');
    Route::post('batch-invoices/store', [BatchInvoiceController::class, 'store'])->name('batch-invoices.store');
    

    // Parent Portal Routes
    Route::prefix('portal')->name('portal.')->group(function () {
        Route::get('invoices', [ParentPortalController::class, 'invoices'])->name('invoices');
        Route::get('invoices/{id}', [ParentPortalController::class, 'showInvoice'])->name('invoices.show');
        Route::post('invoices/{id}/pay', [ParentPortalController::class, 'submitPaymentProof'])->name('invoices.pay');
    });
    
    Route::get('payment-verifications', [PaymentVerificationController::class, 'index'])->name('payment-verifications.index');
    Route::post('payment-verifications/{id}', [PaymentVerificationController::class, 'verify'])->name('payment-verifications.verify');
    
    Route::resource('billing-categories', BillingCategoryController::class);
    Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::delete('payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
});
