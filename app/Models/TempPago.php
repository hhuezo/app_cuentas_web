<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempPago extends Model
{
    use HasFactory;

    protected $table = 'temp_pago';
    public $timestamps = false; // Poner true si quieres usar los campos timestamps (created_at, updated_at)

    protected $fillable = [
        'prestamo_id',
        'cantidad',
        'fecha',
        'pagado',
    ];

    // Relación con Prestamo
    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class);
    }
}
