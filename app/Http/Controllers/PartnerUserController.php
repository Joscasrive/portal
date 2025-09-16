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
            'phone' => ['required', 'string', 'max:255'],
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
                    'tags' => ['partner']
                ]);
            } else {
                
                $newContactData = [
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'companyName' => $request->company,
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

        return redirect()->route('partners.users.create')->with('success', 'User created successfully!');
    }
}
