<?php

namespace App\Models\Siasik\TransaksiPendapatan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendapatanLainRinci extends Model
{
    use HasFactory;
    // protected $connection = 'rs_coba';
    protected $guarded = ['id'];
    protected $table = 'rs260';
    protected $timestamp = false;
    // public function cppjr_rinci()
    // {
    //     return $this->hasMany(CpPanjar_Rinci::class, 'nopengembalianpanjar', 'nopengembalianpanjar');
    // }
}
