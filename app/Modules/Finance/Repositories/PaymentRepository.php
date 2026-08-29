<?php

namespace App\Modules\Finance\Repositories;

use App\Modules\Finance\Models\Payment;
use App\Modules\Finance\Models\Invoice;
use Illuminate\Support\Facades\DB;
use App\Modules\Finance\Exceptions\OverPaymentException;
use App\Modules\Finance\Exceptions\InvoiceAlreadyPaidException;
use Illuminate\Support\Str;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            $invoice = Invoice::findOrFail($data['invoice_id']);
            
            if ($invoice->status === 'Paid') {
                throw new InvoiceAlreadyPaidException('Tagihan sudah lunas, tidak dapat menerima pembayaran baru.');
            }
            
            $status = $data['status'] ?? 'Verified';
            if ($status === 'Verified') {
                $totalPaid = Payment::where('invoice_id', $invoice->id)->where('status', 'Verified')->lockForUpdate()->sum('amount');
                if ($totalPaid + $data['amount'] > $invoice->amount) {
                    throw new OverPaymentException('Nominal pembayaran melebihi sisa tagihan.');
                }
            }
            
            if (empty($data['verification_token'])) {
                $data['verification_token'] = (string) Str::uuid();
            }

            $payment = Payment::create($data);
            
            if ($payment->status === 'Verified') {
                $this->updateInvoiceStatus($payment->invoice_id);
            }
            
            DB::commit();
            return $payment;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update($id, array $data)
    {
        DB::beginTransaction();
        try {
            $payment = Payment::findOrFail($id);
            $oldStatus = $payment->status;
            
            $status = $data['status'] ?? $payment->status;
            if ($status === 'Verified' && $oldStatus !== 'Verified') {
                $invoice = Invoice::findOrFail($payment->invoice_id);
                if ($invoice->status === 'Paid') {
                    throw new InvoiceAlreadyPaidException('Tagihan sudah lunas, tidak dapat memverifikasi pembayaran ini.');
                }
                $totalPaid = Payment::where('invoice_id', $invoice->id)->where('status', 'Verified')->lockForUpdate()->sum('amount');
                if ($totalPaid + $payment->amount > $invoice->amount) {
                    throw new OverPaymentException('Verifikasi ditolak: Nominal pembayaran akan melebihi sisa tagihan.');
                }
            }

            $payment->update($data);
            
            if ($oldStatus !== $payment->status) {
                $this->updateInvoiceStatus($payment->invoice_id);
            }
            
            DB::commit();
            return $payment;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $payment = Payment::findOrFail($id);
            $invoiceId = $payment->invoice_id;
            
            $payment->delete();
            
            $this->updateInvoiceStatus($invoiceId);
            
            DB::commit();
            return $payment;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function updateInvoiceStatus($invoiceId)
    {
        $invoice = Invoice::lockForUpdate()->findOrFail($invoiceId);
        $totalPaid = Payment::where('invoice_id', $invoiceId)
            ->where('status', 'Verified')
            ->sum('amount');
            
        $invoice->paid_amount = $totalPaid;
        
        if ($invoice->paid_amount >= $invoice->amount) {
            $invoice->status = 'Paid';
        } elseif ($invoice->paid_amount > 0) {
            $invoice->status = 'Partial';
        } else {
            $invoice->status = 'Unpaid';
        }
        
        $invoice->save();
    }
    
    public function getPendingVerifications($paginate = 15)
    {
        return Payment::with(['invoice', 'invoice.student'])->where('status', 'Pending')->latest()->paginate($paginate);
    }
}
