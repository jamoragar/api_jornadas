<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class pagoManual extends Model
{
    protected $table = 'bonos_digitales_manuales';
    protected $fillable = ['uid', 'nombre_vendedor', 'apellido_vendedor', 'tipo_pago', 'cod_boucher', 'cant_bonos', 'monto_recaudado', 'orden_compra'];
}
