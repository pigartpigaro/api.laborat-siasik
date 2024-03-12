<?php

namespace App\Http\Controllers\Api\Siasik\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Siasik\TransaksiLS\NpdLS_rinci;
use App\Models\Siasik\TransaksiLS\NpkLS_heder;
use App\Models\Siasik\TransaksiPjr\GeserKas_Header;
use App\Models\Siasik\TransaksiPjr\Nihil;
use App\Models\Siasik\TransaksiPjr\NpkPanjar_Header;
use App\Models\Siasik\TransaksiPjr\SpmUP;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BKUController extends Controller
{
    public function bkuppk()
    {
        // BKU PPK utk transaksi Keluar
        // relasi bertahap dengan select
        $awal=request('tglmulai');
        $akhir=request('tglakhir');
        $keluar = NpkLS_heder::select('nonpk', 'tglnpk')
        ->with(['npklsrinci'=> function($q)
            {
                $q  ->with(['npdlsrinci:nonpdls,koderek50,rincianbelanja,nominalpembayaran'])
                    ->select('nonpk', 'kegiatanblud', 'nonpdls');
            }])

            ->whereBetween('tglnpk', [$awal, $akhir])
            ->paginate(request('per_page'));

        return new JsonResponse($keluar);
    }

    public function bkubpl()
    {
        // return new JsonResponse('wew');
        // $masuk = NpkLS_heder::select('id');
        // $spm = SpmUP::select('id');
        $awal=request('tglmulai');
        $akhir=request('tglakhir');
        $ls = NpkLS_heder::select('nonpk','tglnpk','nopencairan','tglpencairan','nonpdls')
        ->with(['npklsrinci'=> function($npk)
            {
                $npk->with(['npdlsrinci'=> function($npd){
                    $npd->with(['cp'=> function($cp){
                        $cp->select('contrapost.nonpd',
                        'contrapost.nocontrapost',
                        'contrapost.tglcontrapost',
                        'contrapost.koderek50',
                        'contrapost.rincianbelanja',
                        'contrapost.nominalcontrapost');
                    }])
                    ->select(
                    'npdls_rinci.nonpdls',
                    'npdls_rinci.koderek50',
                    'npdls_rinci.rincianbelanja',
                    'npdls_rinci.nominalpembayaran');

                }])->select( 'nonpk',
                'nonpdls',
                'kegiatanblud',
                'nopencairan',
                'total');
            }])
            // ->whereMonth('tglnpk', request('bln'))
            // ->whereYear('tglnpk', request('thn'))
            // ->where('nonpk','00002/I/NPK-LS/2022')
            // ->get();
        ->whereBetween('tglnpk', [$awal, $akhir])
        ->paginate(request('per_page'));
        return new JsonResponse($ls);
    }
    public function spm()
    {
        $awal=request('tglmulai');
        $akhir=request('tglakhir');
        $spm = SpmUP::select('noSpm','tglSpm','jumlahspp')
        ->whereBetween('tglnpk', [$awal, $akhir])
        ->paginate(request('per_page'));
        return new JsonResponse($spm);
    }
    public function panjar()
    {
        $awal=request('tglmulai');
        $akhir=request('tglakhir');
        $kas = GeserKas_Header::select('notrans','tgltrans','jenis','tglverif')
        ->with(['kasrinci'=> function($kasrinci)
        {
            $kasrinci->with(['npkrinci'=>function($npkrinci){
            $npkrinci->with(['npkhead'=>function($npkhead){
                // CATATAN di NPK PANJAR HEADER hanya 1 nomer NPD yg relasi sharusnya mncul banyak
                $npkhead->select(
                    'npkpanjar_heder.nonpk',
                    'npkpanjar_heder.tglnpk',
                    'npkpanjar_heder.nonpdpanjar');
                },'npdpjr_head'=>function($npdpjr_head){
                    $npdpjr_head->with(['npdpjr_rinci'=>function($npdpjr_rinci){
                    $npdpjr_rinci->select(
                        'npdpanjar_rinci.nonpdpanjar',
                        'npdpanjar_rinci.koderek50',
                        'npdpanjar_rinci.rincianbelanja50',
                        'npdpanjar_rinci.totalpermintaanpanjar');
                    }, 'nota_head'=>function($notahead){
                        $notahead->with(['nota_rinci'=>function($notarinci){
                        $notarinci->with(['spj_head'=>function($spjhead){
                            $spjhead->with(['spj_rinci'=>function($spjrinci){
                                $spjrinci->select(
                                    'spjpanjar_rinci.nospjpanjar',
                                    'spjpanjar_rinci.nonpdpanjar',
                                    'spjpanjar_rinci.koderek50',
                                    'spjpanjar_rinci.rincianbelanja50',
                                    'spjpanjar_rinci.jumlahbelanjapanjar');
                            }])->select(
                                'spjpanjar_heder.notapanjar',
                                'spjpanjar_heder.nospjpanjar',
                                'spjpanjar_heder.tglspjpanjar',
                            );
                        }])->select(
                            'notapanjar_rinci.nonotapanjar',
                            'notapanjar_rinci.total');
                        }])->select(
                            'notapanjar_heder.nonpd',
                            'notapanjar_heder.nonotapanjar',
                            'notapanjar_heder.tglnotapanjar');
                    }])->select(
                        'npdpanjar_heder.nonpdpanjar',
                        'npdpanjar_heder.tglnpdpanjar',
                        'npdpanjar_heder.kodepptk',
                        'npdpanjar_heder.pptk',
                        'npdpanjar_heder.kegiatanblud',
                        'npdpanjar_heder.bidang');
                }])->select(
                    'npkpanjar_rinci.nonpk',
                    'npkpanjar_rinci.nonpd',
                    'npkpanjar_rinci.kegiatanblud',
                    'npkpanjar_rinci.nonpd',
                    'npkpanjar_rinci.total');
            },'sisapjr_head'=>function($sisahead){
                $sisahead->with(['sisarinci'=>function($sisarinci){
                $sisarinci->select(
                    'pengembaliansisapanjar_rinci.nopengembaliansisapanjar',
                    'pengembaliansisapanjar_rinci.koderek50',
                    'pengembaliansisapanjar_rinci.rincianbelanja50',
                    'pengembaliansisapanjar_rinci.sisapanjar');
            }])->select(
                    'pengembaliansisapanjar_heder.nonpdpanjar',
                    'pengembaliansisapanjar_heder.nopengembaliansisapanjar',
                    'pengembaliansisapanjar_heder.tglpengembaliansisapanjar',
                    'pengembaliansisapanjar_heder.kegiatanblud',);
            },'cppjr_head'=>function($cphead){
                $cphead->with(['cppjr_rinci'=>function($cprinci){
                    $cprinci->select(
                        'pengembalianpanjar_rinci.nopengembalianpanjar',
                        'pengembalianpanjar_rinci.koderek50',
                        'pengembalianpanjar_rinci.rincianbelanja50',
                        'pengembalianpanjar_rinci.sisapanjar');
                }])->select(
                    'pengembalianpanjar_heder.nopengembalianpanjar',
                    'pengembalianpanjar_heder.tglpengembalianpanjar',
                    'pengembalianpanjar_heder.kegiatanblud');
            }])
            ->select('notrans','nonpk','keterangan','jumlah','nonpd');
        }])
        // $panjar = NpkPanjar_Header::select('nonpk','tglnpk')
        // ->with('npkrinci:nonpk,nonpd,kegiatanblud,total','')
        ->whereBetween('tgltrans', [$awal, $akhir])
        // ->where('notrans', '137/01/2024/T-GS')
        ->paginate(request('per_page'));
        return new JsonResponse($kas);
    }

    public function nihil()
    {
        $awal=request('tglmulai');
        $akhir=request('tglakhir');
        $nihil = Nihil::select(
            'nopengembalian',
            'tgltrans',
            'jmlup',
            'jmlspj',
            'jmlcp',
            'jmlpengembalianup',
            'jmlsisaup',
            'jmlpengembalianreal',)
        // ->whereBetween('tgltrans', [$awal, $akhir])
        // ->get();
        ->paginate(request('per_page'));
        return new JsonResponse($nihil);
    }
    // coba appends
    public function kode(){
        // $kode = Akun_permendagri50::with('npdls_rinci')
        // ->where('kode1', '5')
        // ->where('kode3', '02')
        // ->limit(100)->get();
        // return ($kode);
        $npd=NpdLS_rinci::with('cp')
        ->where('nonpdls','00004/I/UMUM/NPD-LS/2022')
        ->limit(50)
        ->get();
        return ($npd);
    }

    public function coba(){
        $npd = NpkLS_heder::where('id')
        ->limit(50)
        ->get();
        return($npd);
    }

}
