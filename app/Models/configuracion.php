<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class configuracion extends Model
{
    use HasFactory;
    
    protected $table = 'configuracion';

    protected $fillable = [
        'id',
        'dias_max_edicion',
        'dias_max_eliminacion',
        'valor_prioridad_alta',
        'valor_prioridad_media',
        'valor_prioridad_baja',
        'vigencia',
        'terminos_condiciones',
        'gratis_ofer',
        'gratis_d',
        'estandar_ofer',
        'estandar_d',
        'premium_ofer',
        'premiun_d',
        'u_ofer',
        'u_d',
        
    ];

}
