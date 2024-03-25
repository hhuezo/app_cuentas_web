<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    use HasFactory;

    protected $table = 'cargo'; // Opcional si el nombre del modelo es el singular de la tabla
    public $timestamps = false; // Cambiar a false si no deseas usar los timestamps por defecto de Laravel

    protected $fillable = [
        'prestamo_id',
        'fecha',
        'cantidad',
        'comprobante',
        'observacion',
    ];

    // Relación con el modelo Prestamo (asumiendo que ya tienes este modelo)
    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class);
    }
}
