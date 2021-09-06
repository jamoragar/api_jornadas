<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Talonario;
use Illuminate\Support\Facades\DB;


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

    public function obtieneBonosVendidos(){
        $bonos_transbank = DB::select( 
            'SELECT p.token_ws, p.buy_order, p.created_at, p.updated_at, p.authorizationCode, p.amount, p.responseCode, p.transactionDate, p.sessionId, p.paymentType, p.uid, t.cantidad, t.nombre, t.apellido, t.email, t.telefono
            FROM desa_jornadas.pago_exitosos as p
            INNER JOIN desa_jornadas.transactions as t
            ON p.token_ws = t.token_ws
            WHERE p.sessionId = "BonoSorteoApp" || p.sessionId =  "BonoSorteoSitioWeb"'
        );

        $result = collect($bonos_transbank);

        return $result;
    }

    public function obtieneBonosManuales(){
        $bonos_manuales = DB::select('SELECT * FROM desa_jornadas.bonos_digitales_manuales');
        $result = collect($bonos_manuales);

        return $result;
    }
}
