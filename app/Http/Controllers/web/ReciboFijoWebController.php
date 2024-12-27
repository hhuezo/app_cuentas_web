<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\PrestamoFijo;
use App\Models\ReciboFijo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class ReciboFijoWebController extends Controller
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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

        $recibo = new ReciboFijo();
        $recibo->prestamo_fijo_id = $request->prestamo_fijo_id;
        $recibo->fecha = $request->fecha;
        $recibo->cantidad = $request->cantidad;
        $recibo->observacion = $request->observacion;
        //$recibo->comprobante = $request->comprobante;
        $recibo->estado = 2;
        $recibo->save();



             // Verificar si el comprobante existe y no está vacío
             if ($request->has('comprobante') && $request->comprobante) {
                // Buscar el préstamo

                // Ruta donde se guardará el archivo
                $fileName = 'recibo_fijo_' . $recibo->id . '.jpg';
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
                    $recibo->comprobante_url = $fileName;
                    $recibo->save();
                } catch (Exception $e) {
                    // Loguear errores para depuración
                    Log::error('Error al guardar el comprobante: ' . $e->getMessage());
                }
            }



        if ($request->remanente == $request->cantidad) {
            $prestamo = PrestamoFijo::findOrFail($request->prestamo_fijo_id);
            $prestamo->estado = 2;
            $prestamo->save();
        }

        alert()->success('El registro ha sido guardado correctamente');
        return Redirect::to('prestamo_fijo_web/'.$request->prestamo_fijo_id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
