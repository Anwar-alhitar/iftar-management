<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'id_number',
      
        'gender'
    ];

    public addSerial_namper(){
        static::creating(function ($beneficiary) {
            $maxSerial = Beneficiary::where('gender', $beneficiary->gender)
                ->max('serial_number');

            $beneficiary->serial_number = ($maxSerial ?? 0) + 1;
        });
    }

}
