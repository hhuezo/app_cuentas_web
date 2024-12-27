<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\CargoFijo;
use App\Models\Persona;
use App\Models\PrestamoFijo;
use App\Models\ReciboFijo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class PrestamoFijoWebController extends Controller
{

    public function index()
    {
        $prestamos = PrestamoFijo::orderBy('estado')->orderBy('fecha', 'desc')->orderBy('codigo', 'desc')->get();
        return view('prestamo_fijo.index', compact('prestamos'));
    }

    public function create()
    {
        $personas = Persona::select('id', 'nombre')->where('activo', 1)->orderBy('nombre')->get();
        return view('prestamo_fijo.create', compact('personas',));
    }

    public function store(Request $request)
    {
        $max = PrestamoFijo::max('codigo');
        $codigo = is_null($max) ? 1 : $max + 1;

        $prestamo = new PrestamoFijo();
        $prestamo->persona_id = $request->persona_id;
        $prestamo->cantidad = $request->cantidad;
        //$prestamo->comprobante = $request->comprobante;
        $prestamo->fecha = $request->fecha;
        $prestamo->codigo = $codigo;
        $prestamo->estado = 1;
        $prestamo->observacion = $request->observacion;
        $prestamo->save();


        // Verificar si el comprobante existe y no está vacío
        if ($request->has('comprobante') && $request->comprobante) {
            // Buscar el préstamo

            if (!$prestamo) {
                return response()->json(['error' => 'Préstamo no encontrado'], 404);
            }

            // Ruta donde se guardará el archivo
            $fileName = 'prestamo_fijo_' . $prestamo->id . '.jpg';
            $filePath = public_path('comprobantes/' . $fileName);

            // Decodificar el Base64 y guardar el archivo
            try {
                // Remover el prefijo "data:image/jpeg;base64," si existe
                $base64Image = preg_replace('/^data:image\/\w+;base64,/', '', $request->comprobante);

                // Decodificar el Base64
                $imageData = base64_decode($base64Image);

                // Verificar si la decodificación fue exitosa
                if ($imageData === false) {
                    throw new Exception('La imagen no pudo ser decodificada');
                }

                // Guardar el archivo en la carpeta public/comprobantes
                file_put_contents($filePath, $imageData);

                // Actualizar el registro del préstamo
                $prestamo->comprobante_url = $fileName;
                $prestamo->save();
            } catch (Exception $e) {
                // Loguear errores para depuración
                Log::error('Error al guardar el comprobante: ' . $e->getMessage());
            }
        }

        alert()->success('El registro ha sido creado correctamente');
        return Redirect::to('prestamo_fijo_web/');
    }

    public function show($id)
    {
        $prestamo = PrestamoFijo::findOrFail($id);
        $sum_recibos = ReciboFijo::where('prestamo_fijo_id', $id)->sum('cantidad');
        $sum_cargos = CargoFijo::where('prestamo_fijo_id', $id)->sum('cantidad');
        $deuda = $prestamo->cantidad + $sum_cargos - $sum_recibos;

        return view('prestamo_fijo.show', compact('prestamo', 'deuda'));
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
