<?php

namespace App\Models\Simrs\Rajal;

use App\Models\Pegawai\Mpegawaisimpeg;
use App\Models\Satset\Satset;
use App\Models\Simrs\Anamnesis\Anamnesis;
use App\Models\Simrs\Edukasi\Transedukasi;
use App\Models\Simrs\Ews\ProcedureM;
use App\Models\Simrs\Generalconsent\Generalconsent;
use App\Models\Simrs\Master\Dokter;
use App\Models\Simrs\Master\Mpasien;
use App\Models\Simrs\Master\Mpoli;
use App\Models\Simrs\Master\Msistembayar;
use App\Models\Simrs\Pelayanan\Diagnosa\Diagnosa;
use App\Models\Simrs\Pelayanan\Diagnosa\Diagnosakeperawatan;
use App\Models\Simrs\Pemeriksaanfisik\Pemeriksaanfisik;
use App\Models\Simrs\Pemeriksaanfisik\Simpangambarpemeriksaanfisik;
use App\Models\Simrs\PemeriksaanRMkhusus\Polimata;
use App\Models\Simrs\Pendaftaran\Mgeneralconsent;
use App\Models\Simrs\Pendaftaran\Rajalumum\Antrianambil;
use App\Models\Simrs\Pendaftaran\Rajalumum\Seprajal;
use App\Models\Simrs\Pendaftaran\Rajalumum\Taskidantrian;
use App\Models\Simrs\Penunjang\DietTrans;
use App\Models\Simrs\Penunjang\Eeg\Eegtrans;
use App\Models\Simrs\Penunjang\Farmasi\Apotekrajal;
use App\Models\Simrs\Penunjang\Farmasi\Apotekrajallalu;
use App\Models\Simrs\Penunjang\Farmasi\Apotekrajalracikanheder;
use App\Models\Simrs\Penunjang\Farmasi\Apotekrajalracikanhedlalu;
use App\Models\Simrs\Penunjang\Farmasi\Apotekrajalracikanrinci;
use App\Models\Simrs\Penunjang\Farmasi\Apotekrajalracikanrincilalu;
use App\Models\Simrs\Penunjang\Farmasinew\Depo\Resepkeluarheder;
use App\Models\Simrs\Penunjang\Fisioterapi\Fisioterapipermintaan;
use App\Models\Simrs\Penunjang\Kamaroperasi\Kamaroperasi;
use App\Models\Simrs\Penunjang\Kamaroperasi\PermintaanOperasi;
use App\Models\Simrs\Penunjang\Laborat\LaboratMeta;
use App\Models\Simrs\Penunjang\Laborat\Laboratpemeriksaan;
use App\Models\Simrs\Penunjang\Lain\Lain;
use App\Models\Simrs\Penunjang\Radiologi\PembacaanradiologiController;
use App\Models\Simrs\Penunjang\Radiologi\Transpermintaanradiologi;
use App\Models\Simrs\Penunjang\Radiologi\Transradiologi;
use App\Models\Simrs\Rekom\Rekomdpjp;
use App\Models\Simrs\Sharing\SharingTrans;
use App\Models\Simrs\Tindakan\Tindakan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

class KunjunganPoli extends Model
{
    use HasFactory;
    protected $table = 'rs17';
    protected $guarded = [''];
    public $timestamps = false;
    protected $primaryKey = 'rs1';
    protected $keyType = 'string';

    public function masterpasien()
    {
        return $this->hasOne(Mpasien::class, 'rs1', 'rs2');
    }

    // public function relrekomdpjp()
    // {
    //     return $this->hasMany(Rekomdpjp::class, 'rs1', 'noreg');
    // }

    public function relmpoli()
    {
        return $this->belongsTo(Mpoli::class, 'rs8', 'rs1');
    }

    public function msistembayar()
    {
        return $this->belongsTo(Msistembayar::class, 'rs14', 'rs1');
    }

    public function dokter()
    {
        return $this->hasOne(Dokter::class, 'rs1', 'rs9');
    }

    public function seprajal()
    {
        return $this->hasOne(Seprajal::class, 'rs1', 'rs1');
    }

    public function generalconsent()
    {
        return $this->hasOne(Mgeneralconsent::class, 'noreg', 'rs1');
    }

    public function taskid()
    {
        return $this->hasMany(Taskidantrian::class, 'noreg', 'rs1');
    }

    public function anamnesis()
    {
        return $this->hasMany(Anamnesis::class, 'rs1', 'rs1');
    }
    public function pemeriksaanfisik()
    {
        return $this->hasMany(Pemeriksaanfisik::class, 'rs1', 'rs1');
    }
    public function gambars()
    {
        return $this->hasMany(Simpangambarpemeriksaanfisik::class, 'noreg', 'rs1');
    }
    public function diagnosa()
    {
        return $this->hasMany(Diagnosa::class, 'rs1', 'rs1');
    }
    public function diagnosakeperawatan()
    {
        return $this->hasMany(Diagnosakeperawatan::class, 'noreg', 'rs1');
    }
    public function tindakan()
    {
        return $this->hasMany(Tindakan::class, 'rs1', 'rs1');
    }
    public function laborats()
    {
        return $this->hasMany(LaboratMeta::class, 'noreg', 'rs1');
    }
    public function radiologi()
    {
        return $this->hasMany(Transpermintaanradiologi::class, 'rs1', 'rs1');
    }
    public function penunjanglain()
    {
        return $this->hasMany(Lain::class, 'rs1', 'rs1');
    }
    public function ok()
    {
        return $this->hasMany(PermintaanOperasi::class, 'rs1', 'rs1');
    }

    public function hasilradiologi()
    {
        return $this->hasMany(PembacaanradiologiController::class, 'rs1', 'rs1');
    }

    public function planning()
    {
        return $this->hasMany(WaktupulangPoli::class, 'rs1', 'rs1');
    }

    public function edukasi()
    {
        return $this->hasMany(Transedukasi::class, 'rs1', 'rs1');
    }

    public function datasimpeg()
    {
        return  $this->hasOne(Mpegawaisimpeg::class, 'kdpegsimrs', 'rs9');
    }

    public function laborat()
    {
        return $this->hasMany(Laboratpemeriksaan::class, 'rs1', 'rs1');
    }

    public function transradiologi()
    {
        return $this->hasMany(Transradiologi::class, 'rs1', 'rs1');
    }

    public function apotekrajalpolilalu()
    {
        return $this->hasMany(Apotekrajallalu::class, 'rs1', 'rs1');
    }

    public function apotekrajal()
    {
        return $this->hasMany(Apotekrajal::class, 'rs1', 'rs1');
    }

    public function apotekracikanrajallalu()
    {
        return $this->hasManyThrough(
            Apotekrajalracikanrincilalu::class,
            Apotekrajalracikanhedlalu::class,
            'rs1',
            'rs1'
        );
    }

    public function apotekracikanrajal()
    {
        return $this->hasManyThrough(
            Apotekrajalracikanrinci::class,
            Apotekrajalracikanheder::class,
            'rs1',
            'rs1'
        );
    }

    public function kamaroperasi()
    {
        return $this->hasMany(Kamaroperasi::class, 'rs1', 'rs1');
    }

    public function tindakanoperasi()
    {
        return $this->hasMany(Tindakan::class, 'rs1', 'rs1');
    }

    public function antrian_ambil()
    {
        return $this->hasMany(Antrianambil::class, 'noreg', 'rs1');
    }

    public function usg()
    {
        return $this->hasMany(Tindakan::class, 'rs1', 'rs1');
    }

    public function ecg()
    {
        return $this->hasMany(Tindakan::class, 'rs1', 'rs1');
    }

    public function eeg()
    {
        return $this->hasOne(Eegtrans::class, 'rs1', 'rs1');
    }

    public function pembacaanradiologi()
    {
        return $this->hasMany(PembacaanradiologiController::class, 'rs1', 'rs1');
    }

    public function fisio()
    {
        return $this->hasMany(Fisioterapipermintaan::class, 'rs1', 'rs1');
    }

    public function diet()
    {
        return $this->hasMany(DietTrans::class, 'noreg', 'rs1');
    }
    public function sharing()
    {
        return $this->hasMany(SharingTrans::class, 'noreg', 'rs1');
    }
    public function prosedur()
    {
        return $this->hasMany(ProcedureM::class, 'noreg', 'rs1');
    }

    public function newapotekrajal()
    {
        return $this->hasMany(Resepkeluarheder::class, 'noreg', 'rs1');
    }

    public function satset()
    {
        return $this->hasOne(Satset::class, 'uuid', 'rs1');
    }
    public function generalcons()
    {
        return $this->hasOne(Generalconsent::class, 'norm', 'norm');
    }
}
