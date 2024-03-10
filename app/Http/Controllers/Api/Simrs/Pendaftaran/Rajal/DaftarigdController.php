<?php

namespace App\Http\Controllers\Api\Simrs\Pendaftaran\Rajal;

use App\Helpers\FormatingHelper;
use App\Http\Controllers\Controller;
use App\Models\Simrs\Master\Mpasien;
use App\Models\Simrs\Pendaftaran\Karcispoli;
use App\Models\Simrs\Rajal\KunjunganPoli;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DaftarigdController extends Controller
{
    public static function simpanMpasien($request)
    {
        $request->validate([
            'norm' => 'required|string|max:6|min:6',
            'tglmasuk' => 'required|date_format:Y-m-d H:i:s',
            'nik' => 'string|max:16|min:16',
            'tgllahir' => 'required|date_format:Y-m-d'
        ]);
        $masterpasien = Mpasien::updateOrCreate(
            ['rs1' => $request->norm],
            [
                'rs2' => $request->nama,
                'rs3' => $request->sapaan,
                'rs4' => $request->alamat,
                'alamatdomisili' => $request->alamatdomisili,
                'rs5' => $request->kelurahan,
                'kd_kel' => $request->kodekelurahan,
                'rs6' => $request->kecamatan,
                'kd_kec' => $request->kodekecamatan,
                'rs7' => $request->rt,
                'rs8' => $request->rw,
                'rs10' => $request->propinsi,
                'kd_propinsi' => $request->kodepropinsi,
                'rs11' => $request->kabupatenkota,
                'kd_kota' => $request->kodekabupatenkota,
                'rs49' => $request->nik,
                'rs37' => $request->templahir,
                'rs16' => $request->tgllahir,
                'rs17' => $request->kelamin,
                'rs19' => $request->pendidikan,
                'kd_kelamin' => $request->kodekelamin,
                'rs22' => $request->agama,
                'kd_agama' => $request->kodemapagama,
                'rs39' => $request->suku,
                'rs55' => $request->noteleponhp,
                'bahasa' => $request->bahasa,
                'noidentitaslain' => $request->nomoridentitaslain,
                'namaibu' => $request->namaibukandung,
                'kodepos' => $request->kodepos,
                'kd_negara' => $request->negara,
                'kd_rt_dom' => $request->rtdomisili,
                'kd_rw_dom' => $request->rwdomisili,
                'kd_kel_dom' => $request->kelurahandomisili,
                'kd_kec_dom' => $request->kecamatandomisili,
                'kd_kota_dom' => $request->kabupatenkotadomisili,
                'kodeposdom' => $request->kodeposdomisili,
                'kd_prov_dom' => $request->propinsidomisili,
                'kd_negara_dom' => $request->negaradomisili,
                'noteleponrumah' => $request->noteleponrumah,
                'kd_pendidikan' => $request->kodependidikan,
                'kd_pekerjaan' => $request->pekerjaan,
                'flag_pernikahan' => $request->statuspernikahan,
                'rs46' => $request->nokabpjs,
                'rs40' => $request->barulama,
                'gelardepan' => $request->gelardepan,
                'gelarbelakang' => $request->gelarbelakang
            ]
        );
        return $masterpasien;
    }

    public static function simpankunjunganpoli($request)
    {
        $tglmasukx = Carbon::create($request->tglmasuk);
        $tglmasuk = $tglmasukx->toDateString();
        $cekpoli = KunjunganPoli::where('rs2', $request->norm)
            ->where('rs8', $request->kodepoli)
            ->whereDate('rs3', $tglmasuk)
            ->count();

        if ($cekpoli > 0) {
            // return new JsonResponse(['message' => 'PASIEN SUDAH ADA DI HARI DAN POLI YANG SAMA'], 500);
            return false;
        }

        DB::select('call reg_igd(@nomor)');
        $hcounter = DB::table('rs1')->select('rs10')->get();
        $wew = $hcounter[0]->rs10;
        $noreg = FormatingHelper::gennoreg($wew, 'X');

        $input = new Request([
            'noreg' => $noreg
        ]);

        $input->validate([
            'noreg' => 'required|unique:rs17,rs1'
        ]);

        //   $wew =  Validator::make($input, [
        //         'noreg' => 'unique:rs17,rs1'
        //     ]);

        $simpankunjunganpoli = KunjunganPoli::create([
            'rs1' => $input->noreg,
            'rs2' => $request->norm,
            'rs3' => $request->tglmasuk,
            'rs6' => $request->asalrujukan,
            'rs8' => $request->kodepoli,
            //'rs9' => $request->dpjp,
            'rs10' => 0,
            'rs11' => '',
            'rs12' => 0,
            'rs13' => 0,
            'rs14' => $request->kodesistembayar,
            'rs15' => $request->karcis,
            'rs18' => auth()->user()->pegawai_id,
            'rs20' => 'Pendaftaran IGD',

        ]);
        return [
            'simpan' => $simpankunjunganpoli ? '' : $simpankunjunganpoli,
            'input' => $input,
            'masuk' => $tglmasuk,
            'count' => $cekpoli
        ];
    }

    public function tagihanadminigd($input, $request)
    {
        $taguhanigd = Karcispoli::firstOrcreate(
            ['rs1' => $input, 'rs3' => 'A2#'],
            [
                'rs4' => date('Y-m-d'),
                'rs5' => 'D',
                'rs6' => 'Administrasi IGD',
                'rs7' => 8000
            ]
        );
        return $taguhanigd;
    }

    public function simpandaftar(Request $request)
    {
        try {
            //code...
            DB::beginTransaction();

            //-----------Masuk Transaksi--------------
            // $user = auth()->user(]);
            $masterpasien = $this->simpanMpasien($request);
            $simpankunjunganpoli = $this->simpankunjunganpoli($request);
            $tagihanigd = $this->tagihanadminigd($simpankunjunganpoli['input']->noreg, $request);
            // if ($simpankunjunganpoli) {
            //     $karcis = $this->simpankarcis($request, $simpankunjunganpoli['input']->noreg);
            // }
            // $updateantrian = $this->updatelogantrian($request, $simpankunjunganpoli['input']->noreg);
            // $bpjs_antrian = $this->bpjs_antrian($request, date('Y-m-d'), $simpankunjunganpoli['input']->noreg);
            // $addantriantobpjs = BridantrianbpjsController::addantriantobpjs($request,$simpankunjunganpoli['input']->noreg);

            DB::commit();
            return new JsonResponse(
                [
                    'message' => 'DATA TERSIMPAN...!!!',
                    'noreg' => $simpankunjunganpoli ? $simpankunjunganpoli['input']->noreg : 'gagal',
                    'cek' => $simpankunjunganpoli ? $simpankunjunganpoli['count'] : 'gagal',
                    'masuk' => $simpankunjunganpoli ? $simpankunjunganpoli['masuk'] : 'gagal',
                    'hasil' => $simpankunjunganpoli ? $simpankunjunganpoli['simpan'] : 'gagal',
                    'tagihanigd' => $tagihanigd ? $tagihanigd['simpan'] : 'gagal',
                    // 'karcis' => $karcis ? $karcis : 'gagal',
                    // 'updateantrian' => $updateantrian ? $updateantrian : 'gagal',
                    // 'bpjs_antrian' => $bpjs_antrian ? $bpjs_antrian : 'gagal',
                    // 'addantriantobpjs' => $addantriantobpjs ? $addantriantobpjs : 'gagal',
                    'master' => $masterpasien,
                ],
                200
            );
        } catch (\Exception $th) {
            //throw $th;
            DB::rollBack();
            return response()->json(['Gagal tersimpan' => $th], 500);
        }
    }

    public function daftarkunjunganpasienbpjs()
    {
        if (request('from') === '' || request('from') === null) {
            $tgl = Carbon::now()->format('Y-m-d 00:00:00');
            $tglx = Carbon::now()->format('Y-m-d 23:59:59');
        } else {
            $tgl = request('from') . ' 00:00:00';
            $tglx = request('to') . ' 23:59:59';
        }
        $daftarkunjunganpasienbpjs = KunjunganPoli::select(
            'rs17.rs1', // iki tak munculne maneh gawe relasi with
            'rs17.rs1 as noreg',
            'rs17.rs2 as norm',
            'rs17.rs3 as tgl_kunjungan',
            'rs17.rs8 as kodepoli',
            'rs19.rs2 as poli',
            'rs17.rs9 as kodedokter',
            'rs21.rs2 as dokter',
            'rs17.rs14 as kodesistembayar',
            'rs9.rs2 as sistembayar',
            DB::raw('concat(rs15.rs3," ",rs15.gelardepan," ",rs15.rs2," ",rs15.gelarbelakang) as nama'),
            DB::raw('concat(rs15.rs4," KEL ",rs15.rs5," RT ",rs15.rs7," RW ",rs15.rs8," ",rs15.rs6," ",rs15.rs11," ",rs15.rs10) as alamat'),
            DB::raw('concat(TIMESTAMPDIFF(YEAR, rs15.rs16, CURDATE())," Tahun ",
                        TIMESTAMPDIFF(MONTH, rs15.rs16, CURDATE()) % 12," Bulan ",
                        TIMESTAMPDIFF(DAY, TIMESTAMPADD(MONTH, TIMESTAMPDIFF(MONTH, rs15.rs16, CURDATE()), rs15.rs16), CURDATE()), " Hari") AS usia'),
            'rs15.rs16 as tgllahir',
            'rs15.rs17 as kelamin',
            'rs15.rs19 as pendidikan',
            'rs15.rs22 as agama',
            'rs15.rs37 as templahir',
            'rs15.rs39 as suku',
            'rs15.rs40 as jenispasien',
            'rs15.rs46 as noka',
            'rs15.rs49 as nktp',
            'rs15.rs55 as nohp',
            'rs222.rs8 as sep',
            'rs222.kodedokterdpjp as kodedokterdpjp',
            'rs222.dokterdpjp as dokterdpjp',
            'generalconsent.norm as generalconsent',
            // 'bpjs_respon_time.taskid as taskid',
            // TIMESTAMPDIFF(DAY, TIMESTAMPADD(MONTH, TIMESTAMPDIFF(MONTH, rs15 . rs16, now()), rs15 . rs16), now(), " Hari ")
        )
            ->leftjoin('rs15', 'rs15.rs1', '=', 'rs17.rs2') //pasien
            ->leftjoin('rs19', 'rs19.rs1', '=', 'rs17.rs8') //poli
            ->leftjoin('rs21', 'rs21.rs1', '=', 'rs17.rs9') //dokter
            ->leftjoin('rs9', 'rs9.rs1', '=', 'rs17.rs14') //sistembayar
            ->leftjoin('rs222', 'rs222.rs1', '=', 'rs17.rs1') //sep
            ->leftjoin('generalconsent', 'generalconsent.norm', '=', 'rs17.rs2')
            // ->leftjoin('bpjs_respon_time', 'bpjs_respon_time.noreg', '=', 'rs17.rs1')
            ->whereBetween('rs17.rs3', [$tgl, $tglx])
            ->where('rs17.rs8', '=', 'POL014')
            ->where(function ($query) {
                $query->where('rs15.rs2', 'LIKE', '%' . request('q') . '%')
                    ->orWhere('rs15.rs46', 'LIKE', '%' . request('q') . '%')
                    ->orWhere('rs17.rs2', 'LIKE', '%' . request('q') . '%')
                    ->orWhere('rs17.rs1', 'LIKE', '%' . request('q') . '%')
                    ->orWhere('rs19.rs2', 'LIKE', '%' . request('q') . '%')
                    ->orWhere('rs21.rs2', 'LIKE', '%' . request('q') . '%')
                    ->orWhere('rs222.rs8', 'LIKE', '%' . request('q') . '%')
                    ->orWhere('rs9.rs2', 'LIKE', '%' . request('q') . '%');
            })
            ->with([
                'newapotekrajal' => function ($newapotekrajal) {
                    $newapotekrajal->with([
                        'permintaanresep.mobat:kd_obat,nama_obat',
                        'permintaanracikan.mobat:kd_obat,nama_obat',
                    ])
                        ->orderBy('id', 'DESC');
                },
            ])
            ->orderby('rs17.rs3', 'DESC')
            ->paginate(request('per_page'));

        return new JsonResponse($daftarkunjunganpasienbpjs);
    }
}
