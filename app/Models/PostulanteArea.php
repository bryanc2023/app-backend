<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostulanteArea extends Model
{
    use HasFactory;
    protected $table = 'postulante_area';

    // No uses la columna 'id' como clave primaria
    protected $primaryKey = ['id_postulante', 'id_area'];

    // Indica que la clave primaria no es incremental (ya que es compuesta)
    public $incrementing = false;

    // Establece el tipo de clave
    protected $keyType = 'int';

    public $timestamps = false; // Si no tienes columnas de timestamps en esta tabla

    protected $fillable = ['id_postulante', 'id_area', 'fecha_creacion'];

    // Relación con el modelo Postulante
    public function postulante()
    {
        return $this->belongsTo(Postulante::class, 'id_postulante');
    }

    // Relación con el modelo AreaTrabajo
    public function area()
    {
        return $this->belongsTo(AreaTrabajo::class, 'id_area');
    }
}
