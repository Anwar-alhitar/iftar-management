<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealDistribution extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::creating(function ($beneficiary) {
            $maxSerial = Beneficiary::where('gender', $beneficiary->gender)
                ->max('serial_number');

            $beneficiary->serial_number = ($maxSerial ?? 0) + 1;
        });
    }

    public function mealDistributions()
    {
        return $this->hasMany(MealDistribution::class);
    }
}
