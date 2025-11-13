<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Survey;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', '!=', 'admin')
                    ->with(['consultor:id,name', 'sedes:id,name,location']); // Eager load relations

        // Filtrar por rol
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Filtrar por estado
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Búsqueda por nombre o email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        // Agregar estadísticas a cada usuario
        $users->getCollection()->transform(function ($user) {
            $user->statistics = $user->getStatistics();
            $user->survey_url = $user->survey_url;
            $user->consultor_name = $user->consultor ? $user->consultor->name : null;
            return $user;
        });

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Crear nuevo usuario
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:consultor,sede',
            'consultor_id' => 'nullable|exists:users,id', // Consultor asignado (solo para sedes)
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'consultor_id' => $request->consultor_id, // Asignar consultor si es sede
                'phone' => $request->phone,
                'location' => $request->location,
                'unique_token' => Str::random(32),
                'is_active' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Usuario creado exitosamente',
                'data' => [
                    'user' => $user,
                    'survey_url' => $user->survey_url,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar usuario específico
     */
    public function show($id)
    {
        $user = User::with(['consultor:id,name', 'sedes:id,name,location'])->find($id);

        if (!$user || $user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $user->statistics = $user->getStatistics();
        $user->survey_url = $user->survey_url;
        $user->consultor_name = $user->consultor ? $user->consultor->name : null;
        $user->recent_surveys = $user->surveys()
                                     ->orderBy('created_at', 'desc')
                                     ->take(10)
                                     ->get();

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user || $user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|nullable|string|min:8',
            'role' => 'sometimes|required|in:consultor,sede',
            'consultor_id' => 'nullable|exists:users,id',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            if ($request->has('name')) {
                $user->name = $request->name;
            }
            if ($request->has('email')) {
                $user->email = $request->email;
            }
            if ($request->has('password') && !empty($request->password)) {
                $user->password = Hash::make($request->password);
            }
            if ($request->has('role')) {
                $user->role = $request->role;
            }
            if ($request->has('consultor_id')) {
                $user->consultor_id = $request->consultor_id;
            }
            if ($request->has('phone')) {
                $user->phone = $request->phone;
            }
            if ($request->has('location')) {
                $user->location = $request->location;
            }
            if ($request->has('is_active')) {
                $user->is_active = $request->is_active;
            }

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado exitosamente',
                'data' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar usuario
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user || $user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        try {
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Regenerar token único
     */
    public function regenerateToken($id)
    {
        $user = User::find($id);

        if (!$user || $user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        try {
            $user->unique_token = Str::random(32);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Token regenerado exitosamente',
                'data' => [
                    'survey_url' => $user->survey_url,
                    'unique_token' => $user->unique_token,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al regenerar token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de un usuario
     */
    public function statistics($id)
    {
        $user = User::find($id);

        if (!$user || $user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user->getStatistics()
        ]);
    }

    /**
     * Obtener lista de consultores disponibles (para asignar a sedes)
     */
    public function getConsultores()
    {
        $consultores = User::where('role', 'consultor')
                          ->where('is_active', true)
                          ->select('id', 'name', 'email', 'phone')
                          ->withCount('sedes')
                          ->orderBy('name', 'asc')
                          ->get();

        return response()->json([
            'success' => true,
            'data' => $consultores
        ]);
    }
}
