<?php

namespace App\Http\Controllers\api;

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

class PrestamoController extends Controller
{
    public function index(Request $request)
    {
        try {
            $rol = 1;
            $id_usuario = 1;
            if ($request->rol) {
                $rol = $request->rol;
            }

            if ($request->rol) {
                $id_usuario = $request->id_usuario;
            }

            if ($rol == 1) {
                $prestamos = DB::table('prestamo')
                    ->select(
                        'prestamo.id',
                        DB::raw('LPAD(prestamo.codigo, 3, "0") as codigo'),
                        DB::raw('FORMAT(prestamo.cantidad, 2) as cantidad'),
                        DB::raw('FORMAT(prestamo.interes, 2) as interes'),
                        'prestamo.estado',
                        'prestamo.amortizacion',
                        'prestamo.id as comprobante',
                        'prestamo.administrador',
                        'prestamo.pago_especifico as pagoEspecifico',
                        'persona.nombre as persona',
                        'tipo_pago.nombre as tipoPago',
                        'prestamo.tipo_pago_id',
                        'prestamo.numero_pagos',
                        'prestamo.pago_especifico',
                        DB::raw('IFNULL(prestamo.observacion, "") as observacion'),
                        DB::raw('DATE_FORMAT(prestamo.fecha, "%d/%m/%Y") as fecha'),
                        DB::raw('IFNULL((SELECT remanente FROM recibo WHERE recibo.prestamo_id = prestamo.id ORDER BY recibo.id DESC LIMIT 1), prestamo.cantidad) AS deuda'),
                        DB::raw('ROUND((prestamo.cantidad / prestamo.numero_pagos) + (prestamo.cantidad * (prestamo.interes / 100) * IF(prestamo.tipo_pago_id = 2, 0.5, 1)), 2) AS cuota')
                    )
                    ->join('persona', 'prestamo.persona_id', '=', 'persona.id')
                    ->join('tipo_pago', 'prestamo.tipo_pago_id', '=', 'tipo_pago.id')
                    ->orderBy('prestamo.estado')
                    ->get();
            } else {
                $prestamos = DB::table('prestamo')
                    ->select(
                        'prestamo.id',
                        DB::raw('LPAD(prestamo.codigo, 3, "0") as codigo'),
                        DB::raw('FORMAT(prestamo.cantidad, 2) as cantidad'),
                        DB::raw('FORMAT(prestamo.interes, 2) as interes'),
                        'prestamo.estado',
                        'prestamo.amortizacion',
                        'prestamo.id as comprobante',
                        'prestamo.administrador',
                        'prestamo.pago_especifico as pagoEspecifico',
                        'persona.nombre as persona',
                        'tipo_pago.nombre as tipoPago',
                        'prestamo.tipo_pago_id',
                        'prestamo.numero_pagos',
                        'prestamo.pago_especifico',
                        DB::raw('IFNULL(prestamo.observacion, "") as observacion'),
                        DB::raw('DATE_FORMAT(prestamo.fecha, "%d/%m/%Y") as fecha'),
                        DB::raw('IFNULL((SELECT remanente FROM recibo WHERE recibo.prestamo_id = prestamo.id ORDER BY recibo.id DESC LIMIT 1), prestamo.cantidad) AS deuda'),
                        DB::raw('ROUND((prestamo.cantidad / prestamo.numero_pagos) + (prestamo.cantidad * (prestamo.interes / 100) * IF(prestamo.tipo_pago_id = 2, 0.5, 1)), 2) AS cuota')
                    )
                    ->join('persona', 'prestamo.persona_id', '=', 'persona.id')
                    ->join('tipo_pago', 'prestamo.tipo_pago_id', '=', 'tipo_pago.id')
                    ->where('administrador', $id_usuario)
                    ->orderBy('prestamo.estado')
                    ->get();
            }


            foreach ($prestamos as $prestamo) {

                if ($prestamo->pago_especifico) {
                    $prestamo->cuota = $prestamo->pago_especifico;
                }
            }




            // Devolver respuesta JSON con los datos obtenidos
            return response()->json([
                'success' => true,
                'message' => 'Prestamos encontrados',
                'data' => $prestamos
            ]);
        } catch (\Exception $e) {
            // Devolver respuesta JSON en caso de error
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los prestamos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function create()
    {
        try {
            $personas = Persona::select('id as value', 'nombre as label')->where('activo', 1)->get();
            $usuarios = User::select('id as value', 'username as label')->get();
            $tipos_pago = TipoPago::select('id as value', 'nombre as label')->get();

            $response = ["personas" => $personas, "usuarios" => $usuarios, "tipos_pago" => $tipos_pago];
            // Devolver respuesta JSON con los datos obtenidos
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


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'persona_id' => 'required|integer',
            'cantidad' => 'required|numeric|min:0',
            'interes' => 'required|integer|min:0',
            'numero_pagos' => 'required|integer|min:1',
            'tipo_pago_id' => 'required|integer',
            'fecha' => 'required',
            'amortizacion' => 'nullable|boolean',
            'comprobante' => 'nullable|string', // Dependiendo de cómo manejes los largos textos, podrías necesitar ajustar esto
            'administrador' => 'required|integer',
            'pago_especifico' => 'nullable|numeric|min:0',
            'observacion' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $max = Prestamo::max('codigo');
            $codigo = is_null($max) ? 1 : $max + 1;



            $prestamo = new Prestamo();
            $prestamo->persona_id = $request->persona_id;
            $prestamo->cantidad = $request->cantidad;
            $prestamo->interes = $request->interes;
            $prestamo->tipo_pago_id = $request->tipo_pago_id;
            $prestamo->primer_pago = $request->fecha;
            $prestamo->amortizacion = $request->amortizacion;
            $prestamo->comprobante = $request->comprobante;
            $prestamo->administrador = $request->administrador;
            $prestamo->pago_especifico = $request->pago_especifico;
            $prestamo->observacion = $request->observacion;
            $prestamo->codigo = $codigo;
            $prestamo->numero_pagos = $request->numero_pagos;
            $prestamo->save();

            return response()->json([
                'success' => true,
                'message' => 'Prestamo creado exitosamente',
                'data' => $prestamo
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

            $prestamo = Prestamo::select(
                'prestamo.id',
                DB::raw("LPAD(prestamo.codigo, 4, '0') AS codigo"),
                'persona.nombre AS persona',
                'prestamo.cantidad',
                'prestamo.interes',
                'prestamo.estado',
                'prestamo.pago_especifico',
                DB::raw("DATE_FORMAT(prestamo.fecha, '%d/%m/%Y') AS fecha"),
                'tipo_pago.nombre AS tipo'
            )
                ->join('persona', 'prestamo.persona_id', '=', 'persona.id')
                ->join('tipo_pago', 'prestamo.tipo_pago_id', '=', 'tipo_pago.id')
                ->where('prestamo.id', $id)
                ->first();

            $recibo = Recibo::select('id', 'prestamo_id', 'fecha', 'cantidad', 'interes', 'remanente', 'estado')
                ->where('prestamo_id', $id)
                ->orderBy('id', 'desc')
                ->first();
            if ($recibo) {
                $prestamo->remanente = $recibo->remanente;
            } else {
                $prestamo->remanente = $prestamo->cantidad;
            }

            $recibosQuery = Recibo::where('prestamo_id', $prestamo->id)
                ->select(
                    'id',
                    DB::raw('DATE_FORMAT(fecha, "%d/%m/%Y") AS fecha'),
                    'cantidad',
                    'interes',
                    'remanente',
                    DB::raw('"" as observacion'),
                    'estado',
                    DB::raw('1 as tipo')
                );

            $cargosQuery = Cargo::where('prestamo_id', $prestamo->id)
                ->select(
                    'id',
                    DB::raw('DATE_FORMAT(fecha, "%d/%m/%Y") AS fecha'),
                    'cantidad',
                    'observacion',
                    DB::raw('0 as interes'),
                    DB::raw('0 as remanente'),
                    DB::raw('0 as estado'),
                    DB::raw('2 as tipo')
                );

            $resultados = $recibosQuery->union($cargosQuery)
                ->orderBy('fecha')
                ->get();


            $response = ["prestamo" => $prestamo, "recibos" => $resultados];

            return response()->json([
                'success' => true,
                'message' => 'Prestamos encontrados',
                'data' => $response
            ]);


            $prestamo = Prestamo::findOrFail($id);
            $personas = Persona::select('id', 'nombre')->where('activo', 1)->get();
            $usuarios = User::select('id', 'username')->get();
            $tipos_pago = TipoPago::get();

            $response = ["prestamo" => $prestamo, "personas" => $personas, "usuarios" => $usuarios, "tipos_pago" => $tipos_pago];
            // Devolver respuesta JSON con los datos obtenidos
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
        try {
            $prestamo = Prestamo::findOrFail($id);
            $personas = Persona::select('id', 'nombre')->where('activo', 1)->get();
            $usuarios = User::select('id', 'username')->get();
            $tipos_pago = TipoPago::get();

            $response = ["prestamo" => $prestamo, "personas" => $personas, "usuarios" => $usuarios, "tipos_pago" => $tipos_pago];
            // Devolver respuesta JSON con los datos obtenidos
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

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }


}
