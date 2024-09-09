<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recibo extends Model
{
    use HasFactory;
    protected $table = 'recibo';
    public $timestamps = false;

    protected $fillable = [
        'prestamo_id',
        'fecha',
        'cantidad',
        'comprobante',
        'interes',
        'remanente',
        'estado',
        'saldo',
    ];

    /**
     * Relación con el modelo Prestamo.
     */
    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class);
    }
}
