<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use App\Models\Prestamo;
use App\Models\TipoPago;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            $personas = Persona::select('id','nombre')->where('activo', 1)->get();
            $usuarios = User::select('id','username')->get();
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
        return response()->json([
            'success' => true,
            'message' => 'Prestamos encontrados',
            'data' => "hola"
        ]);
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
