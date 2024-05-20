<?php

namespace App\Http\Controllers\Api\Siasik\TransaksiSaldo;

use App\Http\Controllers\Controller;
use App\Models\Siasik\Master\RekeningBank;
use App\Models\Siasik\TransaksiSaldo\SaldoAwal_PPK;
use Illuminate\Http\JsonResponse;
// use App\Models\Sigarang\Rekening50;
use Illuminate\Http\Request;

class TransSaldoController extends Controller
{
    public function lihatrekening() {
        $saldo = RekeningBank::get();
        // ->where('noRek', request('rek'))

        return new JsonResponse( $saldo);

    }
    public function transSaldo(Request $request){

        $data = SaldoAwal_PPK::create([
        'bulan'=> $request->bulan,
        'tahun' => $request->tahun,
        'rekening'=> $request->rekening,
        'nilaisaldo'=> $request->nilaisaldo,
        ]);
        // ($request->only('bulan','tahun','rekening','nilaisaldo'));

        return new JsonResponse(['message' => 'berhasil disimpan', 'data' => $data], 200);
    }
}
