<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Mail;
use App\Mail\EnviarMail;

class TestController extends Controller
{

    /**
     * Send My Demo Mail Example
     *
     * @return void
     */
    public function sendMailAporte()
    {
    	$myEmail = 'marceloh1987@gmail.com';
    	Mail::to($myEmail)->send(new EnviarMail());

    	
    	dd("Mail Send Successfully");
    }

}