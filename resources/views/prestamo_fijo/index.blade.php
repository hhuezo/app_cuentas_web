@extends('menu')
@section('contenido')


    <div class="col-xl-12">
        <div class="card dz-card" id="accordion-six">
            <div class="card-header flex-wrap d-flex justify-content-between">
                <div>
                    <h4 class="card-title">Prestamos</h4>
                    </p>
                </div>
                <ul class="nav nav-tabs dzm-tabs" id="myTab-six" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="{{ url('prestamo_fijo_web/create') }}">
                            <button class="btn btn-primary btn-sm" type="button" role="tab"
                                aria-selected="true">Nuevo</button>
                        </a>
                    </li>

                </ul>
            </div>

            @if (count($errors) > 0)
                <br>
                <div class="mb-3 col-md-6 col-sm-12" style="margin-left: 20px">
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
            <!-- tab-content -->
            <div class="tab-content" id="myTabContent-six">
                <div class="tab-pane fade active show" id="responsive" role="tabpanel" aria-labelledby="home-tab-six">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="responsiveTable" class="display responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Código</th>
                                        <th>Fecha</th>
                                        <th>Cantidad</th>
                                        <th>Descripción</th>
                                        <th>Finalizado</th>
                                        <th>Opciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($prestamos as $obj)
                                        <tr>
                                            <td>{{ $obj->persona->nombre }}</td>
                                            <td>{{ str_pad($obj->codigo, 4, '0', STR_PAD_LEFT) }}</td>
                                            <td>{{ date('d/m/Y', strtotime($obj->fecha)) }}</td>
                                            <td>${{ $obj->cantidad }}</td>
                                            <td>{{ $obj->observacion }}</td>
                                            <td><input type="checkbox" {{ $obj->estado == 2 ? 'checked' : '' }}></td>
                                            <td>
                                                <div class="d-flex">

                                                    <a href="{{ url('prestamo_fijo_web') }}/{{ $obj->id }}"
                                                        class="btn btn-primary shadow btn sharp me-1"><i
                                                            class="fas fa-eye"></i></a> &nbsp;

                                                    <a href="#" data-bs-toggle="modal"
                                                        data-bs-target="#modal-delete-{{ $obj->id }}"
                                                        class="btn btn-danger shadow btn sharp"><i
                                                            class="fa fa-trash"></i></a>

                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /tab-content -->
    </div>

    <script src="{{ asset('template/js/jquery-3.6.0.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#responsiveTable')) {
                $('#responsiveTable').DataTable().destroy();
            }
            $('#responsiveTable').DataTable({
                "language": {
                    "sProcessing": "Procesando...",
                    "sLengthMenu": "Mostrar _MENU_ registros",
                    "sZeroRecords": "No se encontraron resultados",
                    "sEmptyTable": "Ningún dato disponible en esta tabla",
                    "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "sInfoPostFix": "",
                    "sSearch": "Buscar:",
                    "sUrl": "",
                    "sInfoThousands": ",",
                    "sLoadingRecords": "Cargando...",
                    "oPaginate": {
                        "sFirst": "<<",
                        "sLast": ">>",
                        "sNext": ">",
                        "sPrevious": "<"
                    },
                    "oAria": {
                        "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                    }
                },"ordering": false
            });
        });
    </script>


@endsection
