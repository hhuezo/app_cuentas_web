<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Creadenciales extends Model
{
    use HasFactory;
    protected $table = 'credenciales';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'usuario',
        'password',
        'sitio_web',
        'notas',
        'logo'
    ];
}
