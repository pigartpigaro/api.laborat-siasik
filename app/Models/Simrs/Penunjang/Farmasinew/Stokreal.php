<?php

namespace App\Models\Simrs\Penunjang\Farmasinew;

use App\Http\Controllers\Api\Simrs\Penunjang\Farmasinew\MinmaxobatController;
use App\Models\Sigarang\Gudang;
use App\Models\Sigarang\Ruang;
use App\Models\Simrs\Penunjang\Farmasinew\Depo\Permintaanresep;
use App\Models\Simrs\Penunjang\Farmasinew\Depo\Permintaanresepracikan;
use App\Models\Simrs\Penunjang\Farmasinew\Depo\Resepkeluarrinci;
use App\Models\Simrs\Penunjang\Farmasinew\Depo\Resepkeluarrinciracikan;
use App\Models\Simrs\Penunjang\Farmasinew\Obatoperasi\PersiapanOperasiRinci;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stokreal extends Model
{
    use HasFactory;
    protected $table = 'stokreal';
    protected $guarded = ['id'];
    protected $connection = 'farmasi';

    public function obat()
    {
        return $this->belongsTo(Mobatnew::class, 'kdobat', 'kd_obat');
    }
    public function minmax()
    {
        return $this->hasOne(Mminmaxobat::class, 'kd_obat', 'kdobat');
    }

    public function gudangdepo()
    {
        return $this->hasOne(Gudang::class, 'kode', 'kdruang');
    }
    public function ruang()
    {
        return $this->hasOne(Ruang::class, 'kode', 'kdruang');
    }

    public function transnonracikan()
    {
        // return $this->hasMany(Resepkeluarrinci::class, 'kdobat', 'kdobat'); diganti ke permintaan
        return $this->hasMany(Permintaanresep::class, 'kdobat', 'kdobat');
    }

    public function transracikan()
    {
        // return $this->hasMany(Resepkeluarrinciracikan::class, 'kdobat', 'kdobat');
        return $this->hasMany(Permintaanresepracikan::class, 'kdobat', 'kdobat');
    }
    public function persiapanrinci()
    {
        return $this->hasMany(PersiapanOperasiRinci::class, 'kd_obat', 'kdobat');
    }
}
