<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Cargo;
use App\Models\Prestamo;
use App\Models\Recibo;
use Carbon\Carbon;
use Exception;
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

        $base64Cleaned = null;  // Valor por defecto

        if ($recibo && $recibo->comprobante) {
            $comprobantePath = public_path('comprobantes/' . $recibo->comprobante);

            if ($comprobantePath && is_readable($comprobantePath)) {
                try {
                    $imageData = file_get_contents($comprobantePath);
                    $base64Image = base64_encode($imageData);
                    // Si el base64 tiene un prefijo, lo quitamos (aunque en este caso no debería tener)
                    $base64Cleaned = str_replace('data:image/jpeg;base64,', '', $base64Image);
                } catch (\Exception $e) {
                    $base64Cleaned = null;
                }
            }
        }

        // Solo si $recibo no es null, asignamos el base64
        if ($recibo) {
            $recibo->comprobante = $base64Cleaned;
        }

        return response()->json([
            'success' => true,
            'data' => $recibo,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        try {
            $recibo = Recibo::findOrFail($id);

            // Establecer el estado: true => 2, false => 1
            $recibo->estado = $request->estado ?? 1;

            // Guardar el comprobante si existe
            if ($request->has('comprobante') && $request->comprobante) {
                $fileName = 'recibo_' . $recibo->id . '.jpg';
                $filePath = public_path('comprobantes/' . $fileName);

                try {
                    // Limpiar base64 si tiene prefijo
                    $base64Image = preg_replace('/^data:image\/\w+;base64,/', '', $request->comprobante);
                    $imageData = base64_decode($base64Image);

                    if ($imageData === false) {
                        throw new Exception('La imagen no pudo ser decodificada');
                    }

                    file_put_contents($filePath, $imageData);

                    // Guardar solo el nombre del archivo en la base de datos
                    $recibo->comprobante_url = $fileName;
                } catch (Exception $e) {
                    Log::error('Error al guardar el comprobante: ' . $e->getMessage());
                }
            }

            $recibo->save();

            return response()->json([
                'success' => true,
                'id' => $recibo->id
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el recibo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        //
    }
}
