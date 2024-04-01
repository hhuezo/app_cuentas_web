<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CargoFijo extends Model
{
    use HasFactory;

    protected $table = 'cargo_fijo';
    public $timestamps = false;

    protected $fillable = [
        'prestamo_fijo_id',
        'fecha',
        'cantidad',
        'comprobante',
        'observacion',
    ];


    public function prestamo()
    {
        return $this->belongsTo(PrestamoFijo::class);
    }
}
