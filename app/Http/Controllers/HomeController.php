<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use App\Models\Prestamo;
use App\Models\Recibo;
use App\Models\ReciboFijo;
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
        if ($request->fechaInicio) {
            $fechaInicio = $request->fechaInicio;
        }


        // Obtener el último día del mes actual
        $fechaFinal = Carbon::now()->endOfMonth()->format('Y-m-d');
        if ($request->fechaFinal) {
            $fechaFinal = $request->fechaFinal;
        }

        $pagos = Recibo::whereBetween('fecha', [$fechaInicio, $fechaFinal])->orderBy('fecha')->get();

        $count_prestamos = Prestamo::count('id');
        $total_prestado = Prestamo::sum('cantidad');
        $total_cargos = Cargo::sum('cantidad');
        $total_reintegrado = Recibo::where('estado', 2)->sum('cantidad');
        $total_interes_reintegrado = Recibo::where('estado', 2)->sum('interes');
        $total_fijo_reintegrado = ReciboFijo::sum('cantidad');
        $data_general = [
            "count_prestamos" => $count_prestamos,
            "total_prestado" => $total_prestado,
            "total_cargos" => $total_cargos,
            "total_reintegrado" => $total_reintegrado,
            "total_interes_reintegrado" => $total_interes_reintegrado,
            "total_fijo_reintegrado" => $total_fijo_reintegrado
        ];

        $meses = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

        $interesesPorMesArray = [];
        $gananciaPorMesArray = [];



        // Obtener el mes actual
        $currentMonth = Carbon::now()->month;

        // Calcular los últimos 12 meses
        $months = [];
        $years = [];
        for ($i = 0; $i < 12; $i++) {
            $months[] = Carbon::now()->subMonths($i)->month;
            $years[] = Carbon::now()->subMonths($i)->year;
        }

        // Si quieres que el arreglo esté en orden ascendente (de más antiguo a más reciente)
        $months = array_reverse($months);
        $years = array_reverse($years);


        for ($i = 0; $i < 12; $i++) {
           // echo "Mes: " . $months[$i] ."-".$meses[$months[$i]]. " Año: " . $years[$i] . "\n";

            $interesesPorMes = Recibo::selectRaw('SUM(interes) as total_interes, YEAR(fecha) as anio, MONTH(fecha) as mes')
                ->where('estado', 2)
                ->whereYear('fecha', $years[$i])
                ->whereMonth('fecha', $months[$i])
                ->groupByRaw('YEAR(fecha), MONTH(fecha)')
                ->first();
            if ($interesesPorMes) {
                $total = $interesesPorMes->total_interes + 0;
                $array = ["name" => $meses[$months[$i]]."-".$years[$i], "y" => $total, "drilldown" => $meses[$months[$i]]];
                array_push($interesesPorMesArray, $array);
            } else {
                $total = 0;
            }

            $interesesPorMes = ReciboFijo::selectRaw('SUM(cantidad) as total, YEAR(fecha) as anio, MONTH(fecha) as mes')
                ->whereYear('fecha', $years[$i])
                ->whereMonth('fecha', $months[$i])
                ->groupByRaw('YEAR(fecha), MONTH(fecha)')
                ->first();

            if ($interesesPorMes) {
                $total = $interesesPorMes->total + 0;
                $array = ["name" => $meses[$months[$i]]."-".$years[$i], "y" => $total, "drilldown" => $meses[$months[$i]]];
                array_push($gananciaPorMesArray, $array);
            } else {
                $total = 0;
            }
        }
        //dd($months, $years);
        //dd($interesesPorMesArray, $gananciaPorMesArray);



        /*for ($i = 1; $i <= 12; $i++) {
            $interesesPorMes = Recibo::selectRaw('SUM(interes) as total_interes, YEAR(fecha) as anio, MONTH(fecha) as mes')
                ->where('estado', 2)
                ->whereYear('fecha', 2024)
                ->whereMonth('fecha', $i)
                ->groupByRaw('YEAR(fecha), MONTH(fecha)')
                ->first();
            if ($interesesPorMes) {
                $total = $interesesPorMes->total_interes + 0;
                $array = ["name" => $meses[$i], "y" => $total, "drilldown" => $meses[$i]];
                array_push($interesesPorMesArray,$array);
            } else {
                $total = 0;
            }

            $interesesPorMes = ReciboFijo::selectRaw('SUM(cantidad) as total, YEAR(fecha) as anio, MONTH(fecha) as mes')
            ->whereYear('fecha', 2025)
            ->whereMonth('fecha', $i)
            ->groupByRaw('YEAR(fecha), MONTH(fecha)')
            ->first();

            if ($interesesPorMes) {
                $total = $interesesPorMes->total + 0;
                $array = ["name" => $meses[$i], "y" => $total, "drilldown" => $meses[$i]];
                array_push($gananciaPorMesArray,$array);
            } else {
                $total = 0;
            }
        }*/


        return view('home', compact(
            'pagos',
            'data_general',
            'fechaInicio',
            'fechaFinal',
            'interesesPorMesArray',
            'gananciaPorMesArray'
        ));
    }
}
