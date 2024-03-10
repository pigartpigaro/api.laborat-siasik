<?php

namespace App\Models\Pegawai\Akses;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aplikasi extends Model
{
    use HasFactory;
    protected $connection = 'kepex';
    protected $guarded = ['id'];

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }
    public function access()
    {
        return $this->hasMany(Access::class);
    }
}
