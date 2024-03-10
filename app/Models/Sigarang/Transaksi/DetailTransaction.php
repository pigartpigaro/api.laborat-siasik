<?php

namespace App\Models\Sigarang\Transaksi;

use App\Models\Barang108;
use App\Models\BarangRS;
use App\Models\Satuan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTransaction extends Model
{
    use HasFactory;
    protected $connection = 'sigarang';
    protected $guarded = ['id'];
    public function barang108()
    {
        return $this->belongsTo(Barang108::class, 'kode_108', 'kode');
        // return $this->belongsTo(Barang108::class, 'kode', 'kode_108');
    }

    public function barangrs()
    {
        return  $this->belongsTo(BarangRS::class, 'kode_rs', 'kode')->withTrashed();
        // return $this->belongsTo(BarangRS::class, 'kode', 'kode_rs');
    }

    public function satuan()
    {
        return  $this->belongsTo(Satuan::class, 'kode_satuan', 'kode');
        // return $this->belongsTo(BarangRS::class, 'kode', 'kode_rs');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
