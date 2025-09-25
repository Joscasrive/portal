<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; 
use Exception; 
use Illuminate\Pagination\LengthAwarePaginator;
class PartnerUserController extends Controller
{
    /**
     * Apply middleware to ensure only partners with permission can create users.
     * This is a good practice for security.
     */
    public function __construct()
    {
        $this->middleware('auth');
        
    }

    /**
     * Display the form to create a new user and a table of referrals.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Get the authenticated user (the partner)
        $partner = Auth::user();
        // Load the 'referrals' relationship to get the users referred by this partner
        $referrals = $partner->referrals;
        
        // Se cambió la vista para que apunte a 'partner.blade.php' directamente en la carpeta views.
        return view('partner', compact('referrals'));
    }

    /**
     * Store a new user created by a partner.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'string', 'max:255','unique:users'],
            'company' => ['nullable', 'string', 'max:255'],
        ]);

      
        $partner = Auth::user();

       
        $newUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), 
            'phone' => $request->phone,
            'company' => $request->company,
            'referrer_id' => $partner->id, 
        ]);

       
        $rolePartner = Role::where('name', 'partner')->first();
        if ($rolePartner) {
            $newUser->assignRole($rolePartner);
        }

    
        $highLevelApiKey = env('MY_APP_ONE');
        $nameParts = explode(' ', $request->name, 2);
        $firstName = $nameParts[0];
        $lastName = count($nameParts) > 1 ? $nameParts[1] : '';

        try {
           
            $lookupResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $highLevelApiKey,
            ])->get('https://rest.gohighlevel.com/v1/contacts/lookup', ['email' => $request->email]);

            $lookupData = $lookupResponse->json();

            if ($lookupResponse->successful() && !empty($lookupData['contacts'])) {
                
                $contactId = $lookupData['contacts'][0]['id'];
                Http::withHeaders([
                    'Authorization' => 'Bearer ' . $highLevelApiKey,
                    'Content-Type' => 'application/json',
                ])->post("https://rest.gohighlevel.com/v1/contacts/{$contactId}/tags/", [
                    'tags' => ['sub-partner']
                ]);
            } else {
                
                $newContactData = [
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'companyName' => $request->company,
                    'tags' => ['sub-partner'],
                ];
                Http::withHeaders([
                    'Authorization' => 'Bearer ' . $highLevelApiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://rest.gohighlevel.com/v1/contacts/', $newContactData);
            }
        } catch (Exception $e) {
            Log::error('HighLevel API operation failed: ' . $e->getMessage());
        }

        return redirect()->route('partners.users.create')->with('success', 'User created successfully!');
    }
/**
 * @param  \App\Models\User  $user
 */
    public function destroy(User $user)
    {
       $partner = Auth::user(); 
    if ($user->referrer_id !== $partner->id) {
       
        abort(403, 'Unauthorized action.');
    }
    
    
    $user->referrer_id = null;
    $user->save();
    $highLevelApiKey = env('MY_APP_ONE');
        $nameParts = explode(' ', $user->name, 2);
        $firstName = $nameParts[0];
        $lastName = count($nameParts) > 1 ? $nameParts[1] : '';
    
   try {
           
            $lookupResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $highLevelApiKey,
            ])->get('https://rest.gohighlevel.com/v1/contacts/lookup', ['email' => $user->email]);

            $lookupData = $lookupResponse->json();

            if ($lookupResponse->successful() && !empty($lookupData['contacts'])) {
                
                $contactId = $lookupData['contacts'][0]['id'];
                Http::withHeaders([
                    'Authorization' => 'Bearer ' . $highLevelApiKey,
                    'Content-Type' => 'application/json',
                ])->post("https://rest.gohighlevel.com/v1/contacts/{$contactId}/tags/", [
                    'tags' => ['partner']
                ]);
                 Http::withHeaders([
                'Authorization' => 'Bearer ' . $highLevelApiKey,
                'Content-Type' => 'application/json',
            ])->delete("https://rest.gohighlevel.com/v1/contacts/{$contactId}/tags/", [
                'tags' => ['sub-partner']
            ]);
            } else {
                
                $newContactData = [
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'companyName' => $user->company,
                    'tags' => ['partner'],
                ];
                Http::withHeaders([
                    'Authorization' => 'Bearer ' . $highLevelApiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://rest.gohighlevel.com/v1/contacts/', $newContactData);
            }
        } catch (Exception $e) {
            Log::error('HighLevel API operation failed: ' . $e->getMessage());
        }
         return redirect()->route('partners.users.create')->with('success', 'The user has been successfully unlinked!');
}

////////////////////////////////////////////////

public function show(Request $request ,$email){
  $api = env('MY_APP_ONE');

       
          $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://rest.gohighlevel.com/v1/contacts/?limit=100&query=referral',
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
         $loggedInUserEmail = $email ;
          if ($loggedInUserEmail) {
            $contactos = $contactos->filter(function ($contacto) use ($loggedInUserEmail) {
                $foundEmailMatch = false;
                foreach (($contacto['customField'] ?? []) as $field) {
                    if (isset($field['value']) && is_string($field['value'])) {
                        if (strtolower($field['value']) === strtolower($loggedInUserEmail)) {
                            $foundEmailMatch = true;
                            break; 
                        }
                    }
                }
                return $foundEmailMatch; 
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

$user = User::where('email', $email)->first();

return view('partner-show', [
    'contactos' => $contactosPaginator,
    'totalContactos' => $totalContactos,
    'user' => $user,
]);

}
}
