<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\pagoManual;
use App\pagoExitoso;
use Illuminate\Support\Facades\DB;
use ZipArchive;
use File;
use Mail;
use App\Mail\EnviarMail;
use App\Mail\EnviarBono;
use RecursiveIteratorIterator;
use RecursiveArrayIterator;

class pagoManualBonoController extends Controller
{
    public function generarVentaManual(Request $request){
        $pago_manual = new pagoManual;
        date_default_timezone_set("America/Santiago");
        $imgPath = public_path('img/bono/bono.jpg');
        $font = public_path('font/arial.ttf');
        //Info de BD en tabla pago manual
        $pago_manual->uid = $request->uid;
        $pago_manual->nombre_vendedor = $request->nombre_vendedor;
        $pago_manual->apellido_vendedor = $request->apellido_vendedor;
        $pago_manual->tipo_pago = $request->tipo_pago;
        $pago_manual->cod_boucher = $request->cod_boucher ? $request->cod_boucher : null;
        $pago_manual->cant_bonos = $request->cant_bonos;
        $pago_manual->monto_recaudado = $request->monto_recaudado;
        $pago_manual->orden_compra = $request->orden_compra;
        //Guardamos en BD la venta
        $pago_manual->save();
        //Recolectamos data para generar bouchers
        $datos_bono_sorteo = new Class{};
        $datos_bono_sorteo->nombre_cliente = $request->nombre_cliente;
        $datos_bono_sorteo->apellido_cliente = $request->apellido_cliente;
        $datos_bono_sorteo->email = $request->email;
        $datos_bono_sorteo->telefono = $request->telefono;
        $datos_bono_sorteo->orden_compra = $pago_manual->orden_compra;
        //Correlativo
        $bono = DB::table('bonos_digitales')->orderBy('bono_digital', 'desc')->first();
        //email de destino bajo el controlador de correo
        $data_para_enviar = [
            "to" => $datos_bono_sorteo->email,
            "attachments" => []
        ];
        //generamos bonos de sorteo
        for($i = 1; $i <= $pago_manual->cant_bonos ; $i++){
            $our_image = imagecreatefromjpeg($imgPath);

            imagettftext($our_image, 50, 0, 100, 150, 0x222222, $font, $bono->bono_digital + $i);
            imagettftext($our_image, 50, 0, 700, 150, 0x222222, $font, $bono->bono_digital + $i);
            imagettftext($our_image, 24, 90, 200, 720, 0x222222, $font, $datos_bono_sorteo->nombre_cliente.' '.$datos_bono_sorteo->apellido_cliente);
            imagettftext($our_image, 24, 90, 295, 720, 0x222222, $font, $datos_bono_sorteo->email);
            imagettftext($our_image, 28, 90, 390, 720, 0x222222, $font, $datos_bono_sorteo->telefono);

            if (!file_exists(public_path("img/bono/".$datos_bono_sorteo->nombre_cliente.$datos_bono_sorteo->apellido_cliente))) {
                        
                mkdir(public_path("img/bono/".$datos_bono_sorteo->nombre_cliente.$datos_bono_sorteo->apellido_cliente), 0777, true);
                imageJpeg($our_image, public_path("img/bono/".$datos_bono_sorteo->nombre_cliente.$datos_bono_sorteo->apellido_cliente).'/aporte'.$i.'_'.$datos_bono_sorteo->orden_compra.'.jpg', 85);
                imagedestroy($our_image);
            }else{
                imageJpeg($our_image, public_path("img/bono/".$datos_bono_sorteo->nombre_cliente.$datos_bono_sorteo->apellido_cliente).'/aporte'.$i.'_'.$datos_bono_sorteo->orden_compra.'.jpg', 85);
                imagedestroy($our_image);
            }

            DB::table('bonos_digitales_vendidos')->insert(
                ['nombre' => $datos_bono_sorteo->nombre_cliente,
                'apellido' => $datos_bono_sorteo->apellido_cliente,
                'correlativo' => $bono->bono_digital + $i,
                'orden_compra'=> $datos_bono_sorteo->orden_compra,
                'telefono' => $datos_bono_sorteo->telefono,
                'email' => $datos_bono_sorteo->email]
            );
            $data_para_enviar["attachments"][$i - 1] = [
                "path" => public_path("img/bono/".$datos_bono_sorteo->nombre_cliente.$datos_bono_sorteo->apellido_cliente).'/aporte'.$i.'_'.$datos_bono_sorteo->orden_compra.'.jpg',
                "as" => "bono_sorteo_".$i.'.jpg',
                "mime" => "image/jpeg",
            ];
        }
        //Guardamos en BD
        DB::table('bonos_digitales')->insert(
            ['bono_digital' => $bono->bono_digital + $pago_manual->cant_bonos]
        );
        $pago = new pagoExitoso;
        $pago->token_ws = 'Pago plataforma manual';
        $pago->authorizationCode = 'N.A.';
        $pago->amount = $pago_manual->monto_recaudado;
        $pago->responseCode = 0;
        $pago->buy_order = $pago_manual->orden_compra;
        $pago->sessionId = 'BonoSorteoPagoManual';
        $pago->transactionDate = now();
        $pago->paymentType = $pago_manual->tipo_pago;
        $pago->uid = 'plataforma_web';

        $pago->save();


        return ('Venta y bonos realizados con éxito');
    }

    public function zipBonosSorteo(Request $request){
        $zip = new ZipArchive();
        $zip_nombre = 'BonosSorteo.zip';
        
        
        $datos_cliente = new Class{};
        $datos_cliente->nombre_cliente = $request->nombre_cliente;
        $datos_cliente->apellido_cliente = $request->apellido_cliente;
        $datos_cliente->orden_compra = strval($request->orden_compra);

        $bonos_path = public_path("img/bono/".$datos_cliente->nombre_cliente.$datos_cliente->apellido_cliente);

        if(File::isDirectory($bonos_path)){
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($bonos_path),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );
            $zip->open(public_path() . "/img/bono/".$datos_cliente->nombre_cliente.$datos_cliente->apellido_cliente. '/'. $zip_nombre, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
    
            foreach($files as $key => $file){
                if(!$file->isDir() && strpos($file, $datos_cliente->orden_compra) !== false){
                    
                    $filePath = $file->getRealPath();
                    //Extraemos el nombre del archivo
                    $relativePath = substr($filePath, strlen($bonos_path) + 1);
                    
                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();
            // $bonos = array_diff(scandir($bonos_path), array('..', '.'));
            $zipFileUrl = (public_path() . "/img/bono/".$datos_cliente->nombre_cliente.$datos_cliente->apellido_cliente. '/'. $zip_nombre);
            return response()->download($zipFileUrl, 'Bonos.zip', array('Content-Type: application/octet-stream','Content-Length: '. filesize($zipFileUrl)))->deleteFileAfterSend(true);
        }else{
            return ('Error, directorio no encontrado...');
        }
        
    }
    // Funcion para generar bono de forma manual en caso de emergencia...
    public function bonoAux(Request $request){
        date_default_timezone_set("America/Santiago");
        $imgPath = public_path('img/bono/bono.jpg');
        $font = public_path('font/arial.ttf');
        $bono = DB::table('bonos_digitales')->orderBy('bono_digital', 'desc')->first();
        DB::table('bonos_digitales')->insert(
            ['bono_digital' => $bono->bono_digital + $request->cantidad]
        );

        for($i = 1; $i <= $request->cantidad ; $i++){
            $our_image = imagecreatefromjpeg($imgPath);
    
            imagettftext($our_image, 50, 0, 100, 150, 0x222222, $font, $bono->bono_digital + $i);
            imagettftext($our_image, 50, 0, 700, 150, 0x222222, $font, $bono->bono_digital + $i);
            imagettftext($our_image, 24, 90, 200, 720, 0x222222, $font, $request->nombre_cliente.' '.$request->apellido_cliente);
            imagettftext($our_image, 24, 90, 295, 720, 0x222222, $font, $request->email);
            imagettftext($our_image, 28, 90, 390, 720, 0x222222, $font, $request->telefono);
    
            imageJpeg($our_image, public_path("img/bono_auxiliar").'/aporte'.$i.'_JMAGALLANICAS-'.$request->orden_compra.'.jpg', 85);
            imagedestroy($our_image);
            
            DB::table('bonos_digitales_vendidos')->insert(
                ['nombre' => $request->nombre_cliente,
                'apellido' => $request->apellido_cliente,
                'correlativo' => $bono->bono_digital + $i,
                'orden_compra'=> 'JMAGALLANICAS-'.$request->orden_compra,
                'telefono' => $request->telefono,
                'email' => $request->email]
            );
        }

        return ('Bono generado con éxito');
    }

}
