<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleSize extends Model
{
    use HasFactory;
    protected $primaryKey = 'vehicleSizeCode'; // PK
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['vehicleSizeCode', 'vehicleSizeDescription'];

    public function serviceRates()
    {
        return $this->hasMany(ServiceRate::class, 'vehicleSizeCode', 'vehicleSizeCode');  // FK
    }
}
