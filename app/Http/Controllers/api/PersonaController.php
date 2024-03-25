<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PersonaController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = "";
            if ($request->search) {
                $search = $request->search;
            }

            $personas = Persona::where('nombre', 'LIKE', "%{$search}%")
                ->where('activo', '1')
                ->selectRaw("
                        id,
                        nombre,
                        COALESCE(documento, '') as documento,
                        activo,
                        COALESCE(telefono, '') as telefono,
                        COALESCE(banco, '') as banco,
                        COALESCE(cuenta, '') as cuenta
                    ")
                ->get();

            if ($personas->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron personas',
                    'data' => null
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => 'Personas encontradas',
                'data' => $personas
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de consulta en la base de datos',
                'data' => null
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado',
                'data' => null
            ], 500);
        }
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        // Validar la entrada
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:150',
            'documento' => 'nullable|string|max:25',
            'telefono' => 'nullable|string|max:10',
            'banco' => 'nullable|string|max:100',
            'cuenta' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validación fallida',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Crear la persona
            $persona = new Persona();
            $persona->nombre = $request->nombre;
            $persona->documento = $request->documento;
            $persona->telefono = $request->telefono;
            $persona->banco = $request->banco;
            $persona->cuenta = $request->cuenta;
            $persona->save();


            // Devolver respuesta
            return response()->json([
                'status' => 'success',
                'message' => 'Persona creada exitosamente',
                'data' => $persona,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear la persona',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        try {
            $persona = Persona::findOrFail($id);

            // Devolver respuesta
            return response()->json([
                'status' => 'success',
                'message' => 'Persona encontrada exitosamente',
                'data' => $persona,
            ], 200);
        } catch (\Exception $e) {
            // Devolver respuesta de error
            return response()->json([
                'status' => 'error',
                'message' => 'Error al encontrar la persona: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $persona = Persona::findOrFail($id);
            $persona->nombre = $request->nombre ?? $persona->nombre;
            $persona->documento = $request->documento ?? $persona->documento;
            $persona->telefono = $request->telefono ?? $persona->telefono;
            $persona->banco = $request->banco ?? $persona->banco;
            $persona->cuenta = $request->cuenta ?? $persona->cuenta;
            $persona->save();

            // Devolver respuesta de éxito
            return response()->json([
                'status' => 'success',
                'message' => 'Persona actualizada exitosamente',
                'data' => $persona,
            ], 200);
        } catch (\Exception $e) {
            // Devolver respuesta de error
            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar la persona',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function destroy($id)
    {
        try {
            $persona = Persona::findOrFail($id);
            $persona->delete();

            // Devolver respuesta de éxito
            return response()->json([
                'status' => 'success',
                'message' => 'Persona eliminada exitosamente',
            ], 200);
        } catch (\Exception $e) {
            // Devolver respuesta de error
            return response()->json([
                'status' => 'error',
                'message' => 'Error al eliminar la persona',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
