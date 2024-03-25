<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use App\Models\Prestamo;
use App\Models\TipoPago;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PrestamoController extends Controller
{
    public function index()
    {
        try {
            $prestamos = Prestamo::select(
                'prestamo.id',
                DB::raw('LPAD(prestamo.codigo, 3, "0") as codigo'),
                DB::raw('FORMAT(prestamo.cantidad, 2) as cantidad'), // Formatea como decimal con 2 decimales
                DB::raw('FORMAT(prestamo.interes, 2) as interes'), // Igualmente para interes
                'prestamo.estado',
                'prestamo.amortizacion',
                'prestamo.comprobante',
                'prestamo.administrador',
                'prestamo.pago_especifico as pagoEspecifico',
                'persona.nombre as persona',
                'tipo_pago.nombre as tipoPago',
                DB::raw('ifnull(prestamo.observacion,"") as observacion'),
                DB::raw('DATE_FORMAT(prestamo.fecha, "%d/%m/%Y") as fecha')
            )
                ->join('persona', 'prestamo.persona_id', '=', 'persona.id')
                ->join('tipo_pago', 'prestamo.tipo_pago_id', '=', 'tipo_pago.id')
                ->get();


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
            $usuarios = User::select('id', 'username')->get();
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
            'tipo_pago_id' => 'required|integer',
            'fecha' => 'required|date_format:d/m/Y',
            'amortizacion' => 'sometimes|in:0,1', // Asumiendo que los valores pueden ser '0' o '1'
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

            $fechaCarbon = Carbon::createFromFormat('d/m/Y', $request->fecha);

            $prestamo = new Prestamo();
            $prestamo->persona_id = $request->persona_id;
            $prestamo->cantidad = $request->cantidad;
            $prestamo->interes = $request->interes;
            $prestamo->tipo_pago_id = $request->tipo_pago_id;
            $prestamo->fecha = $fechaCarbon->format('Y-m-d');
            $prestamo->amortizacion = $request->amortizacion;
            $prestamo->comprobante = $request->comprobante;
            $prestamo->administrador = $request->administrador;
            $prestamo->pago_especifico = $request->pago_especifico;
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
        //
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
