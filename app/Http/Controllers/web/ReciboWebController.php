<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Recibo;
use Illuminate\Http\Request;

class ReciboWebController extends Controller
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
        //
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
        $recibo = Recibo::findOrFail($id);
        return view('prestamo.recibo', compact('recibo'));
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
        $recibo =  Recibo::findOrFail($id);
        $recibo->fecha = $request->fecha;
        $recibo->cantidad = $request->cantidad;
        $recibo->interes =  $request->interes;
        //$recibo->remanente =  $request->remanente;
        $recibo->comprobante =  $request->comprobante;
        $recibo->estado = $request->estado != null ? 2 : 1;
        $recibo->save();

        alert()->success('El registro ha sido guardado correctamente');
        return redirect('prestamo_web/' . $recibo->prestamo_id);
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
