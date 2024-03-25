<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recibo extends Model
{
    use HasFactory;
    protected $table = 'recibo';
    public $timestamps = false; // Asumiendo que no deseas usar los campos created_at y updated_at

    protected $fillable = [
        'prestamo_id',
        'fecha',
        'cantidad',
        'comprobante',
        'interes',
        'remanente',
        'estado',
    ];

    /**
     * Relación con el modelo Prestamo.
     */
    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class);
    }
}
