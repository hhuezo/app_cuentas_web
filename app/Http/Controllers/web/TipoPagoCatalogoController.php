<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\TipoPago;
use Illuminate\Http\Request;

class TipoPagoCatalogoController extends Controller
{

    public function index()
    {
        $tiposPago =  TipoPago::get();
        return view('catalogo.tipo_pago.index', compact('tiposPago'));
    }

    public function create()
    {
        return view('catalogo.tipo_pago.create');
    }

    public function store(Request $request)
    {
        //
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
