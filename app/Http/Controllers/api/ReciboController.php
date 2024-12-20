<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Cargo;
use App\Models\Prestamo;
use App\Models\Recibo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReciboController extends Controller
{

    public function index()
    {
        //
    }

    public function create() {}

    public function store(Request $request)
    {
        try {
            $recibo = Recibo::where('prestamo_id', $request->prestamo_id)->orderBy('id', 'desc')->first();
            if ($recibo) {
                $remanente = $recibo->remanente - ($request->cantidad - $request->interes);
            } else {
                $prestamo = Prestamo::findOrFail($request->prestamo_id);
                $remanente = $prestamo->cantidad - ($request->cantidad - $request->interes);
            }

            if ($remanente >= 0) {
                $fechaCarbon = Carbon::createFromFormat('d/m/Y', $request->fecha);
                $recibo = new Recibo();
                $recibo->prestamo_id = $request->prestamo_id;
                $recibo->fecha = $fechaCarbon->format('Y-m-d');
                $recibo->cantidad = $request->cantidad;
                $recibo->comprobante = $request->comprobante;
                $recibo->interes = $request->interes;
                $recibo->remanente = $remanente;
                $recibo->estado = 2;
                $recibo->save();

                if ($recibo->remanente == 0) {
                    $prestamo = Prestamo::findOrFail($request->prestamo_id);
                    $prestamo->estado = 2;
                    $prestamo->save();
                }

                return response()->json([
                    'success' => true,
                    'message' => 'registro guardado correctamente',
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: la cantidad ingresada no es válida',
                ], 500);
            }
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
            $prestamo = Prestamo::findOrFail($id);

            $recibo = Recibo::where('prestamo_id', $id)->orderBy('id', 'desc')->first();

            if ($recibo) {
                $remanente = $recibo->remanente;
                $cargo_prestamo = Cargo::where('prestamo_id', $prestamo->id)->orderBy('id', 'desc')->first();

                if ($cargo_prestamo) {
                    if ($cargo_prestamo->fecha > $recibo->fecha) {

                        $prestamo->remanente = $cargo_prestamo->saldo;
                        $remanente = $cargo_prestamo->saldo;
                    }
                }
            } else {
                $remanente = $prestamo->cantidad;
            }

            if ($prestamo->pago_especifico && $prestamo->amortizacion == 1) {
                $cuota = $prestamo->pago_especifico;
                $tasa = $prestamo->interes;
                $interes = number_format($remanente * ($tasa / 100), 2);
                $total = number_format($cuota, 2);
            } else if ($prestamo->pago_especifico) {
                $cuota = $prestamo->cantidad / $prestamo->numero_pagos;
                $interes = number_format($prestamo->pago_especifico - $cuota, 2);
                $total = number_format($prestamo->pago_especifico, 2);
            } else if ($prestamo->amortizacion == '1') {
                $cuota = $prestamo->cantidad;
                if ($recibo) {
                    $cuota = $recibo->remanente;
                }

                $tasa = $prestamo->interes;
                $interes = number_format($cuota  * ($tasa / 100), 2);
                $total = number_format($cuota +  $interes, 2);
            } else if ($prestamo->tipo_pago_id == 2) {
                $tasa = $prestamo->interes / 2;
                $interes = $prestamo->cantidad * ($tasa / 100);
                $interes = number_format($interes, 2);

                $total = number_format(($prestamo->cantidad / $prestamo->numero_pagos) +  $interes);
                //$total = number_format($interes + ($remanente / 2), 2);
            } else {
                $interes = $remanente * ($prestamo->interes / 100);
                $interes = number_format($interes, 2);
                //$total = number_format($interes + $remanente, 2);
                $total = number_format(($prestamo->cantidad / $prestamo->numero_pagos) +  $interes);
            }

            $response = ["nombre" => $prestamo->persona->nombre, "remanente" => $remanente, "interes" => $interes, "total" => $total];

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
        $recibo = DB::table('recibo as r')
            ->selectRaw("DATE_FORMAT(r.fecha, '%d/%m/%Y') AS fecha")
            ->addSelect([
                'r.id',
                'pe.nombre',
                'r.cantidad',
                'r.interes',
                DB::raw('(r.cantidad + r.interes + 0) as total'),
                'r.estado',
                'r.comprobante_url as comprobante'
            ])
            ->join('prestamo as p', 'p.id', '=', 'r.prestamo_id')
            ->join('persona as pe', 'pe.id', '=', 'p.persona_id')
            ->where('r.id', $id)
            ->first();


        // Verificar si el archivo existe
        $comprobantePath = public_path('comprobantes/' . $recibo->comprobante);

        if (is_readable($comprobantePath)) {
            try {
                // Leer el archivo y convertirlo a Base64
                $imageData = file_get_contents($comprobantePath);
                $base64Image = base64_encode($imageData);

                // Si el Base64 tiene un prefijo, quitarlo
                $base64Cleaned = str_replace('data:image/jpeg;base64,', '', $base64Image);
            } catch (\Exception $e) {
                // Si ocurre un error al leer el archivo, devolver null
                $base64Cleaned = null;
            }
        } else {
            // Si el archivo no existe o no es legible, asignar null
            $base64Cleaned = null;
        }

        $recibo->comprobante = $base64Cleaned;


        return response()->json([
            'success' => true,
            'data' => $recibo,
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $recibo = Recibo::findOrFail($id);

            // Establecer el estado
            $recibo->estado = $request->estado ? 2 : 1;

            // Asignar el comprobante solo si no es nulo
            $recibo->comprobante = $request->comprobante ?? $recibo->comprobante;

            $recibo->save();

            return response()->json([
                'success' => true,
                'id' => $recibo->id
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el recibo: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
