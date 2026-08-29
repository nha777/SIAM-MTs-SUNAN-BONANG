<?php
namespace App\Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class BillingCategory extends Model 
{
    use HasUuids;
    
    protected $fillable = ['name', 'description', 'default_amount', 'frequency', 'start_period', 'end_period'];

    protected $casts = [
        'default_amount' => 'decimal:2',
    ];

    public function invoices() { return $this->hasMany(Invoice::class); }
}
