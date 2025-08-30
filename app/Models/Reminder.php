<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;
    protected $primaryKey = 'reminderID'; // PK
    protected $fillable = ['appointmentID', 'reminderDateTime', 'reminderType', 'message', 'sent'];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointmentID', 'appointmentID');  // FK
    }
}