<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaTrabajo extends Model
{
    use HasFactory;

    protected $table = 'area_trabajo';

    protected $fillable = ['nombre_area', 'vigencia'];
  
   

    public function ofertas()
    {
        return $this->hasMany(Oferta::class, 'id_area');
    }

    public function postulantes()
{
    return $this->belongsToMany(Postulante::class, 'postulante_area', 'id_area', 'id_postulante');
               
}

}
