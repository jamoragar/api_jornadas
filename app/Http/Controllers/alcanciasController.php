<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Alcancia;

class alcanciasController extends Controller
{
    public function generaAlcancias(Request $request){
        $alcancia = new Alcancia;

        if($request->has('numero', 'codigo_barra')){
            $alcancia->numero = $request->numero;
            $alcancia->codigo_barra = $request->codigo_barra;

            $alcancia->save();
            return 'Alcancia exitosamente generada';
        }else{
            return 'Faltan datos para generar la alancia';
        }
        
    }
}
