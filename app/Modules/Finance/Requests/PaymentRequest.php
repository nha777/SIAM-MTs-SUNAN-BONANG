<?php
namespace App\Modules\Finance\Requests;
use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest {
    public function authorize() { return true; }
    public function rules() {
        return [
            'invoice_id' => 'required|exists:invoices,id',
            'payment_number' => 'nullable|string|unique:payments,payment_number',
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ];
    }
}
