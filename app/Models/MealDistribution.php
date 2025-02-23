<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Alkoumi\LaravelHijriDate\Hijri;
use Illuminate\Support\Facades\Log;
class MealDistribution extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::creating(function ($meal) {
            // 1. التحقق من وجود beneficiary_id
            if (empty($meal->beneficiary_id)) {
                Log::error('beneficiary_id فارغ', $meal->toArray());
                abort(422, 'لم يتم تحديد مستفيد');
            }

            // 2. جلب المستفيد من قاعدة البيانات
            $beneficiary = Beneficiary::find($meal->beneficiary_id);

            if (!$beneficiary) {
                Log::error('المستفيد غير موجود', [
                    'beneficiary_id' => $meal->beneficiary_id,
                    'input_data' => $meal->toArray()
                ]);
                abort(422, 'المستفيد غير موجود في قاعدة البيانات');
            }

            // 3. توليد التاريخ الهجري
            try {
                $meal->hijri_date = Hijri::date($meal->distributed_at ?? now());
            } catch (\Exception $e) {
                Log::error('خطأ في توليد التاريخ الهجري: ' . $e->getMessage());
                abort(422, 'خطأ في النظام - الرجاء مراجعة المدخلات');
            }

            // 4. التحقق من التكرار
            if (MealDistribution::where('beneficiary_id', $meal->beneficiary_id)
                ->where('hijri_date', $meal->hijri_date)
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

    protected $guarded = [];
}
