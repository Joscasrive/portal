<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use Illuminate\Support\Env;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }
    public function customers(Request $request)
    {
         $api = env('MY_APP_ONE');
         
        if (!empty($request->email)) {
           $email = $request->email;
          
            $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://rest.gohighlevel.com/v1/contacts/?limit=100&query=$email",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    "Authorization: Bearer $api",
  ),
));

$response = curl_exec($curl);
curl_close($curl);


        }else {
          $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://rest.gohighlevel.com/v1/contacts/?limit=100&query=marketing',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    "Authorization: Bearer $api",
  ),
));

$response = curl_exec($curl);
curl_close($curl);
        }
       
       
        


        
      
        $contactos = json_decode($response, true)['contacts']; //Ojoooo Esto debería ser una matriz de contactos.
        $contactos = collect($contactos);
        $contactos = $contactos->map(function ($contacto) {
            $esActivo = collect($contacto['customField'] ?? [])
                ->contains(function ($field) {
                    return is_string($field['value']) && strtolower($field['value']) === 'current';

                });
        
            $contacto['estado'] = $esActivo ? 'active' : 'inactive';
            return $contacto;
        });
        
        $filtroEstado = $request->get('estado'); 

        if (in_array($filtroEstado, ['active', 'inactive'])) {
            $contactos = $contactos->filter(function ($contacto) use ($filtroEstado) {
                return $contacto['estado'] === $filtroEstado;
            });
        }

 
 $search = $request->get('search');

 
 if ($search) {
     $contactos = $contactos->filter(function ($contacto) use ($search) {
         return stripos($contacto['contactName'], $search) !== false || stripos($contacto['email'], $search)!== false || stripos($contacto['phone'], $search) !== false;
     });
 }

// Número de elementos por página
$perPage = request()->get('perPage', 10);
// Página actual
$currentPage = request()->get('page', 1);

// Crear un segmento de la colección para la página actual
$contactosPaginados = $contactos->forPage($currentPage, $perPage);

// Total de contactos
$totalContactos = $contactos->count();

// Crear el paginador
$contactosPaginator = new LengthAwarePaginator(
    $contactosPaginados, // Elementos de la página actual
    $totalContactos,     // Total de elementos
    $perPage,            // Elementos por página
    $currentPage,        // Página actual
    ['path' => request()->url(), 'query' => request()->query()] // Preservar los parámetros de la URL
);


return view('customers', [
    'contactos' => $contactosPaginator,
    'totalContactos' => $totalContactos,
]);


}
public function conversations()
    {
        return view('conversations');
    }

public function Requests(Request $request)
    {
        $validated = $request->validate([
        'customerEmail' => 'required|email',
        'requestTitle' => 'required|string|max:255',
        'description' => 'required|string',
        'dueDate' => 'required|date_format:Y-m-d\TH:i',
    ]);

    // Extraer los datos ya validados
    $email = $validated['customerEmail'];
    $title = $validated['requestTitle'];
    $description = $validated['description'];
    $dueDate = Carbon::parse($validated['dueDate'])->toIso8601String();
    $id=null;

   $api = env('MY_APP_ONE');
   if ($id == null) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => "https://rest.gohighlevel.com/v1/contacts/lookup?email=$email",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer $api"
        ),
        ));

    $response = curl_exec($curl);
    curl_close($curl);
  
$data = json_decode($response, true);


if (isset($data['email']['message'])) {
    return redirect()->back()->with('error', 'The email is invalid or does not exist.');
}


if (empty($data['contact'])) {
    return redirect()->back()->with('error', 'No contact was found with that email.');
}

    $contactos = json_decode($response, true)['contacts'];

    

    $contacto = $contactos[0];

     $id = $contacto['id'];
     if (empty($contacto['assignedTo'])) {
        $contacto['assignedTo'] = 'f7MZKs2m62NyRphpUKqb';
     }
     
   }

    $postData = [
    "title" => $title,
    "dueDate" => $dueDate, 
    "description" => $description . "\n\n— Created by Partner via Portal",
    "assignedTo" => $contacto['assignedTo'], 
    "status" => "incompleted"
    ];

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://rest.gohighlevel.com/v1/contacts/$id/tasks/",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($postData), 
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer $api",
            "Content-Type: application/json" 
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    

if (empty($response)) {
       return redirect()->back()->with('error', 'An unexpected problem occurred.');
    }
    return redirect()->back()->with('success', 'Request sent successfully.');
    }


    public function notes( Request $request){
        $body = $request->description;
        $contactId= $request->contact_id;
        $userId=$request->assigned_to;
        if (empty($userId)) {
            $userId = "f7MZKs2m62NyRphpUKqb";
        }
         $postData = [
    "body" => $body . "\n\n— Created by Partner via Portal",
    "userId" => $userId
];
       $api = env('MY_APP_ONE');
       $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://rest.gohighlevel.com/v1/contacts/$contactId/notes/",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>json_encode($postData),
  CURLOPT_HTTPHEADER => array(
    "Authorization: Bearer $api",
    "Content-Type: application/json"
  ),
));

$response = curl_exec($curl);

curl_close($curl);
if (empty($response)) {
       return redirect()->back()->with('error', 'An unexpected problem occurred.');
    }
    return redirect()->back()->with('success', 'Note sent successfully.');

 }   
    
}