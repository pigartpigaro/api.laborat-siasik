<?php

namespace App\Models\Simrs\Penunjang\Farmasinew\Depo;

use App\Models\Simrs\Penunjang\Farmasinew\Mobatnew;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permintaanresepracikan extends Model
{
    use HasFactory;
    protected $table = 'resep_permintaan_keluar_racikan';
    protected $guarded = ['id'];
    protected $connection = 'farmasi';

    public function mobat()
    {
        return $this->hasone(Mobatnew::class, 'kd_obat', 'kdobat');
    }
}
