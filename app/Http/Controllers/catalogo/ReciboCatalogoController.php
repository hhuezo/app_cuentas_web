<?php

namespace App\Http\Controllers\catalogo;

use App\Http\Controllers\Controller;
use App\Models\Prestamo;
use App\Models\Recibo;
use Illuminate\Http\Request;

class ReciboCatalogoController extends Controller
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
    public function create($id)
    {
        $prestamo = Prestamo::find($id);        
        return view('catalogo.recibo.create', compact('prestamo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $recibo = new Recibo();
        $recibo->prestamo_id = $request->prestamo_id;
        $recibo->fecha = $request->fecha;
        $recibo->cantidad = $request->cantidad;
        $recibo->comprobante = $request->comprobante;
        $recibo->interes = $request->interes;
        $recibo->remanente = $request->remanente;
        if ($request->estado == null) {
            $recibo->estado = 1;
        } else {
            $recibo->estado = 2;
        }
        $recibo->saldo = $request->saldo;
        $recibo->save();

        alert()->info('El registro ha sido creado correctamente');
        return redirect('prestamo_catalogo/' . $request->prestamo_id . '/edit');
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
