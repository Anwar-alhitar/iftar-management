<?php

namespace App\Imports;

use App\Models\Beneficiary;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class BeneficiariesImport  implements ToCollection, WithHeadingRow, WithValidation
{
    protected $generateSerials;
    protected $existingSerials;

    public function __construct(bool $generateSerials = false)
    {
        $this->generateSerials = $generateSerials;
        $this->existingSerials = Beneficiary::pluck('serial_number')->toArray();
    }

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            $maxSerial = Beneficiary::max('universal_serial') ?? 0;

            foreach ($rows as $row) {
                $idNumber = $row['id_number'];

                // التحقق من تكرار رقم الهوية
                if (Beneficiary::where('id_number', $idNumber)->exists()) {
                    throw new \Exception("رقم الهوية $idNumber موجود مسبقاً");
                }

                $serialNumber = $this->generateSerials
                    ? ++$maxSerial
                    : $row['serial_number'];

                // التحقق من التكرار إذا كان المستخدم أدخل التسلسل يدوياً
                if (!$this->generateSerials) {
                    if (in_array($serialNumber, $this->existingSerials)) {
                        throw new \Exception("الرقم التسلسلي $serialNumber موجود مسبقاً");
                    }
                    $this->existingSerials[] = $serialNumber;
                }

                Beneficiary::create([
                    'full_name' => $row['full_name'],
                    'id_number' => $idNumber,
                    'universal_serial' => $serialNumber,
                ]);
            }
        });
    }

    public function rules(): array
    {
        $rules = [
            'full_name' => 'required|string|max:255',
            'id_number' => 'required|unique:beneficiaries,id_number',
        ];

        if (!$this->generateSerials) {
            $rules['serial_number'] = 'required|integer|unique:beneficiaries,universal_serial';
        }

        return $rules;
    }

    public function customValidationMessages()
    {
        return [
            'serial_number.unique' => 'الرقم التسلسلي موجود مسبقاً',
            'id_number.unique' => 'رقم الهوية مسجل مسبقاً',
        ];
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Beneficiary([
            //
        ]);
    }
}
