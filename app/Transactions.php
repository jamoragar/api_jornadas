<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{   
    protected $fillable = ['sessionID', 'monto', 'cantidad', 'orden_compra', 'nombre', 'apellido', 'email', 'token_ws', 'uid', 'telefono'];
}
