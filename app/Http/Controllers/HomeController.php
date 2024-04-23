<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use App\Models\Prestamo;
use App\Models\Recibo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $fechaInicio = Carbon::now()->firstOfMonth()->format('Y-m-d');
        if($request->fechaInicio)
        {
            $fechaInicio = $request->fechaInicio;
        }


        // Obtener el último día del mes actual
        $fechaFinal = Carbon::now()->endOfMonth()->format('Y-m-d');
        if($request->fechaFinal)
        {
            $fechaFinal = $request->fechaFinal;
        }

        $pagos = Recibo::whereBetween('fecha', [$fechaInicio, $fechaFinal])->orderBy('fecha')->get();

        $count_prestamos = Prestamo::count('id');
        $total_prestado = Prestamo::sum('cantidad');
        $total_cargos = Cargo::sum('cantidad');
        $total_reintegrado = Recibo::where('estado', 2)->sum('cantidad');
        $total_interes_reintegrado = Recibo::where('estado', 2)->sum('interes');
        $data_general = ["count_prestamos" => $count_prestamos, "total_prestado" => $total_prestado,"total_cargos" => $total_cargos, "total_reintegrado" => $total_reintegrado, "total_interes_reintegrado" => $total_interes_reintegrado];

        return view('home', compact('pagos', 'data_general','fechaInicio','fechaFinal'));
    }
}
