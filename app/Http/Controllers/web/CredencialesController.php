<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Creadenciales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class CredencialesController extends Controller
{
    public function index()
    {
        /*
        CREATE TABLE `credenciales` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario` varchar(155) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(145) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sitio_web` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notas` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB
 */
        $credenciales = Creadenciales::get();
        return view('credenciales.index', compact('credenciales'));
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
        $credencial = new Creadenciales();
        $credencial->usuario = $request->usuario;
        $credencial->password = $request->password;
        $credencial->sitio_web = $request->sitio_web;
        $credencial->notas = $request->notas;
        $credencial->save();

        alert()->success('El registro ha sido creado correctamente');
        return Redirect::to('credenciales_web/');
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

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $credencial = Creadenciales::findOrFail($id);
        $credencial->usuario = $request->usuario;
        $credencial->password = $request->password;
        $credencial->sitio_web = $request->sitio_web;
        $credencial->notas = $request->notas;
        $credencial->save();

        alert()->success('El registro ha sido modificado correctamente');
        return Redirect::to('credenciales_web/');
    }

    public function destroy($id)
    {
        $credencial = Creadenciales::findOrFail($id);
        $credencial->delete();

        alert()->success('El registro ha sido eliminado correctamente');
        return Redirect::to('credenciales_web/');
    }
}
