<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class pagoExitoso extends Model
{
    protected $fillable = ['token_ws', 'authorizationCode', 'amount', 'responseCode', 'buy_order', 'transactionDate', 'sessionId', 'paymentType', 'uid'];
}
