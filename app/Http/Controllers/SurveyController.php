<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SurveyController extends Controller
{
    public function show($token)
    {
        $user = User::where('unique_token', $token)
            ->where('is_active', true)
            ->first();

        if (!$user) {
            return view('survey.not-found');
        }

        return view('survey.form', compact('user', 'token'));
    }

    public function getData($token)
    {
        $user = User::where('unique_token', $token)
            ->where('is_active', true)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Encuesta no encontrada o inactiva'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role,
                    'location' => $user->location,
                ]
            ]
        ]);
    }

    public function store(Request $request, $token)
    {
        $user = User::where('unique_token', $token)
            ->where('is_active', true)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Encuesta no encontrada o inactiva'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'experience_rating' => 'required|integer|between:1,4',
            'service_quality_rating' => 'required|integer|between:1,4',
            'client_name' => 'nullable|string|max:255',
            'comments' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $survey = Survey::create([
                'user_id' => $user->id,
                'client_name' => $request->client_name,
                'experience_rating' => $request->experience_rating,
                'service_quality_rating' => $request->service_quality_rating,
                'comments' => $request->comments,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => '¡Gracias por tu opinión! Tu encuesta ha sido enviada correctamente.',
                'data' => [
                    'survey_id' => $survey->id,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la encuesta',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
