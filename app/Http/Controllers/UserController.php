<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; 
use Exception; 

class UserController extends Controller
{
    /**
     * Aplica el middleware para asegurar que solo los administradores
     * puedan acceder a estas funciones.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Muestra una lista de todos los usuarios.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Obtiene todos los usuarios, paginando para un mejor rendimiento
        $users = User::paginate(15);
        
        return view('users.index', compact('users'));
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();
        // Obtiene todos los permisos para el formulario de creación
        $permissions = Permission::pluck('name', 'name')->all();
        return view('users.create', compact('roles', 'permissions'));
    }

    /**
     * Almacena un nuevo usuario en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
     public function store(Request $request)
    {
        // Validación de datos del usuario
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
           'phone' => 'required|string|max:255|unique:users',
            'company' => 'nullable|string|max:255',
            'is_commissionable' => 'nullable|boolean',
            'commission_percentage' => 'required_if:is_commissionable,true|nullable|numeric|between:0,100',
            'roles' => 'required',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        // Creación del usuario en tu base de datos
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'company' => $request->company,
            'is_commissionable' => $request->has('is_commissionable'),
            'commission_percentage' => $request->input('commission_percentage'),
        ]);

        // Asignar rol y permisos al usuario
        $user->assignRole($request->input('roles'));
        $user->syncPermissions($request->input('permissions', []));

        // Solo procesar en HighLevel si el usuario tiene el rol 'partner'
        if ($user->hasRole('partner')) {
            // Preparar datos para HighLevel
            $highLevelApiKey = env('MY_APP_ONE');
            $nameParts = explode(' ', $request->name, 2);
            $firstName = $nameParts[0];
            $lastName = count($nameParts) > 1 ? $nameParts[1] : '';

            // Buscar el contacto en HighLevel por email
            try {
                $lookupResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $highLevelApiKey,
                ])->get('https://rest.gohighlevel.com/v1/contacts/lookup', ['email' => $request->email]);

                $lookupData = $lookupResponse->json();

                if ($lookupResponse->successful() && !empty($lookupData['contacts'])) {
                    // El contacto ya existe, solo le agregamos la etiqueta
                    $contactId = $lookupData['contacts'][0]['id'];
                    Http::withHeaders([
                        'Authorization' => 'Bearer ' . $highLevelApiKey,
                        'Content-Type' => 'application/json',
                    ])->post("https://rest.gohighlevel.com/v1/contacts/{$contactId}/tags/", [
                        'tags' => ['partner']
                    ]);
                } else {
                    // El contacto no existe, lo creamos
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
        }

        // Redirección con mensaje de éxito
        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }
    /**
     * Muestra los detalles de un usuario específico.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function show(User $user)
    {
        // Esta función recibe el modelo 'User' directamente gracias al Route Model Binding.
        return view('users.show', compact('user'));
    }

    /**
     * Muestra el formulario para editar un usuario.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->all();

       
        $permissionsToShow = [
            'view company',
            'view email',
            'view phone',
            'view total referred users'
        ];

        
        $userPermissions = $user->getDirectPermissions()->pluck('name')->all();

        return view('users.edit', compact('user', 'roles', 'userRole', 'permissionsToShow', 'userPermissions'));
    }

    /**
     * Actualiza un usuario en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        try {
            $validationRules = [
    'name' => 'required|string|max:255',
    // Ignora el ID del usuario actual para la validación de email
    'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
    // Ignora el ID del usuario actual para la validación de phone
    'phone' => 'required|string|max:255|unique:users,phone,' . $user->id,
    'company' => 'nullable|string|max:255',
    'is_commissionable' => 'nullable|boolean',
    'commission_percentage' => 'required_if:is_commissionable,true|nullable|numeric|between:0,100',
    'roles' => 'required',
    'permissions' => 'nullable|array',
    'permissions.*' => 'string',
];


if ($request->filled('password')) {
    $validationRules['password'] = 'string|min:8|confirmed';
}

$request->validate($validationRules);

           $userData = [
    'name' => $request->name,
    'email' => $request->email,
    'phone' => $request->phone,
    'company' => $request->company,
    'is_commissionable' => $request->has('is_commissionable'),
    'commission_percentage' => $request->input('commission_percentage'),
];

// Solo actualiza la contraseña si se ha proporcionado una nueva en el formulario.
if ($request->filled('password')) {
    $userData['password'] = Hash::make($request->password);
}

// Ahora, pasa el array completo a la función de actualización.
$user->update($userData);
            
            // Sincroniza los roles y permisos del usuario
            $user->syncRoles($request->input('roles'));
            $user->syncPermissions($request->input('permissions', []));

            return redirect()->route('users.index')->with('success', 'User successfully updated.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while updating the user: ' . $e->getMessage());
        }
    }

    /**
     * Elimina un usuario de la base de datos.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();
            return redirect()->route('users.index')->with('success', 'User successfully deleted.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while deleting the user: ' . $e->getMessage());
        }
    }
}