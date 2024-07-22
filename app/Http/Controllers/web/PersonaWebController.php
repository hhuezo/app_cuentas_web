<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class PersonaWebController extends Controller
{

    public function index()
    {
        $personas = Persona::where('activo',1)->get();
        return view('persona.index', compact('personas'));
    }

    public function create()
    {
        return view('persona.create');
    }

    public function store(Request $request)
    {
        $persona = new Persona();
        $persona->nombre = $request->nombre;
        $persona->documento = $request->documento;
        $persona->telefono = $request->telefono;
        $persona->banco = $request->banco;
        $persona->cuenta = $request->cuenta;
        $persona->activo = 1;
        $persona->save();
        alert()->success('El registro ha sido creado correctamente');
        return Redirect::to('persona/');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $persona = Persona::findOrFail($id);
        return view('persona.edit', compact('persona'));
    }

    public function update(Request $request, $id)
    {
        $persona = Persona::findOrFail($id);
        $persona->nombre = $request->nombre;
        $persona->documento = $request->documento;
        $persona->telefono = $request->telefono;
        $persona->banco = $request->banco;
        $persona->cuenta = $request->cuenta;
        $persona->activo = 1;
        $persona->update();
        alert()->success('El registro ha sido modificado correctamente');
        return Redirect::to('persona/');
    }

    public function destroy($id)
    {
        $persona = Persona::findOrFail($id);
        $persona->activo = 0;
        $persona->update();
        alert()->success('El registro ha sido deshabilitado correctamente');
        return Redirect::to('persona/');

    }
}
