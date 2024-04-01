<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReciboFijo extends Model
{
    use HasFactory;

    protected $table = 'recibo_fijo';
    public $timestamps = false; // Asumiendo que no deseas usar los campos created_at y updated_at

    protected $fillable = [
        'prestamo_fijo_id',
        'fecha',
        'cantidad',
        'comprobante',
        'estado',
        'observacion'
    ];

    /**
     * Relación con el modelo Prestamo.
     */
    public function prestamo()
    {
        return $this->belongsTo(PrestamoFijo::class);
    }
}
