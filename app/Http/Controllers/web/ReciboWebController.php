<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Prestamo;
use App\Models\Recibo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReciboWebController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
        $recibo = Recibo::where('prestamo_id', $request->prestamo_id)->orderBy('id', 'desc')->first();
        if ($recibo) {
            $remanente = $recibo->remanente - ($request->cantidad - $request->interes);
        } else {
            $prestamo = Prestamo::findOrFail($request->prestamo_id);
            $remanente = $prestamo->cantidad - ($request->cantidad - $request->interes);
        }

        if ($remanente >= 0) {

            $recibo = new Recibo();
            $recibo->prestamo_id = $request->prestamo_id;
            $recibo->fecha = $request->fecha;
            $recibo->cantidad = $request->cantidad;
            $recibo->interes = $request->interes;
            $recibo->comprobante = $request->comprobante;
            $recibo->remanente = $remanente;
            $recibo->estado = 2;
            $recibo->save();

            if (($remanente - ($request->cantidad - $request->interes)) == 0) {
                $prestamo = Prestamo::findOrFail($request->prestamo_id);
                $prestamo->estado = 2;
                $prestamo->save();
            }

            alert()->success('El registro ha sido guardado correctamente');
            return back();
        } else {
            alert()->error('El registro no ha sido guardado correctamente');
            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $prestamo = Prestamo::findOrFail($id);

        $count_recibos = $prestamo->recibos->count();

        $capital = 0;
        $interes = 0;
        $fecha_temp = Carbon::createFromFormat('Y-m-d', $prestamo->primer_pago);
        //calculando cuota mensual
        if ($prestamo->tipo_pago_id == 1 || $prestamo->tipo_pago_id == 4) {
            $capital = $prestamo->cantidad / $prestamo->numero_pagos;
            $interes = ($prestamo->cantidad * $prestamo->interes) / 100;


            $remanente = $prestamo->cantidad;
            for ($i = 0; $i < $prestamo->numero_pagos; $i++) {
                $remanente = $remanente - $capital;

                if ($i >= $count_recibos) {
                    $recibo = new Recibo();
                    $recibo->prestamo_id = $prestamo->id;
                    $recibo->fecha = $fecha_temp->format('Y-m-d');
                    $recibo->cantidad = $capital + $interes;
                    $recibo->interes = $interes;
                    $recibo->remanente = $remanente;
                    $recibo->save();
                }


                // Añadir 1 día para pasar al primer día del siguiente mes
                $fecha_temp->addDay();

                // Ajustar la fecha al último día del mes
                $fecha_temp->endOfMonth();
            }
        }
        // Calculando cuota quincenal
        else if ($prestamo->tipo_pago_id == 2) {
            $capital = $prestamo->cantidad / ($prestamo->numero_pagos); // Dividir por 2 para pagos quincenales
            $interes = ($prestamo->cantidad * $prestamo->interes) / 100 / 2; // Dividir interés por 2 para pagos quincenales

            $remanente = $prestamo->cantidad;

            for ($i = 0; $i < $prestamo->numero_pagos; $i++) {
                $remanente = $remanente - $capital;

                if ($i >= $count_recibos) {
                    $recibo = new Recibo();
                    $recibo->prestamo_id = $prestamo->id;
                    $recibo->fecha = $fecha_temp->format('Y-m-d');
                    $recibo->cantidad = $capital + $interes;
                    $recibo->interes = $interes;
                    $recibo->remanente = $remanente;
                    $recibo->save();
                }

                // Establecer la fecha al siguiente día 15
                if ($fecha_temp->day == 15) {
                    // Si es el día 15, avanzar 15 días
                    $fecha_temp->addDays(15);
                } else {
                    // Si no es el día 15, establecer la fecha al último día del mes y avanzar al siguiente día 15
                    $fecha_temp->endOfMonth();
                    $fecha_temp->addDay()->day(15);
                }
            }
        }

        //calculando cuota mensual
        else if ($prestamo->tipo_pago_id == 3) {
            $capital = $prestamo->cantidad / $prestamo->numero_pagos;
            $interes = ($prestamo->cantidad * $prestamo->interes) / 100;
            if ($prestamo->pago_especifico > 0) {
                $interes = $prestamo->pago_especifico - $capital;
            }

            $remanente = $prestamo->cantidad;
            for ($i = 0; $i < $prestamo->numero_pagos; $i++) {
                $remanente = $remanente - $capital;

                if ($i >= $count_recibos) {
                    $recibo = new Recibo();
                    $recibo->prestamo_id = $prestamo->id;
                    $recibo->fecha = $fecha_temp->format('Y-m-d');
                    $recibo->cantidad = $prestamo->pago_especifico;
                    $recibo->interes = $interes;
                    $recibo->remanente = $remanente;
                    $recibo->save();
                }

                $fecha_temp->addMonth(); // Esto agrega un mes a la fecha

            }
        }

        // Calculando cuota quincenal
        else if ($prestamo->tipo_pago_id == 5) {
            $capital = $prestamo->cantidad / ($prestamo->numero_pagos); // Dividir por 2 para pagos quincenales
            $interes = ($prestamo->cantidad * $prestamo->interes) / 100 / 4; // Dividir interés por 4 para pagos semanales

            $remanente = $prestamo->cantidad;

            for ($i = 0; $i < $prestamo->numero_pagos; $i++) {
                $remanente = $remanente - $capital;

                if ($i >= $count_recibos) {
                    $recibo = new Recibo();
                    $recibo->prestamo_id = $prestamo->id;
                    $recibo->fecha = $fecha_temp->format('Y-m-d');
                    $recibo->cantidad = $capital + $interes;
                    $recibo->interes = $interes;
                    $recibo->remanente = $remanente;
                    $recibo->save();
                }

                $fecha_temp->addWeek();
            }
        }


        alert()->success('El registro ha sido guardado correctamente');
        return back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $recibo = Recibo::findOrFail($id);
        return view('prestamo.recibo', compact('recibo'));
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
        $recibo =  Recibo::findOrFail($id);
        $recibo->fecha = $request->fecha;
        $recibo->cantidad = $request->cantidad;
        $recibo->interes =  $request->interes;
        //$recibo->remanente =  $request->remanente;
        $recibo->comprobante =  $request->comprobante;
        $recibo->estado = $request->estado != null ? 2 : 1;
        $recibo->save();



        if ($recibo->remanente == 0.00 && $recibo->estado == 2) {
            $prestamo = Prestamo::findOrFail($recibo->prestamo_id);
            $prestamo->estado = 2;
            $prestamo->save();
        }

        alert()->success('El registro ha sido guardado correctamente');
        return redirect('prestamo_web/' . $recibo->prestamo_id);
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
