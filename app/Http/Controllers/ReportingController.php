<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportingController extends Controller
{
     public function __construct()
    {
        $this->middleware('auth');
    }
    public function index($id)
    {
       
        $api = env('MY_APP_ONE');

        

    
    $curlCliente = curl_init();
    curl_setopt_array($curlCliente, [
        CURLOPT_URL => "https://rest.gohighlevel.com/v1/contacts/$id",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $api"
        ],
    ]);
    $responseCliente = curl_exec($curlCliente);
    curl_close($curlCliente);
    $clienteData = json_decode($responseCliente, true);

   
    $curlCitas = curl_init();
    curl_setopt_array($curlCitas, [
        CURLOPT_URL => "https://rest.gohighlevel.com/v1/contacts/$id/appointments/",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $api"
        ],
    ]);
    $responseCitas = curl_exec($curlCitas);
    curl_close($curlCitas);
    
    $citasData = json_decode($responseCitas, true);

    
    return view('reporting.index', compact('clienteData', 'citasData'));
    
}
}