<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Alkoumi\LaravelHijriDate\Hijri;
use Illuminate\Support\Facades\Log;

class MealDistribution extends Model
{
    use HasFactory;

    protected $fillable = ['beneficiary_id', 'distributed_at', 'hijri_date', 'user_id'];


    protected $guarded = ['serial_search'];
    
    protected $dates = ['distributed_at', 'hijri_date'];

     protected static function booted()
    {
        static::creating(function ($meal) {
            // Ensure beneficiary_id is set
            if (empty($meal->beneficiary_id)) {
                Log::error('beneficiary_id فارغ', $meal->toArray());
                abort(422, 'لم يتم تحديد مستفيد');
            }

            // Fetch the beneficiary from the database
            $beneficiary = Beneficiary::find($meal->beneficiary_id);

            if (!$beneficiary) {
                Log::error('المستفيد غير موجود', [
                    'beneficiary_id' => $meal->beneficiary_id,
                    'input_data' => $meal->toArray()
                ]);
                abort(422, 'المستفيد غير موجود في قاعدة البيانات');
            }

            // Generate Hijri date safely
            try {
                $meal->hijri_date = $meal->distributed_at 
                    ? Hijri::date($meal->distributed_at) 
                    : Hijri::date(now());
            } catch (\Exception $e) {
                Log::error('خطأ في توليد التاريخ الهجري: ' . $e->getMessage());
                abort(422, 'خطأ في النظام - الرجاء مراجعة المدخلات');
            }

            // Prevent duplicate meals for the same beneficiary on the same Hijri date
            if (MealDistribution::where('beneficiary_id', $meal->beneficiary_id)
                ->whereDate('hijri_date', $meal->hijri_date)
                ->exists()) {
                abort(422, 'تم تسليم وجبة لهذا المستفيد اليوم');
            }
        });
    }

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

   


}
