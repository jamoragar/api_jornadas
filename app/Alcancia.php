<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Alcancia extends Model
{
    public $timestamps = false;
    protected $fillable = ['numero', 'codigo_barra'];

}
