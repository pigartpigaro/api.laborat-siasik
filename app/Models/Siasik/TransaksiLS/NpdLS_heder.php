<?php

namespace App\Models\Siasik\TransaksiLS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NpdLS_heder extends Model
{
    use HasFactory;
    protected $connection = 'siasik';
    protected $guarded = ['id'];
    protected $table = 'npdls_heder';
    public function npdlsrinci()
    {
        return $this->hasMany(NpdLS_rinci::class, 'nonpdls', 'nonpdls');
    }


}
