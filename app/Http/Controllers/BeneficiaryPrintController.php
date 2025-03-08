<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Beneficiary;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BeneficiaryPrintController extends Controller
{
    //

    public function print(Request $request)
{
    $ids = explode(',', $request->ids);
    $beneficiaries = Beneficiary::whereIn('id', $ids)->get();
    return view('beneficiaries.cards', compact('beneficiaries'));
}
}
