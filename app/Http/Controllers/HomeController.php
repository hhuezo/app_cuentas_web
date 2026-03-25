<?php

namespace App\Http\Controllers;

use App\Mail\MiCorreo;
use App\Models\Cargo;
use App\Models\Prestamo;
use App\Models\Recibo;
use App\Models\ReciboFijo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

        /*$datos = [
            'nombre' => 'Juan Pérez',
            'mensaje' => 'Este es un mensaje de prueba.'
        ];

        Mail::to('hugo.alex.huezo@gmail.com')->send(new MiCorreo($datos));

        return "Correo enviado correctamente";*/


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

        $prestamosCulminanMes = Recibo::with(['prestamo.persona'])
            ->whereBetween('fecha', [$fechaInicio, $fechaFinal])
            ->where('remanente', 0)
            ->orderBy('fecha')
            ->get();

        $personaIdsCulminan = $prestamosCulminanMes
            ->pluck('prestamo.persona_id')
            ->filter()
            ->unique()
            ->values();

        $prestamosPosterioresPorPersona = [];
        if ($personaIdsCulminan->isNotEmpty()) {
            $prestamosPosterioresPorPersona = Prestamo::select('id', 'persona_id')
                ->whereIn('persona_id', $personaIdsCulminan)
                ->whereHas('recibos', function ($query) use ($fechaFinal) {
                    $query->whereDate('fecha', '>', $fechaFinal)
                        ->where('remanente', '>', 0);
                })
                ->get()
                ->groupBy('persona_id')
                ->map(function ($items) {
                    return $items->pluck('id')->values()->all();
                })
                ->all();
        }

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
        $gananciaProyectadaPorMesArray = [];



        // Obtener el mes actual
        $currentMonth = Carbon::now()->month;

        // Calcular los últimos 12 meses
        $months = [];
        $years = [];
        for ($i = 0; $i < 24; $i++) {
            $months[] = Carbon::now()->subMonths($i)->month;
            $years[] = Carbon::now()->subMonths($i)->year;
        }

        // Si quieres que el arreglo esté en orden ascendente (de más antiguo a más reciente)
        $months = array_reverse($months);
        $years = array_reverse($years);


        for ($i = 0; $i < 24; $i++) {
           // echo "Mes: " . $months[$i] ."-".$meses[$months[$i]]. " Año: " . $years[$i] . "\n";

            $interesesPorMes = Recibo::selectRaw('SUM(interes) as total_interes, YEAR(fecha) as anio, MONTH(fecha) as mes')
                ->where('estado', 2)
                ->whereYear('fecha', $years[$i])
                ->whereMonth('fecha', $months[$i])
                ->groupByRaw('YEAR(fecha), MONTH(fecha)')
                ->first();
            $totalInteres = $interesesPorMes ? ($interesesPorMes->total_interes + 0) : 0;
            $arrayInteres = ["name" => $meses[$months[$i]]."-".$years[$i], "y" => $totalInteres, "drilldown" => $meses[$months[$i]]];
            array_push($interesesPorMesArray, $arrayInteres);

            $interesesPorMes = ReciboFijo::selectRaw('SUM(cantidad) as total, YEAR(fecha) as anio, MONTH(fecha) as mes')
                ->whereYear('fecha', $years[$i])
                ->whereMonth('fecha', $months[$i])
                ->groupByRaw('YEAR(fecha), MONTH(fecha)')
                ->first();

            $totalGanancia = $interesesPorMes ? ($interesesPorMes->total + 0) : 0;
            $arrayGanancia = ["name" => $meses[$months[$i]]."-".$years[$i], "y" => $totalGanancia, "drilldown" => $meses[$months[$i]]];
            array_push($gananciaPorMesArray, $arrayGanancia);
        }

        // Proyección: recibos pendientes (estado = 1), 24 meses desde el mes pasado hacia adelante.
        for ($i = -1; $i < 23; $i++) {
            $periodo = Carbon::now()->startOfMonth()->addMonths($i);
            $mes = $periodo->month;
            $anio = $periodo->year;

            $proyeccionMes = Recibo::selectRaw('SUM(interes) as total_interes')
                ->where('estado', 1)
                ->whereYear('fecha', $anio)
                ->whereMonth('fecha', $mes)
                ->first();

            $totalProyectado = $proyeccionMes ? ($proyeccionMes->total_interes + 0) : 0;
            $arrayProyectado = ["name" => $meses[$mes]."-".$anio, "y" => $totalProyectado, "drilldown" => $meses[$mes]];
            array_push($gananciaProyectadaPorMesArray, $arrayProyectado);
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
            'prestamosCulminanMes',
            'prestamosPosterioresPorPersona',
            'data_general',
            'fechaInicio',
            'fechaFinal',
            'interesesPorMesArray',
            'gananciaPorMesArray',
            'gananciaProyectadaPorMesArray'
        ));
    }
}
