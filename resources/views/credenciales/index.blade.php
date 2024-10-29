@extends('menu')
@section('contenido')


    <div class="col-xl-12">
        <div class="card dz-card" id="accordion-six">
            <div class="card-header flex-wrap d-flex justify-content-between">
                <div>
                    <h4 class="card-title">Credenciales</h4>
                    </p>
                </div>
                <ul class="nav nav-tabs dzm-tabs" id="myTab-six" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="btn btn-primary btn-sm" type="button" role="tab" data-bs-toggle="modal"
                            data-bs-target="#modal-create" aria-selected="true">Nuevo</button>

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
                            <table id="example" class="display table" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th>Opciones</th>
                                        <th>Sitio</th>
                                        <th>Usuario</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($credenciales as $credencial)
                                        <tr>
                                            <td>
                                                <div class="d-flex">

                                                    <a href="#" data-bs-toggle="modal"
                                                        data-bs-target="#modal-edit-{{ $credencial->id }}"
                                                        class="btn btn-info shadow btn sharp me-1"><i
                                                            class="fas fa-edit"></i></a> &nbsp;

                                                    <a href="#" data-bs-toggle="modal"
                                                        data-bs-target="#modal-delete-{{ $credencial->id }}"
                                                        class="btn btn-danger shadow btn sharp"><i
                                                            class="fa fa-trash"></i></a>

                                                </div>
                                            </td>
                                            <td>{{ $credencial->sitio_web }}</td>
                                            <td>{{ $credencial->usuario }}</td>
                                        </tr>

                                        @include('credenciales.edit')

                                        @include('credenciales.modal')
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


    @include('credenciales.create')

    <script src="{{ asset('template/js/jquery-3.6.0.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#example')) {
                $('#example').DataTable().destroy();
            }
            $('#example').DataTable({
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
                },
                //"ordering": false
            });
        });
    </script>


    <script>
        document.getElementById('copyButtonUsuario').addEventListener('click', function() {
            const input = document.getElementById('usuarioInput');
            input.select(); // Selecciona el contenido del input
            input.setSelectionRange(0, 99999); // Para dispositivos móviles

            try {
            document.execCommand('copy'); // Copiar al portapapeles

            // Mostrar un toast con SweetAlert2
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '¡Usuario copiado al portapapeles!',
                showConfirmButton: false,
                timer: 1500
            });
        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'No se pudo copiar el usuario.',
            });
        }
        });

        document.getElementById('copyButtonPass').addEventListener('click', function() {
            const input = document.getElementById('passInput');
            input.select(); // Selecciona el contenido del input
            input.setSelectionRange(0, 99999); // Para dispositivos móviles

            try {
            document.execCommand('copy'); // Copiar al portapapeles

            // Mostrar un toast con SweetAlert2
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '¡Password copiado al portapapeles!',
                showConfirmButton: false,
                timer: 1500
            });
        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'No se pudo copiar el Password.',
            });
        }
        });
    </script>


@endsection
