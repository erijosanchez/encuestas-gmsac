<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UsersWebController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', '!=', 'admin')
                    ->with(['consultor', 'sedes']);

        // Filtros
        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        // Agregar estadísticas
        $users->getCollection()->transform(function ($user) {
            $user->statistics = $user->getStatistics();
            return $user;
        });

        return view('users.index', compact('users'));
    }

    /**
     * Mostrar formulario de crear
     */
    public function create()
    {
        $consultores = User::where('role', 'consultor')
                          ->where('is_active', true)
                          ->orderBy('name')
                          ->get();

        return view('users.create', compact('consultores'));
    }

    /**
     * Guardar nuevo usuario
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required|in:consultor,sede',
            'consultor_id' => 'nullable|exists:users,id',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'consultor_id' => $request->consultor_id,
            'phone' => $request->phone,
            'location' => $request->location,
            'unique_token' => Str::random(32),
            'is_active' => true,
        ]);

        return redirect()->route('users.index')
                        ->with('success', 'Usuario creado exitosamente');
    }

    /**
     * Mostrar detalle de usuario
     */
    public function show($id)
    {
        $user = User::with(['consultor', 'sedes', 'surveys'])
                   ->findOrFail($id);

        if ($user->role === 'admin') {
            abort(404);
        }

        $user->statistics = $user->getStatistics();
        $user->recent_surveys = $user->surveys()
                                     ->orderBy('created_at', 'desc')
                                     ->take(10)
                                     ->get();

        return view('users.show', compact('user'));
    }

    /**
     * Mostrar formulario de editar
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            abort(404);
        }

        $consultores = User::where('role', 'consultor')
                          ->where('is_active', true)
                          ->where('id', '!=', $id)
                          ->orderBy('name')
                          ->get();

        return view('users.edit', compact('user', 'consultores'));
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:8',
            'role' => 'required|in:consultor,sede',
            'consultor_id' => 'nullable|exists:users,id',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->consultor_id = $request->consultor_id;
        $user->phone = $request->phone;
        $user->location = $request->location;
        $user->is_active = $request->has('is_active');

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('users.index')
                        ->with('success', 'Usuario actualizado exitosamente');
    }

    /**
     * Eliminar usuario
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            abort(404);
        }

        $user->delete();

        return redirect()->route('users.index')
                        ->with('success', 'Usuario eliminado exitosamente');
    }

    /**
     * Regenerar token
     */
    public function regenerateToken($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            abort(404);
        }

        $user->unique_token = Str::random(32);
        $user->save();

        return redirect()->back()
                        ->with('success', 'Token regenerado exitosamente');
    }
}
