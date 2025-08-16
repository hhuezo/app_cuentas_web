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
            // Obtener parámetros con valores por defecto
            $rol = $request->get('rol', 1);
            $id_usuario = $request->get('id_usuario', 1);
            $search = $request->get('search', '');

            // Consulta principal
            $prestamos = DB::table('prestamo')
                ->select(
                    'prestamo.id',
                    DB::raw('LPAD(prestamo.codigo, 3, "0") as codigo'),
                    DB::raw('FORMAT(prestamo.cantidad, 2) as cantidad'),
                    DB::raw('FORMAT(prestamo.interes, 2) as interes'),
                    'prestamo.estado',
                    DB::raw('IFNULL(prestamo.amortizacion,0.00) as amortizacion'),
                    DB::raw('"" as comprobante'),
                    DB::raw('"" as administrador'),
                    'persona.nombre as persona',
                    'tipo_pago.nombre as tipoPago',
                    'prestamo.tipo_pago_id',
                    'prestamo.numero_pagos',
                    DB::raw('IFNULL(prestamo.pago_especifico, 0) as pago_especifico'),
                    DB::raw('IFNULL(prestamo.observacion, "") as observacion'),
                    DB::raw('DATE_FORMAT(prestamo.fecha, "%d/%m/%Y") as fecha'),
                    DB::raw('IFNULL((SELECT remanente FROM recibo WHERE recibo.prestamo_id = prestamo.id and recibo.estado = 2  ORDER BY recibo.id DESC LIMIT 1), prestamo.cantidad) AS deuda'),
                    DB::raw('ROUND((prestamo.cantidad / prestamo.numero_pagos) + (prestamo.cantidad * (prestamo.interes / 100) * IF(prestamo.tipo_pago_id = 2, 0.5, IF(prestamo.tipo_pago_id = 5, 0.25, 1))), 2) AS cuota')
                )
                ->leftJoin('persona', 'prestamo.persona_id', '=', 'persona.id')
                ->leftJoin('tipo_pago', 'prestamo.tipo_pago_id', '=', 'tipo_pago.id')
                ->when($rol > 1, fn($query) => $query->where('administrador', $id_usuario))
                ->when($search, fn($query) => $query->where('persona.nombre', 'like', "%$search%"))
                ->orderBy('prestamo.estado')
                ->orderBy('prestamo.fecha', 'desc')
                ->get();

            // Ajuste de cuotas
            foreach ($prestamos as $prestamo) {
                $prestamo->cuota = $prestamo->pago_especifico > 0
                    ? $prestamo->pago_especifico
                    : ($prestamo->cuota ?? "0.00");
            }

            return response()->json([
                'success' => true,
                'message' => 'Préstamos encontrados',
                'data' => $prestamos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los préstamos',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function create()
    {
        try {
            $personas = Persona::select('id', 'nombre')->where('activo', 1)->orderBy("nombre")->get();
            $usuarios = User::select('id', 'username')->orderBy('id', 'desc')->get();
            $tipos_pago = TipoPago::get();

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
            'fecha' => 'required|date_format:d/m/Y',
            'amortizacion' => 'nullable|boolean',
            'comprobante' => 'nullable|string',
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
            DB::beginTransaction(); // Iniciar transacción

            $max = Prestamo::max('codigo');
            $codigo = is_null($max) ? 1 : $max + 1;

            $fechaCarbon = Carbon::createFromFormat('d/m/Y', $request->fecha);
            $imageName = null;

            // Procesamiento de la imagen
            if (!is_null($request->comprobante)) {
                if (preg_match('/^data:image\/(\w+);base64,/', $request->comprobante, $matches)) {
                    $extension = $matches[1];
                    $allowedExtensions = ['png', 'jpg', 'jpeg', 'gif', 'webp'];

                    if ($extension === 'jpeg') {
                        $extension = 'jpg';
                    }

                    if (!in_array($extension, $allowedExtensions)) {
                        throw new \Exception("Formato de imagen no permitido. Use: " . implode(', ', $allowedExtensions));
                    }

                    $imageData = explode(',', $request->comprobante)[1];
                    $imageBinary = base64_decode($imageData);

                    if ($imageBinary === false) {
                        throw new \Exception("Error al decodificar la imagen Base64");
                    }

                    if (@imagecreatefromstring($imageBinary) === false) {
                        throw new \Exception("Los datos no corresponden a una imagen válida");
                    }

                    if (!file_exists(public_path('comprobantes'))) {
                        mkdir(public_path('comprobantes'), 0755, true);
                    }

                    $imageName = 'prestamo_' . $codigo . '.' . $extension;
                    $imagePath = public_path('comprobantes/' . $imageName);

                    if (file_put_contents($imagePath, $imageBinary) === false) {
                        throw new \Exception("Error al guardar la imagen en el servidor");
                    }
                } else {
                    throw new \Exception("El formato de la imagen debe ser un Data URI válido (data:image/...)");
                }
            }

            // Creación del préstamo
            $prestamo = new Prestamo();
            $prestamo->persona_id = $request->persona_id;
            $prestamo->cantidad = $request->cantidad;
            $prestamo->interes = $request->interes;
            $prestamo->tipo_pago_id = $request->tipo_pago_id;
            $prestamo->primer_pago = $fechaCarbon->format('Y-m-d');
            $prestamo->amortizacion = $request->amortizacion;
            $prestamo->comprobante_url = $imageName;
            $prestamo->administrador = $request->administrador;
            $prestamo->pago_especifico = $request->pago_especifico;
            $prestamo->observacion = $request->observacion;
            $prestamo->codigo = $codigo;
            $prestamo->numero_pagos = $request->numero_pagos;
            $prestamo->save();

            // Generación de recibos según tipo de pago
            $capital = 0;
            $interes = 0;
            $fecha_temp = Carbon::createFromFormat('Y-m-d', $fechaCarbon->format('Y-m-d'));
            $remanente = $request->cantidad;

            switch ($request->tipo_pago_id) {
                case 1: // Mensual (último día del mes)
                    if ($request->numero_pagos > 0) {
                        $capital = $request->cantidad / $request->numero_pagos;
                        $interes = ($request->cantidad * $request->interes) / 100;

                        for ($i = 0; $i < $request->numero_pagos; $i++) {
                            $remanente -= $capital;
                            $this->crearRecibo($prestamo->id, $fecha_temp, $capital, $interes, $remanente);
                            $fecha_temp->addMonthsNoOverflow();
                        }
                    }
                    break;
                case 4: // Otro tipo mensual
                    if ($request->numero_pagos > 0) {
                        $capital = $request->cantidad / $request->numero_pagos;
                        $interes = ($request->cantidad * $request->interes) / 100;

                        for ($i = 0; $i < $request->numero_pagos; $i++) {
                            $remanente -= $capital;
                            $this->crearRecibo($prestamo->id, $fecha_temp, $capital, $interes, $remanente);
                            $fecha_temp->addDay()->endOfMonth();
                        }
                    }
                    break;

                case 2: // Quincenal (días 15 y último día del mes)
                    if ($request->numero_pagos > 0) {
                        $capital = $request->cantidad / $request->numero_pagos;
                        $interes = ($request->cantidad * $request->interes) / 100 / 2;

                        for ($i = 0; $i < $request->numero_pagos; $i++) {
                            $remanente -= $capital;
                            $this->crearRecibo($prestamo->id, $fecha_temp, $capital, $interes, $remanente);

                            if ($fecha_temp->day == 15) {
                                $fecha_temp->endOfMonth();
                            } else {
                                $fecha_temp->endOfMonth()->addDay()->day(15);
                            }
                        }
                    }
                    break;

                case 3: // Mensual (mismo día cada mes)
                case 3: // Mensual (mismo día cada mes)
                    if ($request->numero_pagos > 0) {
                        $capital = $request->cantidad / $request->numero_pagos;
                        $interes = ($request->cantidad * $request->interes) / 100;

                        for ($i = 0; $i < $request->numero_pagos; $i++) {
                            $remanente -= $capital;
                            $this->crearRecibo($prestamo->id, $fecha_temp, $capital, $interes, $remanente);
                            $fecha_temp->addMonth();
                        }
                    }
                    break;

                case 5: // Semanal
                    if ($request->numero_pagos > 0) {
                        $capital = $request->cantidad / $request->numero_pagos;
                        $interes = ($request->cantidad * $request->interes) / 100 / 4;

                        for ($i = 0; $i < $request->numero_pagos; $i++) {
                            $remanente -= $capital;
                            $this->crearRecibo($prestamo->id, $fecha_temp, $capital, $interes, $remanente);
                            $fecha_temp->addWeek();
                        }
                    }
                    break;
            }

            DB::commit(); // Confirmar transacción si todo fue exitoso

            return response()->json([
                'success' => true,
                'message' => 'Préstamo creado exitosamente',
                'data' => $prestamo
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // Revertir transacción en caso de error

            // Eliminar la imagen si se creó pero falló la transacción
            if (isset($imageName) && file_exists(public_path('comprobantes/' . $imageName))) {
                unlink(public_path('comprobantes/' . $imageName));
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al crear el préstamo',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    private function crearRecibo($prestamoId, $fecha, $capital, $interes, $remanente)
    {
        $recibo = new Recibo();
        $recibo->prestamo_id = $prestamoId;
        $recibo->fecha = $fecha->format('Y-m-d');
        $recibo->cantidad = $capital + $interes;
        $recibo->interes = $interes;
        $recibo->remanente = $remanente;
        $recibo->save();

        return $recibo;
    }

    public function show($id)
    {
        try {

            $prestamo = Prestamo::select(
                'prestamo.id',
                DB::raw("LPAD(prestamo.codigo, 4, '0') AS codigo"),
                'persona.nombre AS persona',
                'prestamo.cantidad',
                DB::raw("CAST(prestamo.interes AS CHAR) AS interes"),
                'prestamo.estado',
                DB::raw("ifnull(prestamo.amortizacion,'') AS amortizacion"),
                DB::raw("ifnull(prestamo.pago_especifico,0.00) AS pago_especifico"),
                DB::raw("DATE_FORMAT(prestamo.fecha, '%d/%m/%Y') AS fecha"),
                'tipo_pago.nombre AS tipo'
            )
                ->join('persona', 'prestamo.persona_id', '=', 'persona.id')
                ->join('tipo_pago', 'prestamo.tipo_pago_id', '=', 'tipo_pago.id')
                ->where('prestamo.id', $id)
                ->first();



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
                ->where('estado', 2)
                ->select(
                    'id',
                    DB::raw('DATE_FORMAT(fecha, "%d/%m/%Y") AS fecha'),
                    'cantidad',
                    DB::raw('"" as comprobante'),
                    'interes',
                    'remanente',
                    'estado',
                    DB::raw('1 as tipo'),
                    DB::raw('"" as observacion'),
                    'fecha as fechaDate'
                );

            $cargosQuery = Cargo::where('prestamo_id', $prestamo->id)
                ->select(
                    'id',
                    DB::raw('DATE_FORMAT(fecha, "%d/%m/%Y") AS fecha'),
                    'cantidad',
                    DB::raw('"" as comprobante'),
                    DB::raw('0 as interes'),
                    DB::raw('saldo as remanente'),
                    DB::raw('0 as estado'),
                    DB::raw('2 as tipo'),
                    DB::raw('ifnull(observacion,"") as observacion'),
                    'fecha as fechaDate'
                );

            $resultados = $recibosQuery->union($cargosQuery)
                ->orderBy('fechaDate', 'desc')
                ->get();

            $saldo = 0;
            foreach ($resultados as $resultado) {
                if ($resultado->tipo == 1) {
                    $saldo = $resultado->remanente . "";
                } else {
                    if ($resultado->remanente == 0) {
                        $saldo =  $saldo + $resultado->cantidad;
                        $resultado->remanente = $saldo . "";
                    } else {
                        $saldo = $resultado->remanente . "";
                    }
                }
            }

            //
            $response = ["prestamo" => $prestamo, "recibos" => $resultados];

            return response()->json([
                'success' => true,
                'message' => 'Prestamos encontrados',
                'data' => $response
            ]);


            $prestamo = Prestamo::findOrFail($id);
            $personas = Persona::select('id', 'nombre')->where('activo', 1)->get();
            $usuarios = User::select('id', 'username')->orderBy('id', 'desc')->get();
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
