<?php
namespace App\Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\User;

class Payment extends Model 
{
    use HasUuids;
    
    protected $fillable = ['proof_of_payment', 'status', 'verified_by', 'verified_at', 'rejection_reason', 'invoice_id', 'payment_number', 'amount', 'payment_date', 'payment_method', 'reference_number', 'notes', 'recorded_by'];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function verifiedBy() { return $this->belongsTo(User::class, 'verified_by'); }
    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function recordedBy() { return $this->belongsTo(User::class, 'recorded_by'); }
}
