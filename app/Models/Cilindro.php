<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cilindro extends Model
{
    use HasFactory;

    protected $table = 'cilindros';

    protected $fillable =[
        'id',
        'numero_serie',
        'fecha_recepcion',
        'fecha_registro',
        'url_ficha',
        'QR_code',
        'estado',
    ];

}
