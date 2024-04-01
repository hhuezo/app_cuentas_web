<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\CargoFijo;
use App\Models\Prestamo;
use App\Models\PrestamoFijo;
use App\Models\ReciboFijo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReciboFijoController extends Controller
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
        $validator = Validator::make($request->all(), [
            'prestamo_id' => 'required|integer',
            'cantidad' => 'required|numeric|min:0',
            'fecha' => 'required|date_format:d/m/Y',
            'comprobante' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }
        try {
            $fechaCarbon = Carbon::createFromFormat('d/m/Y', $request->fecha);
            $recibo = new ReciboFijo();
            $recibo->prestamo_fijo_id = $request->prestamo_id;
            $recibo->fecha = $fechaCarbon->format('Y-m-d');
            $recibo->cantidad = $request->cantidad;
            $recibo->comprobante = $request->comprobante;
            $recibo->save();

            $prestamo = PrestamoFijo::findOrFail($request->prestamo_id);

            $sum_recibo = ReciboFijo::where('prestamo_fijo_id', $request->prestamo_id)->sum('cantidad');
            $sum_cargos = CargoFijo::where('prestamo_fijo_id', $request->prestamo_id)->sum('cantidad');

            $remanente = $prestamo->cantidad + $sum_cargos - $sum_recibo;

            if ($remanente == 0) {
                $prestamo->estado = 2;
                $prestamo->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'registro guardado correctamente',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $prestamo = PrestamoFijo::findOrFail($id);

            $sum_recibo = ReciboFijo::where('prestamo_fijo_id', $id)->sum('cantidad');
            $sum_cargos = CargoFijo::where('prestamo_fijo_id', $id)->sum('cantidad');

            $remanente = $prestamo->cantidad + $sum_cargos - $sum_recibo;


            $response = ["nombre" => $prestamo->persona->nombre, "remanente" => $remanente];

            return response()->json([
                'success' => true,
                'message' => 'Prestamos encontrados',
                'data' => $response
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
