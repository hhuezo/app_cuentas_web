<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Cargo;
use App\Models\Persona;
use App\Models\Prestamo;
use App\Models\Recibo;
use App\Models\TipoPago;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PrestamoWebController extends Controller
{
    public function index()
    {
        $prestamos = Prestamo::orderBy('estado')->orderBy('primer_pago', 'desc')->get();
        return view('prestamo.index', compact('prestamos'));
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

        return view('prestamo.create', compact('personas', 'usuarios', 'tipos_pago', 'fechaInicial'));
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
        if($request->amortizacion == null){
            $prestamo->amortizacion = 0;
        }
        else{
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
        if (($request->tipo_pago_id == 1 || $request->tipo_pago_id == 4) && $request->numero_pagos > 0 ) {
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
        return back();
        // } catch (\Exception $e) {
        //     alert()->error('Error no se pudo guardar el registro');
        //     return back();
        // }
    }

    public function show($id)
    {
        $prestamo = Prestamo::findOrFail($id);

        $recibo = Recibo::where('prestamo_id', $id)->orderBy('id', 'desc')->where('estado', 2)->first();
        if ($recibo) {
            $prestamo->remanente = $recibo->remanente;
        } else {
            $prestamo->remanente = $prestamo->cantidad;
        }



        $cargo = Cargo::where('prestamo_id', $id)->orderBy('id', 'desc')->first();
        if ($cargo && $recibo && $cargo->fecha > $recibo->fecha) {
            $prestamo->remanente = $cargo->saldo;
        }

        $recibosQuery = Recibo::where('prestamo_id', $prestamo->id)
            ->select(
                'id',
                //DB::raw('DATE_FORMAT(fecha, "%d/%m/%Y") AS fecha'),
                'cantidad',
                DB::raw('"" as comprobante'),
                'interes',
                'remanente',
                'estado',
                DB::raw('1 as tipo'),
                DB::raw('"" as observacion'),
                'fecha'
            );

        $cargosQuery = Cargo::where('prestamo_id', $prestamo->id)
            ->select(
                'id',
                //DB::raw('DATE_FORMAT(fecha, "%d/%m/%Y") AS fecha'),
                'cantidad',
                DB::raw('"" as comprobante'),
                DB::raw('0 as interes'),
                DB::raw('saldo as remanente'),
                DB::raw('0 as estado'),
                DB::raw('2 as tipo'),
                'observacion',
                'fecha'
            );

        $resultados = $recibosQuery->union($cargosQuery)
            ->orderBy('fecha', 'desc')
            ->get();

        $saldo = 0;
        foreach ($resultados as $resultado) {
            if ($resultado->tipo == 1) {
                $saldo = $resultado->remanente;
            } else {
                if ($resultado->remanente == 0) {
                    $saldo =  $saldo + $resultado->cantidad;
                    $resultado->remanente = $saldo;
                } else {
                    $saldo = $resultado->remanente;
                }
            }
        }

        // dd($resultados);

        $recibos = $resultados;
        return view('prestamo.show', compact('prestamo', 'recibos'));
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
