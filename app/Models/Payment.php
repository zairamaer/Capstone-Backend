<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Payment extends Model
{
    use HasFactory;
    protected $table = 'payments';
    protected $primaryKey = 'paymentID';

    protected $fillable = [
        'appointmentID',
        'paymentDateTime',
        'amount',
        'paymentMethod',
        'transactionID',
        'status',
        'paymongo_checkout_id',
        'paymongo_metadata'
    ];
    protected $casts = [
        'paymentDateTime' => 'datetime',
        'amount' => 'decimal:2',
        'paymongo_metadata' => 'array'
    ];
    // Relationships
    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointmentID', 'appointmentID');
    }
    // Scopes
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('paymentMethod', $method);
    }
    // Accessors
    public function getFormattedAmountAttribute()
    {
        return '₱' . number_format((float) $this->amount, 2);
    }
    public function getIsPayMongoAttribute()
    {
        return !empty($this->paymongo_checkout_id);
    }
    // Mutators
    public function setPayMongoMetadataAttribute($value)
    {
        $this->attributes['paymongo_metadata'] = is_array($value) ? json_encode($value) : $value;
    }
}