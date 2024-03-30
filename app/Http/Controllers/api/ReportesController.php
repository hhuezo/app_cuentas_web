<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Prestamo;
use App\Models\Recibo;
use App\Models\TempPago;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

            // Si la fecha final está presente en la solicitud
            if ($request->fecha_final) {
                $fechaCarbon = Carbon::createFromFormat('d/m/Y', $request->fecha_final);
                $fecha_final = $fechaCarbon->format('Y-m-d');
            } else {
                // Obtener la fecha actual
                $now = Carbon::now();

                // Obtener el último día del mes actual
                $fecha_final = $now->endOfMonth()->format('Y-m-d');
            }

            $prestamos = Prestamo::get();
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
                ->get();

            /*$fecha_temp = $prestamo->primer_pago;
            foreach ($pagos as $pago) {
                //print($fecha_temp . ' ' . $prestamo->primer_pago . '<br>');
                $recibo = Recibo::where('prestamo_id', $pago->prestamo_id)->where('fecha', $pago->fecha)->first();

                if ($pago->fecha == $prestamo->primer_pago) {
                    if ($recibo) {
                        $pago->pagado = 1;
                    }
                } else {
                    $recibo = Recibo::where('prestamo_id', $pago->prestamo_id)->whereBetween('fecha', [$fecha_temp, $pago->fecha])->first();
                    if ($recibo) {
                        $pago->pagado = 1;
                    }
                }

                $ultima_fecha = $pago->fecha;
                $nuevaFecha = strtotime('+1 day', strtotime($ultima_fecha));


                $fecha_temp = date('Y-m-d', $nuevaFecha);
            }*/




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
        //
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
        //
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
