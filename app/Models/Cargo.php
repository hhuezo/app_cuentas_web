<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    use HasFactory;

    protected $table = 'cargo';
    public $timestamps = false;

    protected $fillable = [
        'prestamo_id',
        'fecha',
        'cantidad',
        'comprobante',
        'observacion',
        'saldo',
    ];

    // Relación con el modelo Prestamo (asumiendo que ya tienes este modelo)
    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class);
    }
}
