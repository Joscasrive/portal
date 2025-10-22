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

private const COMMISSION_ID = 'ELw4jVDq0DFCXUxOtSfz'; 
    private const PAYMENT_STATUS_ID = 'KgTdj1bYqjSB8Zjg4oNq'; 

    public function show(Request $request, $email)
    {
        $api = env('MY_APP_ONE');
        $headers = ["Authorization: Bearer $api"];

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
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        
        $contactos = json_decode($response, true)['contacts'] ?? [];
        $contactos = collect($contactos);

        // Usamos las constantes dentro del 'map'
        $commissionId = self::COMMISSION_ID;
        $paymentStatusId = self::PAYMENT_STATUS_ID;
        
        $contactos = $contactos->map(function ($contacto) use ($commissionId, $paymentStatusId) {
            
            // 1. Crear un mapa de campos personalizados para búsqueda rápida
            $customFieldsMap = [];
            foreach (($contacto['customField'] ?? []) as $field) {
                if (isset($field['id'])) {
                    $customFieldsMap[$field['id']] = $field['value'] ?? null;
                }
            }
            
            // --- Lógica de Estado de Activo (Existente) ---
            $esActivo = collect($contacto['customField'] ?? [])
                ->contains(function ($field) {
                    return is_string($field['value']) && strtolower($field['value']) === 'current';
                });

            $contacto['estado'] = $esActivo ? 'active' : 'inactive';

            
            // --- Lógica Nueva: Pago Condicional (CORREGIDA) ---
            $commissionStatus = $customFieldsMap[$commissionId] ?? null;
            $paymentStatus = $customFieldsMap[$paymentStatusId] ?? null;

            // La variable final. Por defecto, es 'N/A' (Not Applicable).
            // Esto asegura que siempre será una cadena.
            $conditionalStatus = 'N/A'; 

            // 1. Verificar si la comisión está marcada como 'YES'
            if (is_string($commissionStatus) && strtolower($commissionStatus) === 'yes') {
                
                // 2. Si la comisión es 'yes', comprobamos el estado de pago real.
                if ($paymentStatus && is_string($paymentStatus)) {
                    // Asignar el estado de pago (ej: 'Pagada', 'En progreso')
                    $conditionalStatus = $paymentStatus; 
                } else {
                    // Si es 'yes' pero el campo de estado de pago está vacío
                    $conditionalStatus = 'Undefined';
                }
            }
            
            // Asignar el estado de pago condicional (siempre una cadena)
            $contacto['payment_status_conditional'] = $conditionalStatus;

            return $contacto;
        });
        // ====================================================================
        
        $loggedInUserEmail = $email;
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
        
        // ... (El resto del filtrado y paginación) ...
        
        $search = $request->get('search');

        if ($search) {
            $contactos = $contactos->filter(function ($contacto) use ($search) {
                // Agregado '?? ""' para manejar posibles valores nulos de forma segura
                return stripos($contacto['contactName'] ?? '', $search) !== false || 
                       stripos($contacto['email'] ?? '', $search) !== false || 
                       stripos($contacto['phone'] ?? '', $search) !== false;
            });
        }

        $perPage = request()->get('perPage', 10);
        $currentPage = request()->get('page', 1);

        $contactosPaginados = $contactos->forPage($currentPage, $perPage);
        $totalContactos = $contactos->count();

        $contactosPaginator = new LengthAwarePaginator(
            $contactosPaginados,
            $totalContactos,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $user = User::where('email', $email)->first();

        return view('partner-show', [
            'contactos' => $contactosPaginator,
            'totalContactos' => $totalContactos,
            'user' => $user,
        ]);
    }
}
