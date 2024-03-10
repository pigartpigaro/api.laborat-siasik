<?php

namespace App\Models\Simrs\Penunjang\Farmasinew;

use App\Models\Simrs\Penunjang\Farmasinew\Penerimaan\PenerimaanRinci;
use App\Models\Simrs\Penunjang\Farmasinew\Stok\Stokrel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RencanabeliR extends Model
{
    use HasFactory;
    protected $table = 'perencana_pebelian_r';
    protected $guarded = ['id'];
    protected $connection = 'farmasi';

    public function rincian()
    {
        return $this->hasOne(RencanabeliH::class, 'no_rencbeliobat', 'no_rencbeliobat');
    }

    public function mobat()
    {
        return $this->hasOne(Mobatnew::class, 'kd_obat', 'kdobat');
    }
    public function stok()
    {
        return $this->hasMany(Stokrel::class, 'kdobat', 'kdobat');
    }
    public function minmax()
    {
        return $this->hasMany(Mminmaxobat::class, 'kd_obat', 'kdobat');
    }
    public function penerimaan()
    {
        return $this->hasMany(PenerimaanRinci::class, 'kdobat', 'kdobat');
    }
}
