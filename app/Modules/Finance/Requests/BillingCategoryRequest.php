<?php
namespace App\Modules\Finance\Requests;
use Illuminate\Foundation\Http\FormRequest;

class BillingCategoryRequest extends FormRequest {
    public function authorize() { return true; }
    protected function prepareForValidation()
    {
        if ($this->has('start_period') && $this->start_period) {
            $this->merge(['start_period' => $this->start_period . '-01']);
        }
        if ($this->has('end_period') && $this->end_period) {
            $this->merge(['end_period' => $this->end_period . '-01']);
        }
    }

    public function rules() {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'default_amount' => 'required|numeric|min:0',
            'frequency' => 'required|string|in:One-time,Monthly,Yearly',
        ];
    }
}
