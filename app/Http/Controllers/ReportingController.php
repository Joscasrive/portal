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
    $headers = [
        "Authorization: Bearer $api",
        "Accept: application/json"
    ];

    // 1. Obtener Datos del Cliente (Contacto)
    $curlCliente = curl_init();
    curl_setopt_array($curlCliente, [
        CURLOPT_URL => "https://rest.gohighlevel.com/v1/contacts/$id",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
    ]);
    $responseCliente = curl_exec($curlCliente);
    curl_close($curlCliente);
    $clienteData = json_decode($responseCliente, true);

    // ====================================================================
    // LOGICA PARA ENCONTRAR EL ULTIMO ROUND COMPLETADO Y SU URL
    // ====================================================================

    
    $contact = $clienteData['contact'] ?? $clienteData;
    
    // 1. Crear un mapa temporal de los campos personalizados para buscar por ID.
    $customFieldsMap = [];
    $ghlCustomFields = $contact['customField'] ?? [];
    foreach ($ghlCustomFields as $field) {
        
        if (isset($field['id'])) {
            $customFieldsMap[$field['id']] = $field;
        }
    }

    // 🚩 NUEVA LÓGICA: OBTENER SOLO LA URL DEL REPORTE INICIAL 🚩
    $reporteInicialFieldId = '1MPmFaGP5xZio9SBVRlY';
    $reporteInicialUrl = null; 

    $reporteData = $customFieldsMap[$reporteInicialFieldId] ?? null;

    // Verificar si el campo existe, es un array y tiene un 'value' con datos
    if (
        is_array($reporteData) && 
        isset($reporteData['value']) && 
        is_array($reporteData['value']) && 
        !empty($reporteData['value'])
    ) {
        // Extraer el valor del array (documento)
        $fileValues = $reporteData['value'];
        $firstFile = reset($fileValues); 
        
        // Asignar la URL del primer documento encontrado
        if (isset($firstFile['url'])) {
            $reporteInicialUrl = $firstFile['url'];
        }
    }
    
    
    /**
     * IDs de Campo de GHL para los ROUNDS 1 al 12 (Archivos)
     * Estos IDs se extrajeron del array 'customField' del JSON .
     */
    $roundFieldIds = [
         1 => 'hM3Apm9VgmBzlL4DCAVP', // ROUND 1
         2 => 'xvtCygh76m6FJtYMFaz4', // ROUND 2
         3 => 't270417ZcGNzSB4IjspX', // ROUND 3
         4 => 'tG40ikE9oVYG2rIc9e5F', // ROUND 4
         5 => 'eCg8nZItM3iDOS8t4O8n', // ROUND 5
         6 => 'js2hdR28HtwuHEiqQKEw', // ROUND 6
         7 => '1RTxXtWlrouSvJQhJIdd', // ROUND 7
         8 => '5ynhPiLXe4YZQe5iftLw', // ROUND 8
         9 => 'U7fnWSzFR5Z4wq4Q6oIo', // ROUND 9
         10 => 'efINi12Gn1VyALZ0xVru', // ROUND 10
         11 => 'Xz2PNYmirh9Cki3070k0', // ROUND 11
         12 => '93FJxrbKRfTc087vdIp7', // ROUND 12
    ];

    
    $lastCompletedRound = 'ROUND 0'; 
    $lastCompletedRoundUrl = null; 

    // Iteramos desde ROUND 1 hasta ROUND 12
    for ($i = 1; $i <= 12; $i++) {
        $ghlFieldId = $roundFieldIds[$i] ?? null;

        
        $contactRoundData = $customFieldsMap[$ghlFieldId] ?? null;

        
        if (
            $ghlFieldId && 
            is_array($contactRoundData) && 
            isset($contactRoundData['value']) && 
            is_array($contactRoundData['value']) && 
            !empty($contactRoundData['value'])
        ) {
            // Si encontramos datos de archivo, actualizamos el último round completado 
            $lastCompletedRound = "ROUND $i";
            
            // EXTRAEMOS LA URL DEL DOCUMENTO
            $fileValues = $contactRoundData['value'];
            $firstFile = reset($fileValues); 
            
            if (isset($firstFile['url'])) {
                $lastCompletedRoundUrl = $firstFile['url'];
            }
            
        } else {
            
            break; 
        }
    }
    
    
    // ====================================================================
    
    // 2. Obtener Citas
    $curlCitas = curl_init();
    curl_setopt_array($curlCitas, [
        CURLOPT_URL => "https://rest.gohighlevel.com/v1/contacts/$id/appointments/",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
    ]);
    $responseCitas = curl_exec($curlCitas);
    curl_close($curlCitas);
    $citasData = json_decode($responseCitas, true);

    // 3. Obtener Tareas
    $curlTasks = curl_init();
    curl_setopt_array($curlTasks, [
        CURLOPT_URL => "https://rest.gohighlevel.com/v1/contacts/$id/tasks/",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
    ]);
    $responseTasks = curl_exec($curlTasks);
    curl_close($curlTasks);
    $tasksData = json_decode($responseTasks, true);
    
    // 4. Obtener Datos de Crédito (SafeCreditScore)
        // $safeCreditTestUrl = 'https://safecreditscore.com/retailermergedpull_v3.asp?customerEmail=scs923a@mailinator.com&passwordHash=cd25f0f0c93e1e2ccc1edce9a922146eb4f4624f90e4a4222ccf9f31afcdf575&type=json';

        // $curlSafeCredit = curl_init();
        // curl_setopt_array($curlSafeCredit, [
        //     CURLOPT_URL => $safeCreditTestUrl,
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_FOLLOWLOCATION => true, 
        // ]);
        
        // $responseSafeCredit = curl_exec($curlSafeCredit);
        // $httpCode = curl_getinfo($curlSafeCredit, CURLINFO_HTTP_CODE);
        // curl_close($curlSafeCredit);
        
        
        // if ($httpCode === 200) {
        //     $creditData = json_decode($responseSafeCredit, true);
        // } else {
            
        //     $creditData = [
        //         'error' => 'Error al llamar a la API de SafeCreditScore con datos de prueba ', 
        //         'http_code' => $httpCode, 
        //         'response_message' => $responseSafeCredit,
        //     ];
        // }

   
    return view('reporting.index', compact(
        'clienteData', 
        'citasData', 
        'tasksData', 
        'lastCompletedRound', 
        'lastCompletedRoundUrl',
        'reporteInicialUrl' 
    )); 
}
}
