<div class="tab-content" id="pills-tabContent">
    <div class="tab-pane fade show active" id="pills-social" role="tabpanel" aria-labelledby="pills-social-tab">
        <div class="table-responsive">
            <table class="table card-table border-no success-tbl">
                <thead>
                    <tr>
                        <th></th>
                        <th>Fecha</th>
                        <th>Nombre</th>
                        <th style="text-align: right;">Interes</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php($total_interes = 0)
                    @php($total = 0)
                    @foreach ($pagos->where('estado', 1) as $pago)
                        <tr>
                            <td>
                                <button type="button" class="btn btn-link p-0 border-0 btn-ver-recibo"
                                    data-recibo-id="{{ $pago->id }}" title="Ver recibo">
                                    <i class="fa fa-eye fa-lg"></i>
                                </button>
                            </td>
                            <td>{{ date('d/m/Y', strtotime($pago->fecha)) }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="ms-2 cat-name nombre-col">
                                        {{ $pago->prestamo->persona->nombre }}
                                    </div>
                                </div>
                            </td>
                            <td style="text-align: right;">
                                ${{ $pago->interes }}
                            </td>
                            <td>${{ $pago->cantidad }}</td>
                        </tr>

                        @php($total_interes += $pago->interes)
                        @php($total += $pago->cantidad)
                    @endforeach
                    <tr>
                        <th colspan="3" style="text-align: right;">TOTAL</th>
                        <th style="text-align: right;">
                            ${{ number_format($total_interes, 2, '.', ',') }}</th>
                        <th style="text-align: right;">${{ number_format($total, 2, '.', ',') }}</th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="tab-pane fade" id="pills-project" role="tabpanel" aria-labelledby="pills-project-tab">
        <div class="table-responsive">
            <table class="table card-table border-no success-tbl">
                <thead>
                    <tr>
                        <th></th>
                        <th>Fecha</th>
                        <th>Nombre</th>
                        <th>Interes</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php($total_interes = 0)
                    @php($total = 0)
                    @foreach ($pagos->where('estado', 2) as $pago)
                        <tr>
                            <td>
                                <button type="button" class="btn btn-link p-0 border-0 btn-ver-recibo"
                                    data-recibo-id="{{ $pago->id }}" title="Ver recibo">
                                    <i class="fa fa-eye fa-lg"></i>
                                </button>
                            </td>
                            <td style="color: #198754 !important;">{{ date('d/m/Y', strtotime($pago->fecha)) }}</td>
                            <td style="color: #198754 !important;">
                                <div class="d-flex align-items-center">
                                    <div class="ms-2 cat-name nombre-col">
                                        {{ $pago->prestamo->persona->nombre }}
                                    </div>
                                </div>
                            </td>
                            <td style="text-align: right; color: #198754 !important;">
                                ${{ $pago->interes }}
                            </td>
                            <td style="text-align: right; color: #198754 !important;">${{ $pago->cantidad }}</td>
                        </tr>
                        @php($total_interes += $pago->interes)
                        @php($total += $pago->cantidad)
                    @endforeach
                    <tr>
                        <th colspan="3" style="text-align: right;">TOTAL</th>
                        <th style="text-align: right;">
                            ${{ number_format($total_interes, 2, '.', ',') }}</th>
                        <th style="text-align: right;">${{ number_format($total, 2, '.', ',') }}</th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="tab-pane fade" id="pills-all1" role="tabpanel" aria-labelledby="pills-all1-tab">
        <div class="table-responsive">
            <table class="table card-table border-no success-tbl">
                <thead>
                    <tr>
                        <th></th>
                        <th>Fecha</th>
                        <th>Nombre</th>
                        <th>Interes</th>
                        <th>Total</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @php($total_interes = 0)
                    @php($total = 0)
                    @foreach ($pagos as $pago)
                        <tr>
                            <td>
                                <button type="button" class="btn btn-link p-0 border-0 btn-ver-recibo"
                                    data-recibo-id="{{ $pago->id }}" title="Ver recibo">
                                    <i class="fa fa-eye fa-lg"></i>
                                </button>
                            </td>
                            <td>{{ date('d/m/Y', strtotime($pago->fecha)) }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="ms-2 cat-name nombre-col">
                                        {{ $pago->prestamo->persona->nombre }}
                                    </div>
                                </div>
                            </td>
                            <td style="text-align: right;">
                                ${{ $pago->interes }}
                            </td>
                            <td style="text-align: right;">${{ $pago->cantidad }}</td>
                            <td>
                                <span
                                    class="badge badge-{{ $pago->estado == 1 ? 'danger' : 'success' }} light border-0">{{ $pago->estado == 1 ? 'Pendiente' : 'Pagado' }}</span>
                            </td>
                        </tr>
                        @php($total_interes += $pago->interes)
                        @php($total += $pago->cantidad)
                    @endforeach
                    <tr>
                        <th colspan="3" style="text-align: right;">TOTAL</th>
                        <th style="text-align: right;">
                            ${{ number_format($total_interes, 2, '.', ',') }}</th>
                        <th style="text-align: right;">${{ number_format($total, 2, '.', ',') }}</th>
                        <th></th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
