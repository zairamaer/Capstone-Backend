<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $primaryKey = 'customerID';  // PK
    protected $fillable = ['name', 'email', 'phone', 'password'];

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'customerID', 'customerID');  // FK
    }
}