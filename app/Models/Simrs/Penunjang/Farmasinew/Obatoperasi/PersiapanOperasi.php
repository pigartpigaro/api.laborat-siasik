<?php

namespace App\Models\Simrs\Penunjang\Farmasinew\Obatoperasi;

use App\Models\Sigarang\Pegawai;
use App\Models\Simrs\Master\Mpasien;
use App\Models\Simrs\Penunjang\Farmasinew\Mobatnew;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersiapanOperasi extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $connection = 'farmasi';

    public function rinci()
    {
        return $this->hasMany(PersiapanOperasiRinci::class, 'nopermintaan', 'nopermintaan');
    }
    public function distribusi()
    {
        return $this->hasMany(PersiapanOperasiDistribusi::class, 'kd_obat', 'kd_obat');
    }
    public function pasien()
    {
        return $this->belongsTo(Mpasien::class, 'norm', 'rs1');
    }

    public function userminta()
    {
        return $this->belongsTo(Pegawai::class, 'user_minta', 'kdpegsimrs');
    }
    public function userdist()
    {
        return $this->belongsTo(Pegawai::class, 'user_distribusi', 'kdpegsimrs');
    }
    public function dokter()
    {
        return $this->belongsTo(Pegawai::class, 'dokter', 'kdpegsimrs');
    }
}
