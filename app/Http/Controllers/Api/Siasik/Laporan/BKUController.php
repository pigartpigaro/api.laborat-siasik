<?php

namespace App\Http\Controllers\Api\Siasik\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Siasik\Master\Akun_Kepmendg50;
use App\Models\Siasik\TransaksiLS\Contrapost;
use App\Models\Siasik\TransaksiLS\NpdLS_heder;
use App\Models\Siasik\TransaksiLS\NpkLS_heder;
use App\Models\Siasik\TransaksiLS\NpkLS_rinci;
use App\Models\Siasik\TransaksiPendapatan\DataSTS;
use App\Models\Siasik\TransaksiPjr\CpPanjar_Header;
use App\Models\Siasik\TransaksiPjr\CpSisaPanjar_Header;
use App\Models\Siasik\TransaksiPjr\GeserKas_Header;
use App\Models\Siasik\TransaksiPjr\Nihil;
use App\Models\Siasik\TransaksiPjr\NpkPanjar_Header;
use App\Models\Siasik\TransaksiPjr\SpjPanjar_Header;
use App\Models\Siasik\TransaksiPjr\SPM_GU;
use App\Models\Siasik\TransaksiPjr\SpmUP;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToArray;

class BKUController extends Controller
{
    public function bkuppk()
    {

        $awal=request('tahun').'-'. request('bulan').'-01';
        $akhir=request('tahun').'-'. request('bulan').'-31';
        $sts = DataSTS::with(['tbp', 'pendpatanlain'=>function($rinci){
            $rinci -> with('plainlain');
         }])
        ->whereBetween('tgl', [$awal, $akhir])
        ->get();

        // BKU PPK utk transaksi Keluar
        // relasi bertahap dengan select
        // $awal=request('tglmulai');
        // $akhir=request('tglakhir');
        $npkls = NpkLS_heder::with(['npklsrinci'=> function($npk)
            {
                $npk->with(['npdlshead'=> function ($npdrinci){
                    $npdrinci->with(['npdlsrinci']);
                }]);
            }])
        ->whereBetween('tglnpk', [$awal, $akhir])
        ->get();

        // $npklsa = NpkLS_heder::whereBetween('tglnpk', [$awal, $akhir])->pluck('nonpk');
        // $npkls = [];
        // if(count($npklsa)>0){
        //     $npkls = NpkLS_rinci::with(['npdlshead'=> function ($npdrinci){
        //         $npdrinci->with(['npdlsrinci']);
        //     },
        //     'header:nonpk,tglnpk'
        //     ])
        //     ->whereIn('nonpk', $npklsa)->get();
        // }


        $nihil = Nihil::select(
            'nopengembalian',
            'tgltrans',
            'jmlup',
            'jmlspj',
            'jmlcp',
            'jmlpengembalianup',
            'jmlsisaup',
            'jmlpengembalianreal',)
        ->whereBetween('tgltrans', [$awal, $akhir])
        ->get();


        $spm = SpmUP::orderBy('tglSpm','desc')
        ->whereBetween('tglSpm', [$awal, $akhir])
        ->get();
        $spmgu = SPM_GU::orderBy('tglSpm','desc')
        ->whereBetween('tglSpm', [$awal, $akhir])
        ->get();

        $ppk = [
            'sts' => $sts,
            'spm' => $spm,
            'spmgu' => $spmgu,
            'nihil' => $nihil,
            'npkls' => $npkls,
        ];

        return new JsonResponse($ppk);
    }
    public function bkupengeluaran()
    {
        $awal=request('tahun').'-'. request('bulan').'-01';
        $akhir=request('tahun').'-'. request('bulan').'-31';
        $npkls = NpkLS_heder::with(['npklsrinci'=> function($npk)
            {
                $npk->with(['npdlshead'=> function ($npdrinci){
                    $npdrinci->with(['npdlsrinci']);
                }]);
            }])
        ->whereBetween('tglnpk', [$awal, $akhir])
        ->get();

        $cp = Contrapost::orderBy('tglcontrapost','desc')
        ->whereBetween('tglcontrapost', [$awal. ' 00:00:00', $akhir. ' 23:59:59'])
        ->get();

        $spm = SpmUP::orderBy('tglSpm','desc')
        ->whereBetween('tglSpm', [$awal, $akhir])
        ->get();

        $spmgu = SPM_GU::orderBy('tglSpm','desc')
        ->whereBetween('tglSpm', [$awal, $akhir])
        ->get();

        $npkpanjar=NpkPanjar_Header::with(['npkrinci'=> function($npd){
            $npd->with(['npdpjr_rinci']);
        }])
        ->whereBetween('tglnpk', [$awal, $akhir])
        ->get();
        // $npkpanjar=NpkPanjar_Header::with(['npkrinci'=> function($npd){
        //     $npd->with(['npdpjr_head'=>function($npdrinci){
        //         $npdrinci->with(['npdpjr_rinci']);
        //     }]);
        // }])
        // ->whereBetween('tglnpk', [$awal, $akhir])
        // ->get();

        $spjpanjar=SpjPanjar_Header::with(['spj_rinci'])
        ->whereBetween('tglspjpanjar', [$awal, $akhir])
        ->get();

        $pengembalianpjr=CpPanjar_Header::with(['cppjr_rinci'])
        ->whereBetween('tglpengembalianpanjar', [$awal, $akhir])
        ->get();

        $cpsisapjr=CpSisaPanjar_Header::with(['sisarinci'])
        ->whereBetween('tglpengembaliansisapanjar', [$awal, $akhir])
        ->get();


        $pergeserankas = GeserKas_Header::with(['kasrinci'])
        // $cp = Contrapost::orderBy('tglcontrapost','desc')
        ->whereBetween('tgltrans', [$awal, $akhir])
        ->get();

        $nihil = Nihil::select(
            'nopengembalian',
            'tgltrans',
            'jmlup',
            'jmlspj',
            'jmlcp',
            'jmlpengembalianup',
            'jmlsisaup',
            'jmlpengembalianreal',)
        ->whereBetween('tgltrans', [$awal, $akhir])
        ->get();

        $bkupengeluaran = [
            'npkls' => $npkls,
            'cp' => $cp,
            'spm' => $spm,
            'spmgu' => $spmgu,
            'npkpanjar' => $npkpanjar,
            'spjpanjar' => $spjpanjar,
            'pengembalianpjr'=> $pengembalianpjr,
            'cpsisapjr' => $cpsisapjr,
            'pergeserankas' => $pergeserankas,
            'nihil' => $nihil,
        ];

        return new JsonResponse($bkupengeluaran);
    }

    public function bkuptk()
    {
        $awal=request('tahun').'-'. request('bulan').'-01';
        $akhir=request('tahun').'-'. request('bulan').'-31';

        // cari where ... relasi pertma kedua tidak tampil jika kosong
        $pencairanls = NpkLS_heder::when(request('ptk'),function($anu)
        {
            $anu->whereHas('npklsrinci.npdlshead',function($hed){
                $hed->where('kodepptk', request('ptk'));
            });
        })->with(['npklsrinci'=> function($npk)
                {
                    $npk->when(request('ptk'),function($anu){
                        $anu->whereHas('npdlshead',function($hed){
                            $hed->where('kodepptk', request('ptk'));
                        });
                    })->with(['npdlshead'=> function ($npdrinci){
                        $npdrinci->with(['npdlsrinci']);
                    }]);
                }])
            ->whereBetween('tglnpk', [$awal, $akhir])
            ->get();

        // $pencairanls = NpkLS_heder::when(request('ptk'),function($ada){
        //     $ada->with('npklsrinci.npdlshead.npdlsrinci')->whereHas('npklsrinci',function($npk)
        //     {
        //         $npk
        //         ->whereHas('npdlshead',function($hed){
        //             $hed->where('kodepptk', request('ptk'));
        //         })
        //         ->with('npdlshead', function ($npdrinci){
        //             $npdrinci
        //             ->with(['npdlsrinci']);
        //         });
        //     });
        // })
        // ->when(!request('ptk'), function($xx){
        //     $xx->with('npklsrinci.npdlshead');
        // })
        // ->whereBetween('tglnpk', [$awal, $akhir])
        // ->get();


        $npkpanjar = NpkPanjar_Header::when(request('ptk'),function($anu){
            $anu->whereHas('npkrinci.npdpjr_head',function($hed){
                $hed->where('kodepptk', request('ptk'));
            });
        })->with(['npkrinci'=> function($npk)
                {
                    $npk->when(request('ptk'),function($anu){
                        $anu->whereHas('npdpjr_head',function($hed){
                            $hed->where('kodepptk', request('ptk'));
                        });
                    })->with(['npdpjr_head'=> function ($npdrinci){
                        $npdrinci->with(['npdpjr_rinci']);
                    }]);
                }])
            ->whereBetween('tglnpk', [$awal, $akhir])
            ->get();

        $spjpanjar=SpjPanjar_Header::with(['spj_rinci'])
        ->whereBetween('tglspjpanjar', [$awal, $akhir])
        ->where('kodepptk', request('ptk'))
        ->get();

        $pengembalianpjr=CpPanjar_Header::with(['cppjr_rinci'])
        ->whereBetween('tglpengembalianpanjar', [$awal, $akhir])
        ->where('kodepptk', request('ptk'))
        ->get();

        $cpsisapjr=CpSisaPanjar_Header::with(['sisarinci'])
        ->whereBetween('tglpengembaliansisapanjar', [$awal, $akhir])
        ->where('kodepptk', request('ptk'))
        ->get();

        $bkuptk = [
            'pencairanls' => $pencairanls,
            'npkpanjar' => $npkpanjar,
            'spjpanjar' => $spjpanjar,
            'pengembalianpjr' => $pengembalianpjr,
            'cpsisapjr' => $cpsisapjr,
        ];
        return new JsonResponse($bkuptk);

    }

    public function bukubank()
    {
        $awal=request('tahun').'-'. request('bulan').'-01';
        $akhir=request('tahun').'-'. request('bulan').'-31';
        $pencairanls = NpkLS_heder::with(['npklsrinci'=> function($npk)
            {
                $npk->with(['npdlshead'=> function ($npdrinci){
                    $npdrinci->with(['npdlsrinci']);
                }]);
            }])
        ->whereBetween('tglnpk', [$awal, $akhir])
        ->get();

        $cp = Contrapost::orderBy('tglcontrapost','desc')
        ->whereBetween('tglcontrapost', [$awal. ' 00:00:00', $akhir. ' 23:59:59'])
        ->get();

        $spm = SpmUP::orderBy('tglSpm','desc')
        ->whereBetween('tglSpm', [$awal, $akhir])
        ->get();

        $bankkas='Bank Ke Kas';
        $bankkekas = GeserKas_Header::where('jenis', $bankkas)->with(['kasrinci'])
        // $cp = Contrapost::orderBy('tglcontrapost','desc')
        ->whereBetween('tgltrans', [$awal, $akhir])
        ->get();


        $kasbank='Kas Ke Bank';
        $kaskebank = GeserKas_Header::where('jenis', $kasbank)->with(['kasrinci'])
        // $cp = Contrapost::orderBy('tglcontrapost','desc')
        ->whereBetween('tgltrans', [$awal, $akhir])
        ->get();

        $spjpanjar=SpjPanjar_Header::with(['spj_rinci'])
        ->whereBetween('tglspjpanjar', [$awal, $akhir])
        ->get();

        $nihil = Nihil::select(
            'nopengembalian',
            'tgltrans',
            'jmlup',
            'jmlspj',
            'jmlcp',
            'jmlpengembalianup',
            'jmlsisaup',
            'jmlpengembalianreal',)
        ->whereBetween('tgltrans', [$awal, $akhir])
        ->get();

        $spmgu = SPM_GU::orderBy('tglSpm','desc')
        ->whereBetween('tglSpm', [$awal, $akhir])
        ->get();

        $bukubank = [
            'pencairanls' => $pencairanls,
            'cp' => $cp,
            'spm' => $spm,
            'bankkekas' => $bankkekas,
            'kaskebank'=> $kaskebank,
            'spjpanjar' => $spjpanjar,
            'nihil' => $nihil,
            'spmgu' => $spmgu,
        ];
        return new JsonResponse($bukubank);

    }
    public function bukutunai()
    {
        $awal=request('tahun').'-'. request('bulan').'-01';
        $akhir=request('tahun').'-'. request('bulan').'-31';
        // $pergeserankas = GeserKas_Header::with(['kasrinci'])
        // // $cp = Contrapost::orderBy('tglcontrapost','desc')
        // ->whereBetween('tgltrans', [$awal, $akhir])
        // ->get();

        $bankkas='Bank Ke Kas';
        $bankkekas = GeserKas_Header::where('jenis', $bankkas)->with(['kasrinci'])
        // $cp = Contrapost::orderBy('tglcontrapost','desc')
        ->whereBetween('tgltrans', [$awal, $akhir])
        ->get();


        $kasbank='Kas Ke Bank';
        $kaskebank = GeserKas_Header::where('jenis', $kasbank)->with(['kasrinci'])
        // $cp = Contrapost::orderBy('tglcontrapost','desc')
        ->whereBetween('tgltrans', [$awal, $akhir])
        ->get();

        $npkpanjar=NpkPanjar_Header::with(['npkrinci'=> function($npd){
            $npd->with(['npdpjr_rinci']);
        }])
        ->whereBetween('tglnpk', [$awal, $akhir])
        ->get();

        $pengembalianpjr=CpPanjar_Header::with(['cppjr_rinci'])
        ->whereBetween('tglpengembalianpanjar', [$awal, $akhir])
        ->get();

        $cpsisapjr=CpSisaPanjar_Header::with(['sisarinci'])
        ->whereBetween('tglpengembaliansisapanjar', [$awal, $akhir])
        ->get();

        $pjr = 'PANJAR';
        $cp = Contrapost::where('jenisbelanja', $pjr)
        ->orderBy('tglcontrapost','desc')
        ->whereBetween('tglcontrapost', [$awal. ' 00:00:00', $akhir. ' 23:59:59'])
        ->get();

        $bukutunai = [
            // 'pergeserankas' => $pergeserankas,
            'bankkekas' => $bankkekas,
            'kaskebank' => $kaskebank,
            'npkpanjar' => $npkpanjar,
            'pengembalianpjr' => $pengembalianpjr,
            'cpsisapjr' => $cpsisapjr,
            'cp' => $cp,
        ];
        return new JsonResponse($bukutunai);
    }
    public function bkubpl()
    {
        // return new JsonResponse('wew');
        // $masuk = NpkLS_heder::select('id');
        // $spm = SpmUP::select('id');
        $awal=request('tglmulai', '2024-01-01');
        $akhir=request('tglakhir', '2024-12-31');
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
                'kegiatan',
                'kegiatanblud',
                'nopencairan',
                'total');
            }])
            // ->whereMonth('tglnpk', request('bln'))
            // ->whereYear('tglnpk', request('thn'))
            // ->where('nonpk','00002/I/NPK-LS/2022')
            // ->get();
            // ->whereYear('tglnpk', '2024');
        ->whereBetween('tglnpk', [$awal, $akhir])
        ->get();
        // ->paginate(request('per_page'));
        return new JsonResponse($ls);
    }
    public function spm()
    {
        $awal=request('tglmulai', '2024-01-01');
        $akhir=request('tglakhir', '2024-12-31');
        // $spm = SpmUP::orderBy('tglSpm','desc')->get();
        // $gu = SPM_GU::orderBy('tglSpm','desc')->get();
        // $all = $spm->merge($gu)
        // ->whereBetween('tglSpm', [$awal, $akhir])
        // // ->whereYear('tglnpk', '2024')
        // ->groupBy(function($item){
        //     return $item->tglSpm;
        // });
        // ->whereBetween('tglSpm', [$awal, $akhir]);
        // ->paginate(request('per_page'));

        $awal=request('tglmulai', '2024-01-01');
        $akhir=request('tglakhir', '2024-12-31');
        $spm = SpmUP::orderBy('tglSpm','desc')
        ->whereBetween('tglSpm', [$awal, $akhir])
        ->get();
        $gu = SPM_GU::orderBy('tglSpm','desc')
        ->whereBetween('tglSpm', [$awal, $akhir])
        ->get();
        $dataspm = [
            'spm' => $spm,
            'gu' => $gu,
        ];
        return new JsonResponse($dataspm);

    }

    public function cp()
    {
        $awal=request('tglmulai', '2023-02-01');
        $akhir=request('tglakhir', '2023-02-31');
        $spjpanjar=SpjPanjar_Header::with(['spj_rinci'])
        ->whereBetween('tglspjpanjar', [$awal, $akhir])
        ->get();
        // $kas = GeserKas_Header::with(['kasrinci'])
        // $cp = Contrapost::orderBy('tglcontrapost','desc')
        // $npkpanjar=NpkPanjar_Header::with(['npkrinci'=> function($npd){
        //     $npd->with(['npdpjr_head'=>function($npdrinci){
        //         $npdrinci->with(['npdpjr_rinci']);
        //     }]);
        // }])
        // $spjpanjar=SpjPanjar_Header::with(['spj_rinci'])
        // ->whereBetween('tglspjpanjar', [$awal, $akhir])
        // ->get();
        // $cpsisapjr=CpSisaPanjar_Header::with(['sisarinci'])
        // ->whereBetween('tglpengembaliansisapanjar', [$awal, $akhir])
        // ->get();
        // $ptk=request('ptk');
        // $pencairan = NpkLS_heder::with(['npklsrinci'=>function ($head) use ($ptk){
        //     $head->whereHas(['npdlshead'=>function ($npd) use ($ptk){
        //         $npd->where('kodepptk', $ptk)
        //         ->with(['npdlsrinci']);
        //     }]);
        // }])

        $npklsa = NpkLS_heder::whereBetween('tglnpk', [$awal, $akhir])->pluck('nonpk');
        $npkls = [];
        if(count($npklsa)>0){
            $npkls = NpkLS_rinci::with(['npdlshead'=> function ($npdrinci){
                    $npdrinci->with(['npdlsrinci']);
                },
                'header:nonpk,tglnpk'
                ])
            ->whereIn('nonpk', $npklsa)->get();
        }

        // $ptk = request('ptk', 'YULIANA, S.A.P');
        // $pencairanls = NpkLS_heder::with(['npklsrinci'=> function($npk) use ($ptk)
        //     {
        //         $npk
        //         ->whereHas(['npdlshead'=> function ($npdrinci) use ($ptk){
        //             $npdrinci->where('kodepptk', $ptk)
        //             ->with(['npdlsrinci']);
        //         }]);
        //     }])
        // ->whereBetween('tglnpk', [$awal, $akhir])
        // ->get();
        // $spjpanjar=SpjPanjar_Header::with(['spj_rinci'])
        // ->whereBetween('tglspjpanjar', [$awal, $akhir])
        // ->where('kodepptk', request('ptk'))
        // ->get();
        return new JsonResponse($spjpanjar);
    }
    public function panjar()
    {
        $awal=request('tglmulai', '2023-01-01');
        $akhir=request('tglakhir', '2023-12-31');
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
        ->get();
        // ->where('notrans', '137/01/2024/T-GS')
        // ->paginate(request('per_page'));
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
        ->whereBetween('tgltrans', [$awal, $akhir])
        // ->get();
        ->paginate(request('per_page'));
        return new JsonResponse($nihil);
    }
    // coba appends
    public function kode(){
        $awal=request('tglmulai', '2024-04-01');
        $akhir=request('tglakhir', '2024-04-31');


        $kode = Akun_Kepmendg50::with(['npdls_rinci' => function ($head){
            $head->whereHas('headerls',function ($cair){
                $cair->where('nopencairan', '!=', '');
            })->with('headerls');
        },
        'spjpanjar','cp'])
        ->where('kode1', '5')
        // ->where('kode3', '02')
        ->limit(100)->get();

        $pencairanls = NpkLS_heder::when(request('ptk'),function($anu)
        {
            $anu->whereHas('npklsrinci.npdlshead',function($hed){
                $hed->where('kodepptk', request('ptk'));
            });
        })->with(['npklsrinci'=> function($npk)
                {
                    $npk->when(request('ptk'),function($anu){
                        $anu->whereHas('npdlshead',function($hed){
                            $hed->where('kodepptk', request('ptk'));
                        });
                    })->with(['npdlshead'=> function ($npdrinci){
                        $npdrinci->with(['npdlsrinci']);
                    }]);
                }])
            ->whereBetween('tglnpk', [$awal, $akhir])
            ->get();



        // return ($kode);
        // $npd=NpdLS_rinci::with('cp')
        // ->where('nonpdls','00004/I/UMUM/NPD-LS/2022')
        // ->limit(50)
        // ->get();
        // return ($npd);


        return new JsonResponse($kode);
    }

    public function coba(){
        $npd = NpkLS_heder::where('id')
        ->limit(50)
        ->get();
        return($npd);
    }



    // CETAK
    public function cetak()
    {
        $data=NpkLS_heder::with(['npklsrinci'])
        ->get();

        return view('bku.cetak.bku')
        ->with('npklsrinci', $data);

    }

}
