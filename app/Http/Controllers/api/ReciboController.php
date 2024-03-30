<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Prestamo;
use App\Models\Recibo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReciboController extends Controller
{

    public function index()
    {
        //
    }

    public function create()
    {
    }

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
                if ($recibo->remanente == 0) {
                    $recibo->estado = 1;
                }
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
            } else {
                $remanente = $prestamo->cantidad;
            }

            if ($prestamo->pago_especifico && $prestamo->amortizacion == 1) {
                $cuota = $prestamo->pago_especifico;
                $tasa = $prestamo->interes;
                $interes = number_format($remanente * ($tasa/100), 2);
                $total = number_format($cuota, 2);

            }
            else if ($prestamo->pago_especifico) {
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
        //
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
        //
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
