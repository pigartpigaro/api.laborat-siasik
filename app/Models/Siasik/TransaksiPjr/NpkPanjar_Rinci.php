<?php

namespace App\Models\Siasik\TransaksiPjr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NpkPanjar_Rinci extends Model
{
    use HasFactory;
    protected $connection = 'siasik';
    protected $guarded = ['id'];
    protected $table = 'npkpanjar_rinci';
    protected $timestamp = false;
    public function npkhead()
    {
        return $this->belongsTo(NpkPanjar_Header::class, 'nonpk', 'nonpk');
    }
    public function npdpjr_head()
    {
        return $this->belongsTo(NpdPanjar_Header::class, 'nonpd', 'nonpdpanjar');
    }
}
