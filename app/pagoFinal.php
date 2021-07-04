<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class pagoFinal extends Model
{
    protected $fillable = ['token_ws', 'authorizationCode', 'amount' ,'responseCode', 'buy_order','transactionDate'];
}
