<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    use HasFactory;
    protected $primaryKey = 'serviceTypeID'; // PK
    protected $fillable = ['serviceTypeName', 'serviceTypeDescription', 'serviceTypeImage'];

    public function serviceRates()
    {
        return $this->hasMany(ServiceRate::class, 'serviceTypeID', 'serviceTypeID');  // FK
    }
}