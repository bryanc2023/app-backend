<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pregunta extends Model
{
    use HasFactory;
    protected $table = 'pregunta';

   
    public $timestamps = false; // Desactivar las marcas de tiempo

    protected $fillable = [
        'id_oferta',
        'pregunta',
    ];
    public function oferta()
    {
        return $this->belongsTo(Oferta::class, 'id_oferta');
    }
}
