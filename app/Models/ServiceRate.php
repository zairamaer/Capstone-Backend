<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRate extends Model
{
    use HasFactory;
    protected $primaryKey = 'serviceRateID'; // PK
    protected $fillable = ['vehicleSizeCode', 'serviceTypeID', 'price', 'serviceTypeImage'];

    public function vehicleSize()
    {
        return $this->belongsTo(VehicleSize::class, 'vehicleSizeCode', 'vehicleSizeCode');  // FK
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class, 'serviceTypeID', 'serviceTypeID');  // FK
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'serviceRateID', 'serviceRateID');  // FK
    }
}