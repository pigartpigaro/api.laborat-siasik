<?php

namespace App\Http\Controllers\Api\Simrs\Penunjang\Farmasinew\Pemesanan;

use App\Helpers\FormatingHelper;
use App\Http\Controllers\Controller;
use App\Models\Simrs\Penunjang\Farmasinew\Pemesanan\PemesananHeder;
use App\Models\Simrs\Penunjang\Farmasinew\Pemesanan\PemesananRinci;
use App\Models\Simrs\Penunjang\Farmasinew\RencanabeliH;
use App\Models\Simrs\Penunjang\Farmasinew\RencanabeliR;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PemesananController extends Controller
{
    public function simpan(Request $request)
    {
        $cekjumlaha = PemesananRinci::select('jumlahdpesan')->where('noperencanaan', $request->noperencanaan)
            ->where('kdobat', $request->kdobat)
            ->sum('jumlahdpesan');
        $jumlaha = $cekjumlaha + $request->jumlahdpesan;

        if ($jumlaha > $request->jumlahdirencanakan) {
            return new JsonResponse(['message' => 'Maaf jumlah yang anda pesan melebihi jumlah yg di rencankan...!!!'], 500);
        }

        if ($jumlaha === $request->jumlahdirencanakan) {
            RencanabeliR::where('no_rencbeliobat', $request->noperencanaan)->where('kdobat', $request->kdobat)
                ->update(['flag' => '1']);
        }

        if ($request->nopemesanan === '' || $request->nopemesanan === null) {

            DB::connection('farmasi')->select('call pemesanan_obat(@nomor)');
            $x = DB::connection('farmasi')->table('conter')->select('pemesanan')->get();
            $wew = $x[0]->pemesanan;
            $nopemesanan = FormatingHelper::pemesananobat($wew, 'PES-BOBAT');

            $simpanheder = PemesananHeder::create([
                'nopemesanan' => $nopemesanan,
                'tgl_pemesanan' => date('Y-m-d H:i:s'),
                'kdpbf' => $request->kdpbf,
                'kd_ruang' => $request->gudang ?? '',
                'user' => auth()->user()->pegawai_id
            ]);

            if (!$simpanheder) {
                return new JsonResponse(['message' => 'not ok'], 500);
            }

            $simpanrinci = PemesananRinci::create([
                'nopemesanan' => $nopemesanan,
                'noperencanaan' => $request->noperencanaan,
                'kdobat'  => $request->kdobat,
                'harga'  => $request->harga,
                'stok_real_gudang'  => $request->stok_real_gudang,
                'stok_real_rs'  => $request->stok_real_rs,
                'stok_max_rs'  => $request->stok_max_rs,
                'jumlah_bisa_dibeli'  => $request->jumlah_bisa_dibeli,
                'tgl_stok'  => $request->tgl_stok,
                'jumlahdpesan'  => $request->jumlahdpesan,
                'user'  => auth()->user()->pegawai_id,
            ]);

            if (!$simpanrinci) {
                return new JsonResponse(['message' => 'not ok'], 500);
            }

            return new JsonResponse(
                [
                    'message' => 'ok',
                    'notrans' => $nopemesanan,
                    'heder' => $simpanheder,
                    'rinci' => $simpanrinci
                ],
                200
            );
        } else {

            $simpanrinci = PemesananRinci::create([
                'nopemesanan' => $request->nopemesanan,
                'noperencanaan' => $request->noperencanaan,
                'kdobat'  => $request->kdobat,
                'stok_real_gudang'  => $request->stok_real_gudang,
                'stok_real_rs'  => $request->stok_real_rs,
                'stok_max_rs'  => $request->stok_max_rs,
                'jumlah_bisa_dibeli'  => $request->jumlah_bisa_dibeli,
                'tgl_stok'  => $request->tgl_stok,
                'jumlahdpesan'  => $request->jumlahdpesan,
                'user'  => auth()->user()->pegawai_id,
            ]);

            if (!$simpanrinci) {
                return new JsonResponse(['message' => 'not ok'], 500);
            }

            return new JsonResponse(
                [
                    'message' => 'ok',
                    'notrans' => $request->nopemesanan,
                    //    'heder' => $simpanheder,
                    'rinci' => $simpanrinci
                ],
                200
            );
        }
    }

    public function listpemesanan()
    {
        // $listpemesanan = RencanabeliH::select('nopemesanan', 'tglpemesanan', 'kodepbf')
        //     ->with('pihakketiga')
        //     ->where('nopemesanan', '!=', '')
        //     ->orderBy('tglpemesanan')->paginate(request('per_page'));
        $gud = request('gudang');
        $gud[] = '';
        // return new JsonResponse($gud);
        $listpemesanan = PemesananHeder::select('nopemesanan', 'tgl_pemesanan', 'kdpbf', 'flag', 'kd_ruang')
            ->with(
                'gudang:kode,nama',
                'pihakketiga',
                'rinci',
                'rinci.masterobat:kd_obat,nama_obat,merk,satuan_b,satuan_k,kandungan,bentuk_sediaan,kekuatan_dosis,volumesediaan,kelas_terapi'
            )
            ->whereIn('kd_ruang', $gud)
            ->orderBy('tgl_pemesanan', 'desc')
            ->paginate(request('per_page'));
        return new JsonResponse($listpemesanan);
    }

    public function kuncipemesanan(Request $request)
    {
        $kuncipemesanan = PemesananHeder::where('nopemesanan', $request->nopemesanan)->update(['flag' => '1']);
        if (!$kuncipemesanan) {
            return new JsonResponse(['message' => 'not ok'], 500);
        }
        return new JsonResponse(['message' => 'ok'], 200);
    }
    public function batal(Request $request)
    {
        $data = PemesananHeder::where('nopemesanan', $request->nopemesanan)->first();
        if (!$data) {
            return new JsonResponse(['message' => 'Data tidak ditemukan, gagal batal'], 410);
        }
        $data->delete();
        $rinci = PemesananRinci::where('nopemesanan', $request->nopemesanan)->get();
        if (count($rinci) > 0) {
            foreach ($rinci as $key) {
                $rencana = RencanabeliR::where('no_rencbeliobat', $key['noperencanaan'])->where('kdobat', $key['kdobat'])->first();
                $rencana->flag = '';
                $rencana->save();
                $key->delete();
            }
        }
        return new JsonResponse(
            [
                'message' => 'Data berhasil dihapus',
                'data' => $data,
                'rinci' => $rinci,
                'req' => $request->all()
            ]
        );
    }
    public function batalRinci(Request $request)
    {
        $data = PemesananRinci::find($request->id);
        if (!$data) {
            return new JsonResponse(['message' => 'Data tidak ditemukan, gagal batal'], 410);
        }
        $rencana = RencanabeliR::where('no_rencbeliobat', $data->noperencanaan)->where('kdobat', $data->kdobat)->first();
        $rencana->flag = '';
        $rencana->save();
        $data->delete();
        $rinci = PemesananRinci::where('nopemesanan', $request->nopemesanan)->get();
        if (count($rinci) === 0) {
            $head = PemesananHeder::where('nopemesanan', $request->nopemesanan)->first();
            if ($head) {
                $head->delete();
            }
        }
        return new JsonResponse(
            [
                'message' => 'Data berhasil dihapus',
                'data' => $data,
                'rinci' => $rinci,
                'req' => $request->all()
            ]
        );
    }
}
