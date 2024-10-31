<?php

namespace App\Http\Controllers\catalogo;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use App\Models\Prestamo;
use App\Models\Recibo;
use App\Models\TipoPago;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class PrestamoCatalogoController extends Controller
{
    public function index()
    {
        $prestamos = Prestamo::get();
        return view('catalogo.prestamo.index', compact('prestamos'));
    }

    public function create()
    {
        $personas = Persona::select('id', 'nombre')->where('activo', 1)->orderBy('nombre')->get();
        $usuarios = User::select('id', 'username')->orderBy('id', 'desc')->get();
        $tipos_pago = TipoPago::get();

        // Obtener la fecha actual
        $fechaActual = Carbon::now();

        // Verificar si la fecha actual es menor al día 15 del mes
        if ($fechaActual->day < 15) {
            // Establecer el 15 del mes como fecha inicial
            $fechaInicial = $fechaActual->copy()->day(15);
        } else {
            // Tomar el último día del mes como fecha inicial
            $fechaInicial = $fechaActual->copy()->endOfMonth();
        }

        $fechaInicial = $fechaInicial->format('Y-m-d');

        return view('catalogo.prestamo.create', compact('personas', 'usuarios', 'tipos_pago', 'fechaInicial'));
    }


    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'persona_id' => 'required|integer',
            'cantidad' => 'required|numeric|min:0',
            'interes' => 'required|integer|min:1',
            'numero_pagos' => 'required|integer|min:0',
            'tipo_pago_id' => 'required|integer',
            'fecha' => 'required',
            //'amortizacion' => 'nullable|boolean',
            'comprobante' => 'nullable|string', // Dependiendo de cómo manejes los largos textos, podrías necesitar ajustar esto
            'administrador' => 'required|integer',
            'pago_especifico' => 'nullable|numeric',
            'observacion' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }


        //try {
        $max = Prestamo::max('codigo');
        $codigo = is_null($max) ? 1 : $max + 1;


        $prestamo = new Prestamo();
        $prestamo->persona_id = $request->persona_id;
        $prestamo->cantidad = $request->cantidad;
        $prestamo->interes = $request->interes;
        $prestamo->tipo_pago_id = $request->tipo_pago_id;
        $prestamo->primer_pago = $request->fecha;
        if ($request->amortizacion == null) {
            $prestamo->amortizacion = 0;
        } else {
            $prestamo->amortizacion = 1;
        }
        $prestamo->comprobante = $request->comprobante;
        $prestamo->administrador = $request->administrador;
        $prestamo->pago_especifico = $request->pago_especifico;
        $prestamo->observacion = $request->observacion;
        $prestamo->codigo = $codigo;
        $prestamo->numero_pagos = $request->numero_pagos;
        $prestamo->save();

        $capital = 0;
        $interes = 0;
        $fecha_temp = Carbon::createFromFormat('Y-m-d', $request->fecha);
        //calculando cuota mensual
        if (($request->tipo_pago_id == 1 || $request->tipo_pago_id == 4) && $request->numero_pagos > 0) {
            $capital = $request->cantidad / $request->numero_pagos;
            $interes = ($request->cantidad * $request->interes) / 100;


            $remanente = $request->cantidad;
            for ($i = 0; $i < $request->numero_pagos; $i++) {
                $remanente = $remanente - $capital;

                $recibo = new Recibo();
                $recibo->prestamo_id = $prestamo->id;
                $recibo->fecha = $fecha_temp->format('Y-m-d');
                $recibo->cantidad = $capital + $interes;
                $recibo->interes = $interes;
                $recibo->remanente = $remanente;
                $recibo->save();

                // Añadir 1 día para pasar al primer día del siguiente mes
                $fecha_temp->addDay();

                // Ajustar la fecha al último día del mes
                $fecha_temp->endOfMonth();
            }
        }
        // Calculando cuota quincenal
        else if ($request->tipo_pago_id == 2) {
            $capital = $request->cantidad / ($request->numero_pagos); // Dividir por 2 para pagos quincenales
            $interes = ($request->cantidad * $request->interes) / 100 / 2; // Dividir interés por 2 para pagos quincenales

            $remanente = $request->cantidad;

            for ($i = 0; $i < $request->numero_pagos; $i++) {
                $remanente = $remanente - $capital;

                $recibo = new Recibo();
                $recibo->prestamo_id = $prestamo->id;
                $recibo->fecha = $fecha_temp->format('Y-m-d');
                $recibo->cantidad = $capital + $interes;
                $recibo->interes = $interes;
                $recibo->remanente = $remanente;
                $recibo->save();

                // Establecer la fecha al siguiente día 15
                if ($fecha_temp->day == 15) {
                    // Si es el día 15, avanzar 15 días
                    $fecha_temp->endOfMonth();
                } else {
                    // Si no es el día 15, establecer la fecha al último día del mes y avanzar al siguiente día 15
                    $fecha_temp->endOfMonth();
                    $fecha_temp->addDay()->day(15);
                }
            }
        }

        //calculando cuota mensual
        else if ($request->tipo_pago_id == 3) {
            $capital = $request->cantidad / $request->numero_pagos;
            $interes = ($request->cantidad * $request->interes) / 100;


            $remanente = $request->cantidad;
            for ($i = 0; $i < $request->numero_pagos; $i++) {
                $remanente = $remanente - $capital;

                $recibo = new Recibo();
                $recibo->prestamo_id = $prestamo->id;
                $recibo->fecha = $fecha_temp->format('Y-m-d');
                $recibo->cantidad = $capital + $interes;
                $recibo->interes = $interes;
                $recibo->remanente = $remanente;
                $recibo->save();

                $fecha_temp->addMonth(); // Esto agrega un mes a la fecha

            }
        }

        // Calculando cuota quincenal
        else if ($request->tipo_pago_id == 5) {
            $capital = $request->cantidad / ($request->numero_pagos); // Dividir por 2 para pagos quincenales
            $interes = ($request->cantidad * $request->interes) / 100 / 4; // Dividir interés por 4 para pagos semanales

            $remanente = $request->cantidad;

            for ($i = 0; $i < $request->numero_pagos; $i++) {
                $remanente = $remanente - $capital;

                $recibo = new Recibo();
                $recibo->prestamo_id = $prestamo->id;
                $recibo->fecha = $fecha_temp->format('Y-m-d');
                $recibo->cantidad = $capital + $interes;
                $recibo->interes = $interes;
                $recibo->remanente = $remanente;
                $recibo->save();

                $fecha_temp->addWeek();
            }
        }

        alert()->success('El registro ha sido creado correctamente');
        return Redirect::to('prestamo_catalogo');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $personas = Persona::select('id', 'nombre')->where('activo', 1)->orderBy('nombre')->get();
        $usuarios = User::select('id', 'username')->orderBy('id', 'desc')->get();
        $tipos_pago = TipoPago::get();

        $prestamo = Prestamo::find($id);

        return view('catalogo.prestamo.edit', compact('prestamo', 'personas', 'usuarios', 'tipos_pago'));
    }

    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'persona_id' => 'required|integer',
            'cantidad' => 'required|numeric|min:0',
            'interes' => 'required|integer|min:1',
            'numero_pagos' => 'required|integer|min:0',
            'tipo_pago_id' => 'required|integer',
            //'amortizacion' => 'nullable|boolean',
            'comprobante' => 'nullable|string', // Dependiendo de cómo manejes los largos textos, podrías necesitar ajustar esto
            'administrador' => 'required|integer',
            'pago_especifico' => 'nullable|numeric',
            'observacion' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $prestamo = Prestamo::findOrFail($id);
        $prestamo->persona_id = $request->persona_id;
        $prestamo->cantidad = $request->cantidad;
        $prestamo->interes = $request->interes;
        $prestamo->tipo_pago_id = $request->tipo_pago_id;
        if ($request->amortizacion == null) {
            $prestamo->amortizacion = 0;
        } else {
            $prestamo->amortizacion = 1;
        }
        if ($request->estado == null) {
            $prestamo->estado = 1;
        } else {
            $prestamo->estado = 2;
        }
        if ($request->comprobante != "") {
            $prestamo->comprobante = $request->comprobante;
        }
        $prestamo->administrador = $request->administrador;
        $prestamo->pago_especifico = $request->pago_especifico;
        $prestamo->observacion = $request->observacion;
        $prestamo->codigo = $request->codigo;
        $prestamo->numero_pagos = $request->numero_pagos;
        $prestamo->save();

        alert()->success('El registro ha sido modificado correctamente');
        return Redirect::to('prestamo_catalogo');
    }

    public function destroy($id)
    {
        $recibos = Recibo::where('prestamo_id', $id)->count();
        if ($recibos > 0) {
            alert()->error('El prestamo cuenta con recibos');
            return back();
        }

        $prestamo = Prestamo::findOrFail($id);
        $prestamo->delete();

        alert()->success('El registro ha sido modificado correctamente');
        return Redirect::to('prestamo_catalogo');
    }
}
