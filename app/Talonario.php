<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Talonario extends Model
{
    public $timestamps = false;
    protected $fillable = ['talonario_numero', 'correlativo'];
}
