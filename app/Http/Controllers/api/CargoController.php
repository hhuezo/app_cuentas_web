<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Cargo;
use App\Models\Prestamo;
use App\Models\Recibo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CargoController extends Controller
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
            //$fechaCarbon = Carbon::createFromFormat('d/m/Y', $request->fecha);

            $validatedData = $request->validate([
                'prestamo_id' => 'required',
                'fecha' => 'required',
                'cantidad' => 'required|numeric|min:0',
                //'observacion' => 'required',
            ]);
            $cargo = new Cargo();
            $cargo->prestamo_id = $request->prestamo_id;
            $cargo->fecha = $request->fecha;
            $cargo->cantidad = $request->cantidad;
            $cargo->comprobante = $request->comprobante;
            $cargo->observacion = $request->observacion;
            $cargo->save();

            $recibo = Recibo::where('prestamo_id',$request->prestamo_id)->orderBy('id','desc')->first();
            $recibo->remanente = $recibo->remanente + $request->cantidad;
            $recibo->save();

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
            $prestamo = Prestamo::join('persona', 'persona.id', '=', 'prestamo.persona_id')
                ->select('prestamo.id', DB::raw("LPAD(prestamo.codigo, 4, '0') AS codigo"), 'persona.nombre')
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
