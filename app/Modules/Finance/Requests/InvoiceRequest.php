<?php
namespace App\Modules\Finance\Requests;
use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest {
    public function authorize() { return true; }
    public function rules() {
        return [
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'invoice_number' => 'nullable|string|unique:invoices,invoice_number,' . $this->invoice,
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'status' => 'nullable|string|in:Unpaid,Partial,Paid',
        ];
    }
}
