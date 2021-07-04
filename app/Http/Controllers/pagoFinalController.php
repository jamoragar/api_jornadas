<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\pagoFinal;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Http;
use Transbank\Webpay\Webpay;
use Transbank\Webpay\Configuration;
use App\pagoExitoso;
use App\Transactions;
use Illuminate\Support\Facades\DB;




class pagoFinalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Setiamos el modelo e PagoFinal
        $pago = new pagoFinal;

        if($request->token_ws){
            /**  Tomamos el parametro que viene por post de Transbank */
            $pago->token_ws = $request->token_ws;

            //Obtenemos la respuesta de transbank con el token del pago

            $db_transaction = pagoExitoso::where('token_ws', $pago->token_ws)->first();

            if (!$db_transaction){
                $obj->error = 'No hay respuesta de la base de datos';
                $obj->code = 401;

                return json_encode($obj);
            };

            $response = $db_transaction;
            //Cambiar a producción
            // $url_return = 'http://localhost:3000/pago-exito/?';
            $url_return = 'https://appjornadasmagallanicas.cl/pago-exito/?';
            $url_return .= $response->token_ws. '?';
            $url_return .= $response->buy_order. '?';
            $url_return .= $response->transactionDate. '?';
            $url_return .= $response->authorizationCode. '?';
            $url_return .= $response->amount. '?';
            $url_return .= $response->uid;
            
            //DB::table('aux_company')->insert(
            //  ['object' => $url_re]
            //    );

            //TODO Crear funcion que genere el bono de sorteo

            return Redirect::to($url_return);

        }else{
            $pago->token_ws = $request->TBK_TOKEN;
            $pago->buy_order = $request->TBK_ORDEN_COMPRA;
            $obj = new Class{};

            $db_transaction = Transactions::where('token_ws', $pago->token_ws)->first();

            if (!$db_transaction){
                $obj->token_ws = $pago->token_ws;
                $obj->error = 'No hay respuesta de la base de datos';
                $obj->code = 401;

                return json_encode($obj);
            };

            $response = $db_transaction;
            //Cambiar a producción
            // $url_return = 'http://localhost:3000/pago-fallido/?';

            $url_return = 'https://appjornadasmagallanicas.cl/pago-fallido/?';
            //Si el 42 es la respuesta a la vida al unierso y todo lo de más... entonces el -42 es la interrogamnte, la pregunta y la duda a la existencia entera?
            $url_return .= '-42'. '?';
            $url_return .= $response->orden_compra. '?';
            $url_return .= $response->monto. '?';
            $url_return .= $response->token_ws. '?';
            $url_return .= $response->created_at. '?';
            $url_return .= $response->sessionID. '?';
            $url_return .= $response->uid;

            return Redirect::to($url_return);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($token_ws)
    {
        return pagoExitoso::where('token_ws', $token_ws)->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
