<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestamoFijo extends Model
{
    use HasFactory;
    protected $table = 'prestamo_fijo';
    public $timestamps = false; // Poner true si quieres usar los campos timestamps (created_at, updated_at)

    protected $fillable = [
        'persona_id',
        'cantidad',
        'fecha',
        'estado',
        'comprobante',
    ];

    // Relación con Persona
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }


      public function recibos()
      {
          return $this->hasMany(ReciboFijo::class);
      }
}
