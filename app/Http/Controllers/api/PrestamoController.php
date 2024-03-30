<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
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
    public function index()
    {
        try {
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

            foreach ($prestamos as $prestamo) {

                if ($prestamo->pago_especifico ) {
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
            'numero_pagos' => 'required|integer|min:1',
            'tipo_pago_id' => 'required|integer',
            'fecha' => 'required|date_format:d/m/Y',
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

            $fechaCarbon = Carbon::createFromFormat('d/m/Y', $request->fecha);


            $prestamo = new Prestamo();
            $prestamo->persona_id = $request->persona_id;
            $prestamo->cantidad = $request->cantidad;
            $prestamo->interes = $request->interes;
            $prestamo->tipo_pago_id = $request->tipo_pago_id;
            $prestamo->primer_pago = $fechaCarbon->format('Y-m-d');
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

            $recibo = Recibo::where('prestamo_id', $id)->orderBy('id', 'desc')->first();
            if($recibo)
            {
                $prestamo->remanente = $recibo->remanente;
            }
            else{
                $prestamo->remanente = $prestamo->cantidad;
            }

            $recibos = Recibo::where('prestamo_id', $prestamo->id)
                ->select(
                    'id',
                    DB::raw('DATE_FORMAT(fecha, "%d/%m/%Y") AS fecha'),
                    'cantidad',
                    'comprobante',
                    'interes',
                    'remanente',
                    'estado'
                )->get();


            $response = ["prestamo" => $prestamo, "recibos" => $recibos];

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

    /*





    <androidx.cardview.widget.CardView
        android:layout_width="match_parent"
        android:layout_height="wrap_content"
        android:layout_margin="8dp"
        app:cardCornerRadius="8dp">

        <ScrollView
            android:layout_width="match_parent"
            android:layout_height="match_parent">

            <LinearLayout
                android:layout_width="match_parent"
                android:layout_height="wrap_content"
                android:orientation="vertical"
                android:padding="16dp">

                <TextView
                    android:layout_width="wrap_content"
                    android:layout_height="wrap_content"
                    android:layout_gravity="center_horizontal"
                    android:text="Préstamo"
                    android:textSize="24sp"
                    android:textStyle="bold" />

                <Space
                    android:layout_width="match_parent"
                    android:layout_height="16dp" />


                <TextView
                    android:layout_width="wrap_content"
                    android:layout_height="wrap_content"
                    android:text="Comprobante"
                    android:textSize="14sp" />

                <ImageView
                    android:id="@+id/comprobanteImageView"
                    android:layout_width="250dp"
                    android:layout_height="150dp"
                    android:layout_gravity="center"
                    android:layout_marginTop="8dp"
                    android:background="@android:drawable/ic_menu_gallery"
                    android:contentDescription="comprobante"
                    android:scaleType="centerCrop" />

                <Button
                    android:id="@+id/aceptarButton"
                    android:layout_width="match_parent"
                    android:layout_height="64dp"
                    android:layout_marginTop="16dp"
                    android:text="Aceptar" />

            </LinearLayout>
        </ScrollView>
    </androidx.cardview.widget.CardView>



    */
}
