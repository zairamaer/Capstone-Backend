<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
    protected $primaryKey = 'appointmentID'; // PK
    protected $fillable = ['customerID', 'serviceRateID', 'appointmentDateTime', 'status', 'notes'];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customerID', 'customerID');  // FK
    }

    public function serviceRate()
    {
        return $this->belongsTo(ServiceRate::class, 'serviceRateID', 'serviceRateID');  // FK
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'appointmentID', 'appointmentID'); // FK
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class, 'appointmentID', 'appointmentID');  // FK
    }
}
