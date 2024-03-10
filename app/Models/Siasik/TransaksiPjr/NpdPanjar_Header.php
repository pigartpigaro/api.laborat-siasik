<?php

namespace App\Models\Siasik\TransaksiPjr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NpdPanjar_Header extends Model
{
    use HasFactory;
    protected $connection = 'siasik';
    protected $guarded = ['id'];
    protected $table = 'npdpanjar_heder';
    protected $timestamp = false;
    public function npdpjr_rinci()
    {
        return $this->hasMany(NpdPanjar_Rinci::class, 'nonpdpanjar', 'nonpdpanjar');
    }
}
