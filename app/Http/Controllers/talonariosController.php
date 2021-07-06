<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Talonario;

class talonariosController extends Controller
{
    public function generaTalonarios(Request $request){
        $talonario = new Talonario;

        if($request->has('talonario_numero', 'correlativo')){
            $talonario->talonario_numero = $request->talonario_numero;
            $talonario->correlativo = $request->correlativo;

            $talonario->save();
            return 'Talonario exitosamente generado';
        }else{
            return 'Faltan datos para generar el Talonario';
        }
        
    }
}
