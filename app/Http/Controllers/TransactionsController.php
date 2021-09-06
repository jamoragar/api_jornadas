<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transactions;
use Transbank\Webpay\Webpay;
use Transbank\Webpay\Configuration;
use Illuminate\Support\Facades\DB;

class TransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $last_id = DB::select(
            'SELECT id FROM transactions ORDER BY id DESC LIMIT 1'
        );
        $last_id = array_map(function($value){
            return (array)$value;
        }, $last_id);
        return $last_id[0]['id'];
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
        $obj = new Class{};
        // Cambiar a url de producción al momento de hacer el release.
        // $url_retorno = 'http://localhost:8000/api/pago';
        // $url_final = 'http://localhost:8000/api/pagoFinal';
        $url_retorno = 'https://appjornadasmagallanicas.cl/api/api/pago';
        $url_final = 'https://appjornadasmagallanicas.cl/api/api/pagoFinal';

        if($request->sessionID && $request->monto && $request->cantidad && $request->nombre && $request->rut){
            DB::table('transactions')->insert(
            ['sessionID' => $request->sessionID,
            'monto' => $request->monto,
            'cantidad' => $request->cantidad,
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'rut' => $request->rut,
            'telefono' => $request->telefono]
            );
            
            $ultimo_id = $this->index();
            
            //Inicializamos configuración de Transbank
            $configuration = new Configuration();
            //Reemplazar por número de comercio oficial antes de salir a producción
            $configuration->setCommerceCode(597035794077);
            //Cambiar a Producción
            $configuration->setEnvironment("PRODUCCION");
$configuration->setPrivateKey("-----BEGIN RSA PRIVATE KEY-----
MIIEowIBAAKCAQEAxqFxCWM4Buk4Xm7P6iNwXn2fml5wsA5zRiEN7Y+ofv5xCwew
gNwsY8FG+KuCNhk5urCY6U4xTNazF/tAhJMSy96sbc3rFUrnZ/bZW1dW+kF3SkDn
nR3rbzibiMbhngYDYmHA305E/gV60l4xk6HRMxZF4to82ckZvi0XqcEpmp4C9i1/
6XfMSOeZ3fhoV+6sJ7nfBf/yvl69zcfswPzbdsP/yKuPIqCfrNX16NyqtaXBNpal
MT4yVhkUqtUK7oV73vtAGgPW/MiYQchNV6U4c5He2ltprBUdw5ul+/5FRXyCT9/4
yPuuibvJhwVOCXH7mhJ7VF6835C79eUfK3ZG2wIDAQABAoIBAFhu5B6j1/vrtbcF
avf5UzwefgKCbXVU2amtacAMp53ytm2MvN6CKBk5odlwsoZXcMZXfZzRvU1SgWX5
7N2ZhVLTDr3MYg+Wy7QTyO3L0uOUZgHfJ79h5MId19FrimOKldMlZnOFK15VHjuu
yC/MaZFvJyo6s8DaseVjuhdP8q/tQ/sNcfQNGhwxCOU/bS1sELhvM8GKE3s27NoE
TC7fkbKwNwgG34UgmW93VtYhglU9dpytEI1mwQpTi5g190fGo7Pw3RzvgkX/52AT
PYbul6zEH8oeDvV+rmoMGAWLJeYpkDdEbQxHTOroGcer99aA0+SnP8K1PzVXdUzf
KAZJ5YECgYEA+BOiTwfDvNG2OrgWL9ltPsVf/UAUr+znFnEGyf4o3Sb3DE2FiivO
UGXDXZEJR1AwE/SrUj/J65xekSL98rkhn+dvsdotrYX1LvoJE7JOEEbbjWDh6fgA
x/2s+063eNU2F6/VJrDmjuFUfIxqh0ha5D2IMl0qoQlam6fk8xsBkmECgYEAzPmE
tQS8hFIEbE5c5CvPnhdxD3pO9O5mTHnJEMO+JEy7BSG1vLQj3YjMdIiI0zPEXGXF
hBbsk+zE3hPj8Zs8fL/Ts5wUAZVE5W3EVcFhWMBts7P/Kn8xzEzHtfINZ9zPlOzB
EN9rLvmWMvVBPGfoe1EAYDdmoO3cDKYMw2qCmrsCgYBpiqDhEqKZ8Hag4LS/wrcR
n5NJATxL5HQkPg3vXewwumUcnNaVhDGQTtshMZVK+7iYpN1GbtEPJkWtZb+4xj6h
8yq39eS9EGLRi5rVAGTp09uQeIlkqxhZ/Xjcqg6wn7UVur7qaRSN8RuqqWqhB41z
0SHim3SJcptT4cgsDW9LwQKBgCuqJ9wMA0DI3Apacy1kK9lIsxwR+QnyUzaNZwi8
OiAvfFOuh7GISm+h+bQFdehQCc+JGpd17rXgZVvNruxEHpGQp7+GSzi/HKsnRADk
riEi9PuoJ35dFDWqUYzv4G00u7/E46f9gC7EmnuGhXwhwoOqkMLual1z7kF/ig6C
/QmLAoGBAO7MYTQOR/j+q5DZx1stiz0Q7SmLlm3TTX6uUJ061c/LYMJg9xgDOOOy
rExpHujBHuzKH5aQMUbCe2d9umcbGPolt2qwSeV52kFVjaAYlxmjN0ofRnD5SYWJ
cl0xy3CdX2e5M6LJ80Vofs5eCqkPGTfB2a23K6tvTaQV9044kA3n
-----END RSA PRIVATE KEY-----");
            $configuration->setPublicCert("-----BEGIN CERTIFICATE-----
MIIDxDCCAqwCCQDjhNtdCaIp4DANBgkqhkiG9w0BAQsFADCBozELMAkGA1UEBhMC
Q0wxEzARBgNVBAgMCk1BR0FMTEFORVMxFTATBgNVBAcMDFBVTlRBIEFSRU5BUzEk
MCIGA1UECgwbQ0xVQiBERSBMRU9ORVMgQ1JVWiBERUwgU1VSMRUwEwYDVQQDDAw1
OTcwMzU3OTQwNzcxKzApBgkqhkiG9w0BCQEWHEpBVklFUi5NT1JBR0FST0pBU0BH
TUFJTC5DT00wHhcNMjAwOTA3MTMxNDExWhcNMjQwOTA2MTMxNDExWjCBozELMAkG
A1UEBhMCQ0wxEzARBgNVBAgMCk1BR0FMTEFORVMxFTATBgNVBAcMDFBVTlRBIEFS
RU5BUzEkMCIGA1UECgwbQ0xVQiBERSBMRU9ORVMgQ1JVWiBERUwgU1VSMRUwEwYD
VQQDDAw1OTcwMzU3OTQwNzcxKzApBgkqhkiG9w0BCQEWHEpBVklFUi5NT1JBR0FS
T0pBU0BHTUFJTC5DT00wggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQDG
oXEJYzgG6Thebs/qI3BefZ+aXnCwDnNGIQ3tj6h+/nELB7CA3CxjwUb4q4I2GTm6
sJjpTjFM1rMX+0CEkxLL3qxtzesVSudn9tlbV1b6QXdKQOedHetvOJuIxuGeBgNi
YcDfTkT+BXrSXjGTodEzFkXi2jzZyRm+LRepwSmangL2LX/pd8xI55nd+GhX7qwn
ud8F//K+Xr3Nx+zA/Nt2w//Iq48ioJ+s1fXo3Kq1pcE2lqUxPjJWGRSq1QruhXve
+0AaA9b8yJhByE1XpThzkd7aW2msFR3Dm6X7/kVFfIJP3/jI+66Ju8mHBU4Jcfua
EntUXrzfkLv15R8rdkbbAgMBAAEwDQYJKoZIhvcNAQELBQADggEBALKoIttUwpx0
+0eVlucxOhpqOcmgOstPbKVSVsRqQnEIUHPjPtT8dGNXX6LowhLwsnCjKnokgmn/
DC52Z+TfPYubouoMZ7Jjgta702sZM8dGOL/j11GMwXXF26N5lWqkDwWZ3g/Yv1au
VkFkD2Suw4FM7uRY12WmxKWJE0Z0YZvLB1EWMVhZ1nuDQIxdC49hw+S4Nh0red51
r7tcmkiupIbC4GnelbVGI7E3AMd+fMn8ie+qumzchFlVB0FWr/Vf5cTbB54E96oj
3CKEddpzJlQ1gvI+xAOBE2I3uWIhM60GS4RRXXtLkhGu8Shts/uRt+xyuuZrXReW
CgqfFDXi06c=
-----END CERTIFICATE-----");
            //INTEGRACION
            // $tb_transaction = (new Webpay(Configuration::forTestingWebpayPlusNormal()))
            //    ->getNormalTransaction();
            $tb_transaction = (new Webpay($configuration))->getNormalTransaction();

            $tb_transaction->sessionID = $request->sessionID;
            $tb_transaction->monto = $request->monto;
            $tb_transaction->cantidad = $request->cantidad;
            $tb_transaction->orden_compra = "JMAGALLANICA-$ultimo_id";
            $tb_transaction->nombre = $request->nombre;
            $tb_transaction->apellido = $request->apellido;
            $tb_transaction->rut = $request->rut;
            
            //monto, id de sesion, numero de orden de compra, url de retorno, url final
            $initResult = $tb_transaction->initTransaction(
                $tb_transaction->monto, "JMAGALLANICA-$ultimo_id", $tb_transaction->sessionID, $url_retorno, $url_final
            );
            
            //Setiamos modelo para escribir en BD
            $transaction = new Transactions;
            $formAction = $initResult->url;
            $tokenWs = $initResult->token;
            //Setiamos hora
            date_default_timezone_set("America/Santiago");
            DB::table('transactions')
                ->where('id', $ultimo_id)
                ->update(
                    ['orden_compra'=> "JMAGALLANICA-$ultimo_id",
                    'created_at' => date_create(),
                    'updated_at' => date_create(),
                    'token_ws'=> $tokenWs,
                    'uid'=> $request->uid ? $request->uid : 'plataforma_web']);
            
            $obj->token_ws = $tokenWs;
            $obj->url = strval($formAction);
            
            return json_encode($obj);
        }else{
            $obj->code = 400;
            $obj->error = 'ERROR: Faltan datos para iniciar la transacción';

            return json_encode($obj);
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Transactions::where("id", $id)->get();
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
