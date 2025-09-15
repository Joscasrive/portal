<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

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
        return view('users.create', compact('roles'));
    }

    /**
     * Almacena un nuevo usuario en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'is_commissionable' => 'nullable|boolean',
            'commission_percentage' => 'required_if:is_commissionable,true|nullable|numeric|between:0,100',
            'roles' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'company' => $request->company,
            'is_commissionable' => $request->has('is_commissionable'),
            'commission_percentage' => $request->input('commission_percentage'),
        ]);

        $user->assignRole($request->input('roles'));

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
        return view('users.edit', compact('user', 'roles', 'userRole'));
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
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'phone' => 'required|string|max:255',
                'company' => 'nullable|string|max:255',
                'is_commissionable' => 'nullable|boolean',
                'commission_percentage' => 'required_if:is_commissionable,true|nullable|numeric|between:0,100',
                'roles' => 'required',
            ]);

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'company' => $request->company,
                'is_commissionable' => $request->has('is_commissionable'),
                'commission_percentage' => $request->input('commission_percentage'),
            ]);
            $user->syncRoles($request->input('roles'));

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