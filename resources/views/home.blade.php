@extends('menu')
@section('contenido')
    <style>
        .highcharts-figure,
        .highcharts-data-table table {
            min-width: 310px;
            max-width: 800px;
            margin: 1em auto;
        }

        #container {
            height: 400px;
        }

        .highcharts-data-table table {
            font-family: Verdana, sans-serif;
            border-collapse: collapse;
            border: 1px solid #ebebeb;
            margin: 10px auto;
            text-align: center;
            width: 100%;
            max-width: 500px;
        }

        .highcharts-data-table caption {
            padding: 1em 0;
            font-size: 1.2em;
            color: #555;
        }

        .highcharts-data-table th {
            font-weight: 600;
            padding: 0.5em;
        }

        .highcharts-data-table td,
        .highcharts-data-table th,
        .highcharts-data-table caption {
            padding: 0.5em;
        }

        .highcharts-data-table thead tr,
        .highcharts-data-table tr:nth-child(even) {
            background: #f8f8f8;
        }

        .highcharts-data-table tr:hover {
            background: #f1f7ff;
        }
    </style>



    <div class="row">
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="icon-box icon-box-lg bg-success-light rounded-circle">
                            <svg width="46" height="46" viewBox="0 0 46 46" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M22.9715 29.3168C15.7197 29.3168 9.52686 30.4132 9.52686 34.8043C9.52686 39.1953 15.6804 40.331 22.9715 40.331C30.2233 40.331 36.4144 39.2328 36.4144 34.8435C36.4144 30.4543 30.2626 29.3168 22.9715 29.3168Z"
                                    stroke="#3AC977" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M22.9714 23.0537C27.7304 23.0537 31.5875 19.1948 31.5875 14.4359C31.5875 9.67694 27.7304 5.81979 22.9714 5.81979C18.2125 5.81979 14.3536 9.67694 14.3536 14.4359C14.3375 19.1787 18.1696 23.0377 22.9107 23.0537H22.9714Z"
                                    stroke="#3AC977" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </div>
                        <div class="total-projects ms-3">
                            <h3 class="text-success count">
                                ${{ number_format($data_general['total_prestado'] + $data_general['total_cargos'], 2, '.', ',') }}
                            </h3>
                            <span>Total prestado</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="icon-box icon-box-lg bg-primary-light rounded-circle">
                            <svg width="46" height="46" viewBox="0 0 46 46" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M32.8961 26.5849C34.1612 26.5849 35.223 27.629 35.0296 28.8783C33.8947 36.2283 27.6026 41.6855 20.0138 41.6855C11.6178 41.6855 4.8125 34.8803 4.8125 26.4862C4.8125 19.5704 10.0664 13.1283 15.9816 11.6717C17.2526 11.3579 18.5553 12.252 18.5553 13.5605C18.5553 22.4263 18.8533 24.7197 20.5368 25.9671C22.2204 27.2145 24.2 26.5849 32.8961 26.5849Z"
                                    stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                </path>
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M41.1733 19.2019C41.2739 13.5059 34.2772 4.32428 25.7509 4.48217C25.0877 4.49402 24.5568 5.04665 24.5272 5.70783C24.3121 10.3914 24.6022 16.4605 24.764 19.2118C24.8134 20.0684 25.4864 20.7414 26.341 20.7907C29.1693 20.9526 35.4594 21.1736 40.0759 20.4749C40.7035 20.3802 41.1634 19.8355 41.1733 19.2019Z"
                                    stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                </path>
                            </svg>

                        </div>
                        <div class="total-projects ms-3">
                            <h3 class="text-primary count">
                                ${{ number_format($data_general['total_reintegrado'] - $data_general['total_interes_reintegrado'], 2, '.', ',') }}
                            </h3>
                            <span>Total reintegrado</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="icon-box icon-box-lg bg-purple-light rounded-circle">
                            <svg width="46" height="46" viewBox="0 0 46 46" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M22.9717 41.0539C22.9717 41.0539 37.3567 36.6983 37.3567 24.6908C37.3567 12.6814 37.878 11.7439 36.723 10.5889C35.5699 9.43391 24.858 5.69891 22.9717 5.69891C21.0855 5.69891 10.3736 9.43391 9.21863 10.5889C8.0655 11.7439 8.58675 12.6814 8.58675 24.6908C8.58675 36.6983 22.9717 41.0539 22.9717 41.0539Z"
                                    stroke="#BB6BD9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M26.4945 26.4642L19.4482 19.4179" stroke="#BB6BD9" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M19.4487 26.4642L26.495 19.4179" stroke="#BB6BD9" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </div>
                        <div class="total-projects ms-3">
                            <h3 class="text-purple count">
                                ${{ number_format($data_general['total_prestado'] + $data_general['total_cargos'] - ($data_general['total_reintegrado'] - $data_general['total_interes_reintegrado']), 2, '.', ',') }}
                            </h3>
                            <span>Dinero invertido</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="icon-box icon-box-lg bg-purple-light rounded-circle">
                            <svg width="46" height="46" viewBox="0 0 46 46" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M22.9717 41.0539C22.9717 41.0539 37.3567 36.6983 37.3567 24.6908C37.3567 12.6814 37.878 11.7439 36.723 10.5889C35.5699 9.43391 24.858 5.69891 22.9717 5.69891C21.0855 5.69891 10.3736 9.43391 9.21863 10.5889C8.0655 11.7439 8.58675 12.6814 8.58675 24.6908C8.58675 36.6983 22.9717 41.0539 22.9717 41.0539Z"
                                    stroke="#BB6BD9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M26.4945 26.4642L19.4482 19.4179" stroke="#BB6BD9" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M19.4487 26.4642L26.495 19.4179" stroke="#BB6BD9" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </div>
                        <div class="total-projects ms-3">
                            <h3 class="text-purple count">
                                ${{ number_format($data_general['total_interes_reintegrado'], 2, '.', ',') }}</h3>
                            <span>Intereses</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="icon-box icon-box-lg bg-danger-light rounded-circle">
                            <svg width="46" height="46" viewBox="0 0 46 46" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M34.0396 20.974C36.6552 20.6065 38.6689 18.364 38.6746 15.6471C38.6746 12.9696 36.7227 10.7496 34.1633 10.3296"
                                    stroke="#FF5E5E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                </path>
                                <path
                                    d="M37.4912 27.262C40.0243 27.6407 41.7925 28.5276 41.7925 30.3557C41.7925 31.6139 40.96 32.4314 39.6137 32.9451"
                                    stroke="#FF5E5E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                </path>
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M22.7879 28.0373C16.7616 28.0373 11.6147 28.9504 11.6147 32.5973C11.6147 36.2423 16.7297 37.1817 22.7879 37.1817C28.8141 37.1817 33.9591 36.2779 33.9591 32.6292C33.9591 28.9804 28.846 28.0373 22.7879 28.0373Z"
                                    stroke="#FF5E5E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                </path>
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M22.7876 22.8325C26.742 22.8325 29.9483 19.6281 29.9483 15.6719C29.9483 11.7175 26.742 8.51123 22.7876 8.51123C18.8333 8.51123 15.627 11.7175 15.627 15.6719C15.612 19.6131 18.7939 22.8194 22.7351 22.8325H22.7876Z"
                                    stroke="#FF5E5E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                </path>
                                <path
                                    d="M11.5344 20.974C8.91691 20.6065 6.90504 18.364 6.89941 15.6471C6.89941 12.9696 8.85129 10.7496 11.4107 10.3296"
                                    stroke="#FF5E5E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                </path>
                                <path
                                    d="M8.0825 27.262C5.54937 27.6407 3.78125 28.5276 3.78125 30.3557C3.78125 31.6139 4.61375 32.4314 5.96 32.9451"
                                    stroke="#FF5E5E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                </path>
                            </svg>
                        </div>
                        <div class="total-projects ms-3">
                            <h3 class="text-danger count" style="text-align: right;">
                                ${{ number_format($data_general['total_fijo_reintegrado'], 2, '.', ',') }}</h3>
                            <span>Total préstamo fijo</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div class="col-xl-6 col-xxl-12">
            <div class="card">
                <div class="card-header border-0 pb-0 flex-wrap">
                    <h4 class="heading mb-0">Pagos</h4>
                </div>
                <div class="card-body px-0 pb-0">
                    <ul class="nav nav-pills success-tab" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" style="width: 100px" data-series="social"
                                id="pills-social-tab" data-bs-toggle="pill" data-bs-target="#pills-social"
                                type="button" role="tab" aria-selected="true">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1"
                                    class="svg-main-icon">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <polygon points="0 0 24 0 24 24 0 24" />
                                        <path
                                            d="M16.5,4.5 C14.8905,4.5 13.00825,6.32463215 12,7.5 C10.99175,6.32463215 9.1095,4.5 7.5,4.5 C4.651,4.5 3,6.72217984 3,9.55040872 C3,12.6834696 6,16 12,19.5 C18,16 21,12.75 21,9.75 C21,6.92177112 19.349,4.5 16.5,4.5 Z"
                                            fill="var(--text-dark)" fill-rule="nonzero" opacity="0.3" />
                                        <path
                                            d="M12,19.5 C6,16 3,12.6834696 3,9.55040872 C3,6.72217984 4.651,4.5 7.5,4.5 C9.1095,4.5 10.99175,6.32463215 12,7.5 L12,19.5 Z"
                                            fill="var(--text-dark)" fill-rule="nonzero" />
                                    </g>
                                </svg>
                                <span>Pendientes</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-series="project" style="width: 100px" id="pills-project-tab"
                                data-bs-toggle="pill" data-bs-target="#pills-project" type="button" role="tab"
                                aria-selected="false">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1"
                                    class="svg-main-icon">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path
                                            d="M5.84026576,8 L18.1597342,8 C19.1999115,8 20.0664437,8.79732479 20.1528258,9.83390904 L20.8194924,17.833909 C20.9112219,18.9346631 20.0932459,19.901362 18.9924919,19.9930915 C18.9372479,19.9976952 18.8818364,20 18.8264009,20 L5.1735991,20 C4.0690296,20 3.1735991,19.1045695 3.1735991,18 C3.1735991,17.9445645 3.17590391,17.889153 3.18050758,17.833909 L3.84717425,9.83390904 C3.93355627,8.79732479 4.80008849,8 5.84026576,8 Z M10.5,10 C10.2238576,10 10,10.2238576 10,10.5 L10,11.5 C10,11.7761424 10.2238576,12 10.5,12 L13.5,12 C13.7761424,12 14,11.7761424 14,11.5 L14,10.5 C14,10.2238576 13.7761424,10 13.5,10 L10.5,10 Z"
                                            fill="var(--text-dark)" />
                                        <path
                                            d="M10,8 L8,8 L8,7 C8,5.34314575 9.34314575,4 11,4 L13,4 C14.6568542,4 16,5.34314575 16,7 L16,8 L14,8 L14,7 C14,6.44771525 13.5522847,6 13,6 L11,6 C10.4477153,6 10,6.44771525 10,7 L10,8 Z"
                                            fill="var(--text-dark)" fill-rule="nonzero" opacity="0.3" />
                                    </g>
                                </svg>
                                <span>Pagados</span>
                            </button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-series="all" id="pills-all1-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-all1" type="button" role="tab" aria-selected="false">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1"
                                    class="svg-main-icon">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path
                                            d="M3.5,21 L20.5,21 C21.3284271,21 22,20.3284271 22,19.5 L22,8.5 C22,7.67157288 21.3284271,7 20.5,7 L10,7 L7.43933983,4.43933983 C7.15803526,4.15803526 6.77650439,4 6.37867966,4 L3.5,4 C2.67157288,4 2,4.67157288 2,5.5 L2,19.5 C2,20.3284271 2.67157288,21 3.5,21 Z"
                                            fill="var(--text-dark)" opacity="0.3" />
                                        <path
                                            d="M14.35,10.5 C13.54525,10.5 12.604125,11.4123161 12.1,12 C11.595875,11.4123161 10.65475,10.5 9.85,10.5 C8.4255,10.5 7.6,11.6110899 7.6,13.0252044 C7.6,14.5917348 9.1,16.25 12.1,18 C15.1,16.25 16.6,14.625 16.6,13.125 C16.6,11.7108856 15.7745,10.5 14.35,10.5 Z"
                                            fill="var(--text-dark)" fill-rule="nonzero" opacity="0.3" />
                                    </g>
                                </svg>
                                <span>Todos</span>
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-social" role="tabpanel"
                            aria-labelledby="pills-social-tab">
                            <div class="table-responsive">
                                <table class="table  card-table border-no success-tbl">
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
                                                    <a href="{{ url('prestamo_web') }}/{{ $pago->prestamo_id }}">
                                                        <i class="fa fa-eye fa-lg"></i>
                                                    </a>
                                                </td>
                                                <td>{{ date('d/m/Y', strtotime($pago->fecha)) }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="ms-2 cat-name">
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
                        <div class="tab-pane fade" id="pills-project" role="tabpanel"
                            aria-labelledby="pills-project-tab">
                            <div class="table-responsive">
                                <table class="table  card-table border-no success-tbl">
                                    <thead>
                                        <tr>
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
                                                <td>{{ date('d/m/Y', strtotime($pago->fecha)) }}
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="ms-2 cat-name">
                                                            {{ $pago->prestamo->persona->nombre }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="text-align: right;">
                                                    ${{ $pago->interes }}
                                                </td>
                                                <td style="text-align: right;">${{ $pago->cantidad }}</td>
                                            </tr>
                                            @php($total_interes += $pago->interes)
                                            @php($total += $pago->cantidad)
                                        @endforeach
                                        <tr>
                                            <th colspan="2" style="text-align: right;">TOTAL</th>
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
                                <table class="table  card-table border-no success-tbl">
                                    <thead>
                                        <tr>
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
                                                <td>{{ date('d/m/Y', strtotime($pago->fecha)) }}
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="ms-2 cat-name">
                                                            {{ $pago->prestamo->persona->nombre }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="text-align: right;">
                                                    ${{ $pago->interes }}
                                                </td>
                                                <td style="text-align: right;">${{ $pago->cantidad }}</td>
                                                <td><span
                                                        class="badge badge-{{ $pago->estado == 1 ? 'danger' : 'success' }} light border-0">{{ $pago->estado == 1 ? 'Pendiente' : 'Pagado' }}</span>
                                                </td>

                                            </tr>
                                            @php($total_interes += $pago->interes)
                                            @php($total += $pago->cantidad)
                                        @endforeach
                                        <tr>
                                            <th colspan="2" style="text-align: right;">TOTAL</th>
                                            <th style="text-align: right;">
                                                ${{ number_format($total_interes, 2, '.', ',') }}</th>
                                            <th style="text-align: right;">${{ number_format($total, 2, '.', ',') }}</th>
                                            <th colspan="3" style="text-align: right;"></th>
                                        </tr>

                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-xl-6 col-xxl-12">
            <div class="card">
                <div class="card-body px-0 pb-0">
                    <div id="container"></div>


                </div>
            </div>
        </div>
        <div class="col-xl-6 col-xxl-12">
            <div class="card">
                <div class="card-body px-0 pb-0">
                    <div id="container2"></div>


                </div>
            </div>
        </div>



    </div>


    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/data.js"></script>
    <script src="https://code.highcharts.com/modules/drilldown.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

    <script>
        Highcharts.chart('container', {
            chart: {
                type: 'column'
            },
            title: {
                align: 'left',
                text: 'Ganancias mensuales'
            },
            subtitle: {
                align: 'left',
                text: ''
            },
            accessibility: {
                announceNewData: {
                    enabled: true
                }
            },
            xAxis: {
                type: 'category'
            },
            yAxis: {
                title: {
                    text: ''
                }

            },
            legend: {
                enabled: false
            },
            plotOptions: {
                series: {
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        format: '${point.y:.2f}'
                    }
                }
            },

            tooltip: {
                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.0f}</b> of total<br/>'
            },

            series: [{
                name: 'Browsers',
                colorByPoint: true,
                data: @json($interesesPorMesArray)
            }]
        });

        Highcharts.chart('container2', {
            chart: {
                type: 'column'
            },
            title: {
                align: 'left',
                text: 'Ganancias mensuales(Fijos)'
            },
            subtitle: {
                align: 'left',
                text: ''
            },
            accessibility: {
                announceNewData: {
                    enabled: true
                }
            },
            xAxis: {
                type: 'category'
            },
            yAxis: {
                title: {
                    text: ''
                }

            },
            legend: {
                enabled: false
            },
            plotOptions: {
                series: {
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        format: '${point.y:.2f}'
                    }
                }
            },

            tooltip: {
                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.0f}</b> of total<br/>'
            },

            series: [{
                name: 'Browsers',
                colorByPoint: true,
                data: @json($gananciaPorMesArray)
            }]
        });
    </script>
@endsection
