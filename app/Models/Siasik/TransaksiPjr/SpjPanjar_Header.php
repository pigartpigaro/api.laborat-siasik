<?php

namespace App\Models\Siasik\TransaksiPjr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpjPanjar_Header extends Model
{
    use HasFactory;
    protected $connection = 'siasik';
    protected $guarded = ['id'];
    protected $table = 'spjpanjar_heder';
    protected $timestamp = false;
}
