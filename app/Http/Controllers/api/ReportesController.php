<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Cargo;
use App\Models\Prestamo;
use App\Models\Recibo;
use App\Models\ReciboFijo;
use App\Models\TempPago;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ReportesController extends Controller
{

    public function index(Request $request)
    {
        try {
            // Obtener la fecha actual
            $now = Carbon::now();

            if ($request->fecha_inicio) {
                $fechaCarbon = Carbon::createFromFormat('d/m/Y', $request->fecha_inicio);
                $fecha_inicio = $fechaCarbon->format('Y-m-d');
            } else {
                // Obtener el primer día del mes actual
                $fecha_inicio = $now->firstOfMonth()->format('Y-m-d');
            }
            if ($request->fecha_final) {
                $fechaCarbon = Carbon::createFromFormat('d/m/Y', $request->fecha_final);
                $fecha_final = $fechaCarbon->format('Y-m-d');
            } else {
                // Obtener la fecha actual
                $now = Carbon::now();

                // Obtener el último día del mes actual
                $fecha_final = $now->endOfMonth()->format('Y-m-d');
            }


            $rol = 1;
            $usuario_id = 1;
            if ($request->rol) {
                $rol = $request->rol;
            }

            if ($request->usuario_id) {
                $usuario_id = $request->usuario_id;
            }

            /*

            // Si la fecha final está presente en la solicitud






            $prestamos = Prestamo::where('amortizacion', false);

            if ($rol > 1) {
                $prestamos->where('administrador', $usuario_id);
            }

            $prestamos = $prestamos->get();

            TempPago::where('id', '>', 0)->delete();
            foreach ($prestamos as $prestamo) {
                $primer_pago = $prestamo->primer_pago;
                $numero_pagos = $prestamo->numero_pagos;
                $cuota = $this->calculoCuota($prestamo->id);

                //crando nuevo registro
                $pago = new TempPago();
                $pago->prestamo_id = $prestamo->id;
                $pago->fecha = $primer_pago;
                $pago->cantidad = $cuota;
                $pago->nombre = $prestamo->persona->nombre;
                $pago->save();

                $fecha_primer_pago = Carbon::parse($prestamo->primer_pago);


                if ($prestamo->tipo_pago_id == 1) {
                    for ($i = 1; $i < $numero_pagos; $i++) {

                        $fecha_temp = $fecha_primer_pago;
                        $fecha_primer_pago->addMonth();


                        $pago = new TempPago();
                        $pago->prestamo_id = $prestamo->id;
                        $pago->fecha = $fecha_primer_pago->format('Y-m-d');
                        $pago->cantidad = $cuota;

                        $pago->nombre = $prestamo->persona->nombre;
                        $pago->save();
                    }
                }

                if ($prestamo->tipo_pago_id == 2) {
                    for ($i = 1; $i < $numero_pagos; $i++) {

                        $dia_primario = $fecha_primer_pago->format('d');
                        $dia_primario_numero = intval($dia_primario);
                        $dia_secundario = $fecha_primer_pago->endOfMonth()->format('d');
                        $dia_secundario_numero = intval($dia_secundario);


                        $fecha_temp = $fecha_primer_pago;
                        if ($dia_primario_numero === $dia_secundario_numero) {
                            // Añadir 14 días a la fecha actual
                            //print('1<br>');
                            $fecha_primer_pago->addDays(15);
                        } else if ($fecha_primer_pago->format('d') > 15) {
                            // Obtener el último día del mes actual
                            $fecha_primer_pago = $fecha_primer_pago->endOfMonth();
                            //print('2<br>');
                        } else {
                            // Añadir 14 días a la fecha actual
                            $fecha_primer_pago->addDays(14);
                            //print('3<br>');
                        }



                        $pago = new TempPago();
                        $pago->prestamo_id = $prestamo->id;
                        $pago->fecha = $fecha_primer_pago->format('Y-m-d');
                        $pago->cantidad = $cuota;
                        $pago->nombre = $prestamo->persona->nombre;

                        $pago->save();
                    }
                }
            }

            $pagos = TempPago::selectRaw('id, prestamo_id,fecha, DATE_FORMAT(fecha, "%d/%m/%Y") AS fecha_formato, cantidad, pagado,nombre')
                ->whereBetween('fecha', [$fecha_inicio, $fecha_final])
                ->orderBy('fecha')
                ->get();*/


            $pagos = Recibo::join('prestamo', 'prestamo.id', '=', 'recibo.prestamo_id')
                ->join('persona', 'persona.id', '=', 'prestamo.persona_id')
                ->whereBetween('recibo.fecha', [$fecha_inicio, $fecha_final])
                ->selectRaw('recibo.id, recibo.prestamo_id,recibo.fecha, DATE_FORMAT(recibo.fecha, "%d/%m/%Y") AS fecha_formato, recibo.cantidad, recibo.estado as pagado,persona.nombre')
                ->orderBy('recibo.fecha')
                ->get();

            foreach ($pagos as $pago) {
                $pago->pagado = $pago->pagado - 1;
            }


            $data = ["pagos" => $pagos];

            return response()->json([
                'success' => true,
                'message' => 'Datos encontrados',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }

        dd($data);
    }

    public function calculoCuota($id)
    {
        $prestamo = Prestamo::findOrFail($id);
        $recibo = Recibo::where('prestamo_id', $id)->orderBy('id', 'desc')->first();

        if ($recibo) {
            $remanente = $recibo->remanente;
        } else {
            $remanente = $prestamo->cantidad;
        }

        if ($prestamo->pago_especifico) {
            $cuota = $prestamo->cantidad / $prestamo->numero_pagos;
            $interes = $prestamo->pago_especifico - $cuota;
            $total = $prestamo->pago_especifico;
        } else if ($prestamo->amortizacion == '1') {
            $cuota = $prestamo->cantidad;
            if ($recibo) {
                $cuota = $recibo->remanente;
            }

            $tasa = $prestamo->interes;
            $interes = $cuota  * ($tasa / 100);
            $total = $cuota +  $interes;
        } else if ($prestamo->tipo_pago_id == 2) {
            $tasa = $prestamo->interes / 2;
            $interes = $prestamo->cantidad * ($tasa / 100);


            $total = ($prestamo->cantidad / $prestamo->numero_pagos) +  $interes;
            //$total = number_format($interes + ($remanente / 2), 2);
        } else {
            $interes = $remanente * ($prestamo->interes / 100);

            //$total = number_format($interes + $remanente, 2);
            $total = ($prestamo->cantidad / $prestamo->numero_pagos) +  $interes;
        }

        return $total;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /*$recibos = Recibo::get();

        foreach ($recibos as $recibo) {
            // Verificar si el comprobante no es nulo
            if ($recibo->comprobante != null) {

                // Verificar si el archivo ya existe
                $fileName = 'recibo_' . $recibo->id . '.jpg';
                $filePath = 'comprobantes/' . $fileName;

                // Si el archivo no existe
                if (!file_exists(public_path('comprobantes/' . $fileName))) {

                    // Decodificar el base64 y obtener la parte de la imagen sin el prefijo
                    $base64Image = preg_replace('/^data:image\/jpeg;base64,/', '', $recibo->comprobante);
                    $imageData = base64_decode($base64Image);

                    // Guardar el archivo de imagen en el directorio 'public/comprobantes'
                    file_put_contents(public_path($filePath), $imageData);

                    // Actualizar el registro con la URL del archivo
                    $recibo->comprabante_url =  $fileName;
                    $recibo->save();
                }
            }
        }*/

        /*$prestamos = Prestamo::get();

        foreach ($prestamos as $prestamo) {
            // Verificar si el comprobante no es nulo
            if ($prestamo->comprobante != null) {

                // Verificar si el archivo ya existe
                $fileName = 'prestamo_' . $prestamo->id . '.jpg';
                $filePath = 'comprobantes/' . $fileName;

                // Si el archivo no existe
                if (!file_exists(public_path('comprobantes/' . $fileName))) {

                    // Decodificar el base64 y obtener la parte de la imagen sin el prefijo
                    $base64Image = preg_replace('/^data:image\/jpeg;base64,/', '', $prestamo->comprobante);
                    $imageData = base64_decode($base64Image);

                    // Guardar el archivo de imagen en el directorio 'public/comprobantes'
                    file_put_contents(public_path($filePath), $imageData);

                    // Actualizar el registro con la URL del archivo
                    $prestamo->comprobante_url = $fileName;
                    $prestamo->save();
                }
            }
        }*/
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        try {
            // Definir los meses
            $meses = ["", "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];

            // Último día del mes actual
            $ultimoDiaMesActual = Carbon::now()->endOfMonth()->format('Y-m-d');

            // Primer día de hace 6 meses
            $primerDiaHace6Meses = Carbon::now()->subMonths(5)->startOfMonth()->format('Y-m-d');

            // Inicializar las variables para almacenar los resultados
            $gananciasRecibo = [];
            $gananciasReciboMes = [];
            $gananciasReciboFijo = [];
            $gananciasReciboFijoMes = [];

            // Consultar los intereses de Recibo
            $interesesRecibo = Recibo::selectRaw('SUM(interes) as total_interes, YEAR(fecha) as anio, MONTH(fecha) as mes')
                ->where('estado', 2)
                ->whereBetween('fecha', [$primerDiaHace6Meses, $ultimoDiaMesActual])
                ->groupByRaw('YEAR(fecha), MONTH(fecha)')
                ->get();

            // Consultar los intereses de ReciboFijo
            $interesesReciboFijo = ReciboFijo::selectRaw('SUM(cantidad) as total_interes, YEAR(fecha) as anio, MONTH(fecha) as mes')
                ->where('estado', 2)
                ->whereBetween('fecha', [$primerDiaHace6Meses, $ultimoDiaMesActual])
                ->groupByRaw('YEAR(fecha), MONTH(fecha)')
                ->get();

            // Procesar los resultados de Recibo
            $gananciasRecibo = $interesesRecibo->map(function ($item) {
                return round((float) $item->total_interes, 2);
            })->toArray();
            $mesesIdsRecibo = $interesesRecibo->pluck('mes')->toArray();
            $gananciasReciboMes = array_map(function ($mes) use ($meses) {
                return $meses[$mes];
            }, $mesesIdsRecibo);

            // Procesar los resultados de ReciboFijo
            $gananciasReciboFijo = $interesesReciboFijo->map(function ($item) {
                return round((float) $item->total_interes, 2);
            })->toArray();
            $mesesIdsReciboFijo = $interesesReciboFijo->pluck('mes')->toArray();
            $gananciasReciboFijoMes = array_map(function ($mes) use ($meses) {
                return $meses[$mes];
            }, $mesesIdsReciboFijo);




            //data general
            $count_prestamos = Prestamo::count('id');
            $total_prestado = Prestamo::sum('cantidad');
            $total_cargos = Cargo::sum('cantidad');
            $total_reintegrado = Recibo::where('estado', 2)->sum('cantidad');
            $total_interes_reintegrado = Recibo::where('estado', 2)->sum('interes');
            $total_fijo_reintegrado = ReciboFijo::sum('cantidad');
            $data_general = [
                "countPrestamos" => $count_prestamos,
                "totalPrestado" => number_format($total_prestado +  $total_cargos + 0, 2, '.', ','),
                "totalReintegrado" => number_format($total_reintegrado - $total_interes_reintegrado + 0, 2, '.', ','),
                "dineroInvertido" => number_format(($total_prestado + $total_cargos) - ($total_reintegrado - $total_interes_reintegrado) + 0, 2, '.', ','),
                "totalCargos" => number_format($total_cargos, 2, '.', ','),
                "totalInteresReintegrado" => number_format($total_interes_reintegrado, 2, '.', ','),
                "totalFijoReintegrado" => number_format($total_fijo_reintegrado, 2, '.', ',')
            ];




            $fechaInicio = Carbon::now()->startOfMonth()->format('Y-m-d');
            $fechaFin = Carbon::now()->endOfMonth()->format('Y-m-d');

            $recibos = Recibo::join('prestamo as p', 'recibo.prestamo_id', '=', 'p.id')
                ->join('persona as pe', 'pe.id', '=', 'p.persona_id')
                ->join('tipo_pago as t', 't.id', '=', 'p.tipo_pago_id')
                ->select(
                    'recibo.id',
                    DB::raw("DATE_FORMAT(recibo.fecha, '%d/%m/%Y') as fecha"),
                    'p.cantidad',
                    'p.numero_pagos as numeroPagos',
                    'recibo.cantidad as cantidadRecibo',
                    'pe.nombre',
                    't.nombre as tipo'
                )
                ->whereBetween('recibo.fecha', [$fechaInicio, $fechaFin])
                ->where('recibo.remanente', 0.00)
                ->orderBy('recibo.fecha')
                ->get();


            return response()->json([
                'success' => true,
                'gananciasPrestamo' => $gananciasRecibo,
                'gananciasPrestamoMes' => $gananciasReciboMes,
                'gananciasReciboFijo' => $gananciasReciboFijo,
                'gananciasReciboFijoMes' => $gananciasReciboFijoMes,
                'dataGeneral' => $data_general,
                'prestamosFinalizados' => $recibos
            ]);
        } catch (\Exception $e) {
            // Retornar respuesta en caso de error
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
