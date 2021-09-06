<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Http;
use App\pagoExitoso;
use Transbank\Webpay\Webpay;
use Transbank\Webpay\Configuration;
use App\Transactions;
use Mail;
use App\Mail\EnviarMail;
use App\Mail\EnviarBono;
use Illuminate\Support\Facades\DB;

class pagoExitosoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bono = DB::table('bonos_digitales')->orderBy('bono_digital', 'desc')->first();
        
        DB::table('bonos_digitales')->insert(
        ['bono_digital' => $bono->bono_digital + 12]
        );
        
        return $bono->bono_digital + 12;
        
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
        //Inicializamos transbank y vamos a buscar la informaciion de la transacción



        $configuration = new Configuration();
        $configuration->setCommerceCode(597035794077);
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

        $transaction = (new Webpay($configuration))->getNormalTransaction();

        // Setiamos el modelo
        $pago = new pagoExitoso;
        /**  Tomamos el parametro que viene por post de Transbank */
        $pago->token_ws = $request->token_ws;

        //Obtenemos la respuesta de transbank con el token del pago
        $result = $transaction->getTransactionResult($pago->token_ws);
        $output = $result->detailOutput;

        $db_result = Transactions::where('token_ws', $pago->token_ws)->first();

        //Si el pago es exitoso...
        if($output->responseCode == 0){
            $pago->authorizationCode = $output->authorizationCode;
            $pago->amount = $output->amount;
            $pago->responseCode = $output->responseCode;
            $pago->buy_order = $result->buyOrder;
            $pago->transactionDate = $result->transactionDate;
            $pago->sessionId = $result->sessionId;
            $pago->paymentType = $output->paymentTypeCode;
            $pago->uid = $db_result->uid;

            
            /** Guardamos en BD */
            $pago->save();
            
            //Despues de guardar en BD comienza la logica para generar bonos o comprobante de aporte
            if($pago->sessionId == 'DonacionApp' || $pago->sessionId == 'DonacionSitioWeb'){
                date_default_timezone_set("America/Santiago");
                $imgPath = public_path('img/aporte/aporte.jpg');
    
                $font = public_path('font/arial.ttf');
    
                $our_image = imagecreatefromjpeg($imgPath);
    
                imagettftext($our_image, 30, 0, 160, 300, 0x222222, $font, date("d"));
                imagettftext($our_image, 30, 0, 300, 300, 0x222222, $font, date("m"));
                imagettftext($our_image, 30, 0, 440, 300, 0x222222, $font, date("Y"));
                imagettftext($our_image, 30, 0, 250, 450, 0x222222, $font, $db_result->nombre.' '.$db_result->apellido);
                imagettftext($our_image, 30, 0, 260, 520, 0x222222, $font, '$'.number_format($pago->amount));
    
                if (!file_exists(public_path("img/aporte/".$db_result->nombre.$db_result->apellido))) {
                    mkdir(public_path("img/aporte/".$db_result->nombre.$db_result->apellido), 0777, true);
                    imageJpeg($our_image, public_path("img/aporte/".$db_result->nombre.$db_result->apellido).'/aporte.jpg', 85);
                	Mail::to($db_result->email)->send(new EnviarMail(public_path("img/aporte/".$db_result->nombre.$db_result->apellido).'/aporte.jpg'));
                    imagedestroy($our_image);
                }else{
                    imageJpeg($our_image, public_path("img/aporte/".$db_result->nombre.$db_result->apellido).'/aporte.jpg', 85);
                	Mail::to($db_result->email)->send(new EnviarMail(public_path("img/aporte/".$db_result->nombre.$db_result->apellido).'/aporte.jpg'));
                    imagedestroy($our_image);
                }                
            }
            if($pago->sessionId == 'BonoSorteoApp' || $pago->sessionId == 'BonoSorteoSitioWeb'){
                $bono = DB::table('bonos_digitales')->orderBy('bono_digital', 'desc')->first();
                
                
                date_default_timezone_set("America/Santiago");
                $imgPath = public_path('img/bono/bono.jpg');
                
                $font = public_path('font/arial.ttf');
                
                $data_para_enviar = [
                    "to" => $db_result->email,
                    "attachments" => []
                    ];
                
                for($i = 1; $i <= $db_result->cantidad ; $i++){
                    $our_image = imagecreatefromjpeg($imgPath);
                    
                    imagettftext($our_image, 50, 0, 100, 150, 0x222222, $font, $bono->bono_digital + $i);
                    imagettftext($our_image, 50, 0, 700, 150, 0x222222, $font, $bono->bono_digital + $i);
                    imagettftext($our_image, 24, 90, 200, 720, 0x222222, $font, $db_result->nombre.' '.$db_result->apellido);
                    imagettftext($our_image, 24, 90, 295, 720, 0x222222, $font, $db_result->email);
                    imagettftext($our_image, 28, 90, 390, 720, 0x222222, $font, $db_result->telefono);
                    
                    if (!file_exists(public_path("img/bono/".$db_result->nombre.$db_result->apellido))) {
                        
                        mkdir(public_path("img/bono/".$db_result->nombre.$db_result->apellido), 0777, true);
                        imageJpeg($our_image, public_path("img/bono/".$db_result->nombre.$db_result->apellido).'/aporte'.$i.'_'.$pago->buy_order.'.jpg', 85);
                        imagedestroy($our_image);
                    }else{
                        imageJpeg($our_image, public_path("img/bono/".$db_result->nombre.$db_result->apellido).'/aporte'.$i.'_'.$pago->buy_order.'.jpg', 85);
                        imagedestroy($our_image);
                    }
                    
                    DB::table('bonos_digitales_vendidos')->insert(
                    ['nombre' => $db_result->nombre,
                    'apellido' => $db_result->apellido,
                    'correlativo' => $bono->bono_digital + $i,
                    'orden_compra'=> $pago->buy_order,
                    'telefono' => $db_result->telefono,
                    'email' => $db_result->email]
                    );
                    
                    $data_para_enviar["attachments"][$i - 1] = [
                            "path" => public_path("img/bono/".$db_result->nombre.$db_result->apellido).'/aporte'.$i.'_'.$pago->buy_order.'.jpg',
                            "as" => "bono_sorteo_".$i.'.jpg',
                            "mime" => "image/jpeg",
                        ];
                }
                //Guardamos en BD
                DB::table('bonos_digitales')->insert(
                ['bono_digital' => $bono->bono_digital + $db_result->cantidad]
                );
                //Enviamos el email con los archivos adjuntos
            	Mail::to($db_result->email)->send(new EnviarBono($data_para_enviar));
            }

            //Retornamos a el boucher de transbank
            // $url_return = 'http://localhost:3000/procesa-pago/?';
            $url_return = 'https://appjornadasmagallanicas.cl/procesa-exito/?';

            // $url_return .= $result->urlRedirection. '?';
            $url_return .= $pago->token_ws;
            // $url_return .= $pago->authorizationCode. '?';
            // $url_return .= $pago->amount. '?';
            // $url_return .= $pago->responseCode. '?';
            // $url_return .= $pago->buy_order. '?';
            // $url_return .= $pago->transactionDate. '?';
            // $url_return .= $pago->sessionId. '?';
            // $url_return .= $pago->paymentType;

            return Redirect::to($url_return);

        }else{
            $pago->authorizationCode = $output->authorizationCode;
            $pago->amount = $output->amount;
            $pago->responseCode = $output->responseCode;
            $pago->buy_order = $output->buyOrder;
            $pago->transactionDate = $result->transactionDate;
            $pago->sessionId = $result->sessionId;
            $pago->paymentType = $output->paymentTypeCode;
            $pago->uid = $db_result->uid;


            /** Guardamos en BD */
            $pago->save();
            
            //Retornamos a la página de Error
            //  $url_return = 'http://localhost:3000/pago-fallido/?';
            $url_return = 'https://appjornadasmagallanicas.cl/pago-fallido/?';
            
            //$url_return .= $pago->responseCode. '?';
            $url_return .= $pago->responseCode;
            //$url_return .= $pago->buy_order. '?';
            // $url_return .= $pago->amount. '?';
            // $url_return .= $pago->token_ws. '?';
            // $url_return .= $pago->transactionDate. '?';
            // $url_return .= $pago->sessionId. '?';
            // $url_return .= $pago->uid. '?';
            // $url_return .= $pago->paymentType;

            return Redirect::to($url_return);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $token_ws
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
