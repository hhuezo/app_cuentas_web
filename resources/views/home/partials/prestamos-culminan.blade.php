<div class="table-responsive">
    <table class="table card-table border-no">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Nombre</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($prestamosCulminanMes as $reciboFinMes)
                @php
                    $personaId = optional($reciboFinMes->prestamo)->persona_id;
                    $prestamoIdActual = $reciboFinMes->prestamo_id;
                    $prestamosPosteriores = $prestamosPosterioresPorPersona[$personaId] ?? [];
                    $tieneOtroPrestamoPosterior = collect($prestamosPosteriores)
                        ->contains(fn($id) => (int) $id !== (int) $prestamoIdActual);
                @endphp
                <tr>
                    <td>{{ date('d/m/Y', strtotime($reciboFinMes->fecha)) }}</td>
                    <td>{{ optional(optional($reciboFinMes->prestamo)->persona)->nombre ?? 'Sin nombre' }}</td>
                    <td>
                        @if ($tieneOtroPrestamoPosterior)
                            <span class="badge badge-warning light border-0">
                                Tiene otro préstamo activo posterior
                            </span>
                        @else
                            <span class="badge badge-success light border-0">
                                Culminado este mes
                            </span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center text-muted">No hay préstamos que culminen este mes.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
