<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\CargoFijo;
use App\Models\PrestamoFijo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CargoFijoController extends Controller
{

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            $fechaCarbon = Carbon::createFromFormat('d/m/Y', $request->fecha);

            $cargo = new CargoFijo();
            $cargo->prestamo_fijo_id = $request->prestamo_id;
            $cargo->fecha = $fechaCarbon->format('Y-m-d');
            $cargo->cantidad = $request->cantidad;
            $cargo->comprobante = $request->comprobante;
            $cargo->observacion = $request->observacion;
            $cargo->save();

            return response()->json([
                'success' => true,
                'message' => 'Prestamo creado exitosamente',
                'data' => $cargo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el prestamo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $prestamo = PrestamoFijo::join('persona', 'persona.id', '=', 'prestamo_fijo.persona_id')
                ->select('prestamo_fijo.id', DB::raw("LPAD(prestamo_fijo.codigo, 4, '0') AS codigo"), 'persona.nombre')
                ->findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Prestamos encontrados',
                'data' => $prestamo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
