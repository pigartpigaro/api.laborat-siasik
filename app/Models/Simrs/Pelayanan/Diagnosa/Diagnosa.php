<?php

namespace App\Models\Simrs\Pelayanan\Diagnosa;

use App\Models\Simrs\Master\Diagnosa_m;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnosa extends Model
{
    use HasFactory;
    protected $table = 'rs101';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function masterdiagnosa()
    {
        return $this->hasOne(Diagnosa_m::class, 'rs1', 'rs3');
    }
}
