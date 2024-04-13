<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\CargoFijo;
use App\Models\Persona;
use App\Models\PrestamoFijo;
use App\Models\ReciboFijo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PrestamoFijoController extends Controller
{
    public function index()
    {
        try {

            $prestamos = DB::table('prestamo_fijo')
                ->join('persona', 'persona.id', '=', 'prestamo_fijo.persona_id')
                ->select(
                    'prestamo_fijo.id',
                    'persona.nombre as persona',
                    'cantidad',
                    DB::raw('DATE_FORMAT(fecha, "%d/%m/%Y") as fecha'),
                    'fecha as fechaDate',
                    DB::raw('LPAD(codigo, 3, "0") as codigo'),
                    'estado',
                    'persona.nombre as comprobante',
                    DB::raw('IFNULL(observacion, "") as observacion')
                )
                ->orderBy('estado','desc')
                ->orderBy('fechaDate','desc')
                ->get();

            foreach ($prestamos as $prestamo) {
                $sum_recibo = ReciboFijo::where('prestamo_fijo_id', $prestamo->id)->sum('cantidad');
                $sum_cargos = CargoFijo::where('prestamo_fijo_id', $prestamo->id)->sum('cantidad');

                $remanente = $prestamo->cantidad + $sum_cargos - $sum_recibo;
                $prestamo->deuda = number_format($remanente, 2, '.', '');
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
            $personas = Persona::select('id', 'nombre')->where('activo', 1)->get();

            // Devolver respuesta JSON con los datos obtenidos
            return response()->json([
                'success' => true,
                'message' => 'datos encontrados',
                'data' => $personas
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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'persona_id' => 'required|integer',
            'cantidad' => 'required|numeric|min:0',
            'fecha' => 'required|date_format:d/m/Y',
            'comprobante' => 'nullable|string', // Dependiendo de cómo manejes los largos textos, podrías necesitar ajustar esto
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
            $max = PrestamoFijo::max('codigo');
            $codigo = is_null($max) ? 1 : $max + 1;

            $fechaCarbon = Carbon::createFromFormat('d/m/Y', $request->fecha);


            $prestamo = new PrestamoFijo();
            $prestamo->persona_id = $request->persona_id;
            $prestamo->cantidad = $request->cantidad;
            $prestamo->fecha = $fechaCarbon->format('Y-m-d');
            $prestamo->comprobante = $request->comprobante;
            $prestamo->observacion = $request->observacion;
            $prestamo->codigo = $codigo;
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
            $prestamo = PrestamoFijo::join('persona', 'persona.id', '=', 'prestamo_fijo.persona_id')
                ->select(
                    'prestamo_fijo.id',
                    'persona.nombre as persona',
                    'cantidad',
                    DB::raw('DATE_FORMAT(fecha, "%d/%m/%Y") as fecha'),
                    DB::raw('LPAD(codigo, 3, "0") as codigo'),
                    'estado',
                    DB::raw('"" as comprobante'),
                    DB::raw('IFNULL(observacion, "") as observacion')
                )->findOrFail($id);

            $sum_recibo = ReciboFijo::where('prestamo_fijo_id', $prestamo->id)->sum('cantidad');
            $sum_cargos = CargoFijo::where('prestamo_fijo_id', $prestamo->id)->sum('cantidad');

            $remanente = $prestamo->cantidad + $sum_cargos - $sum_recibo;
            $prestamo->deuda = number_format($remanente, 2, '.', '');


            $recibosQuery = ReciboFijo::where('prestamo_fijo_id', $id)
                ->select(
                    'id',
                    DB::raw('DATE_FORMAT(fecha, "%d/%m/%Y") AS fecha'),
                    'cantidad',
                    DB::raw('"" as comprobante'),
                    'estado',
                    DB::raw('1 as tipo'),
                    'observacion'
                );

            $cargosQuery = CargoFijo::where('prestamo_fijo_id', $id)
                ->select(
                    'id',
                    DB::raw('DATE_FORMAT(fecha, "%d/%m/%Y") AS fecha'),
                    'cantidad',
                    'comprobante',
                    DB::raw('0 as estado'),
                    DB::raw('2 as tipo'),
                    'observacion'
                );

            $recibos = $recibosQuery->union($cargosQuery)
                ->orderBy('fecha')
                ->get();


            $response = ["prestamo" => $prestamo, "recibos" => $recibos];


            return response()->json([
                'success' => true,
                'message' => 'registros encontrados',
                'data' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el prestamo',
                'error' => $e->getMessage()
            ], 500);
        }
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
