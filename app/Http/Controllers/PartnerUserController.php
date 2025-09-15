<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
        
        
        return redirect()->route('partners.users.create')->with('success', 'User created successfully!');
    }
}
