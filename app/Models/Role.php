<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $table = 'roles';

    protected $primaryKey = 'id';

    public $timestamps = false;


    protected $fillable = [
        'name',

    ];

    protected $guarded = [];

    public function user_has_role()
    {
        return $this->belongsToMany(User::class, 'model_has_roles', 'role_id', 'model_id');
    }

   /* public function role_has_permissions()
    {
        return $this->belongsToMany('App\Models\Permission', 'role_has_permissions', 'role_id');
    }*/
}
