<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    use HasFactory;

    protected $table = 'prestamo';
    public $timestamps = false;

    protected $fillable = [
        'persona_id',
        'cantidad',
        'interes',
        'tipo_pago_id',
        'fecha',
        'estado',
        'amortizacion',
        'comprobante',
        'administrador',
        'pago_especifico',
        'primer_pago',
    ];

    // Relación con Persona
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    // Relación con TipoPago
    public function tipoPago()
    {
        return $this->belongsTo(TipoPago::class);
    }

    // Relación con cargos
    public function cargos()
    {
        return $this->hasMany(Cargo::class);
    }

     // Relación con TipoPago
     public function recibos()
     {
         return $this->hasMany(Recibo::class);
     }

     // Relación con TipoPago
     public function administradorUser()
     {
         return $this->belongsTo(User::class,'administrador');
     }
}
