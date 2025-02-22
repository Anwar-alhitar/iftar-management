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

    protected static function booted()
    {
        static::creating(function ($beneficiary) {
            $beneficiary->serial_number = Beneficiary::where('gender', $beneficiary->gender)
                ->max('serial_number') + 1;
        });
    
        static::saving(function ($beneficiary) {
            $exists = Beneficiary::where('gender', $beneficiary->gender)
                ->where('serial_number', $beneficiary->serial_number)
                ->exists();
                
            if ($exists) {
                throw new \Exception('الرقم التسلسلي موجود مسبقًا لهذا النوع');
            }
        });
    }

}
