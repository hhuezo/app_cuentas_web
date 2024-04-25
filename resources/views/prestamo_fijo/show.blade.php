@extends('menu')
@section('contenido')




    <style>
        .card.blue-border {
            border: 2px solid blue;
        }
    </style>

    <div class="2xl:col-span-12 lg:col-span-12 col-span-12">
        <div class="card">


            <div class="card-header flex-wrap d-flex justify-content-between">
                <div>
                    <h4 class="card-title">Préstamo</h4>
                    </p>


                </div>
                <ul class="nav nav-tabs dzm-tabs" id="myTab-six" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="{{ url('prestamo_fijo_web') }}">
                            <button class="btn btn-primary btn-sm" type="button" role="tab"
                                aria-selected="true">Salir</button></a>
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

            <div class="card-body flex flex-col p-6">





                <!-- Nav tabs -->
                <div class="default-tab">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#home"><i class="la la-home me-2"></i>
                                Recibos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#profile"><i class="la la-user me-2"></i>
                                Prestamo</a>
                        </li>

                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="home" role="tabpanel">
                            <br>
                            <div class="row">
                                @foreach ($prestamo->recibos as $recibo)
                                    <div class="mb-3 col-md-4 col-sm-12">
                                        <a href="{{ url('recibo_fijo_web') }}/{{ $recibo->id }}/edit">
                                            <div class="card <?php echo $recibo->estado == 2 ? 'blue-border' : ''; ?>" id="card-title-1">
                                                <div class="card-header border-0 pb-0 ">
                                                    <h5 class="card-title">{{ date('d/m/Y', strtotime($recibo->fecha)) }}
                                                    </h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <p class="card-text"><strong>Cantidad:
                                                            </strong>${{ $recibo->cantidad }}
                                                            <br>
                                                            <strong>Observación: </strong>{{ $recibo->observacion }}
                                                            <br>
                                                            <strong>Estado:
                                                            </strong>{{ $recibo->estado == 1 ? 'Ingresado' : 'Registrado' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                    <div class="tab-pane fade" id="profile">
                        <br>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="input-area relative">
                                    <label for="largeInput" class="form-label">Fecha</label>
                                    <div class="input-hasicon mb-xl-0 mb-3">
                                        <input type="date" name="fecha" required class="form-control" readonly
                                            value="{{ $prestamo->fecha }}">
                                        <div class="icon"><i class="far fa-calendar"></i></div>
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="largeInput" class="form-label">Persona</label>
                                <input type="test" class="form-control" readonly
                                    value="{{ $prestamo->persona->nombre }}">
                            </div>


                            <div class="col-md-6 mb-3">
                                <div class="input-area relative">
                                    <label for="largeInput" class="form-label">Cantidad</label>
                                    <input type="number" name="cantidad" readonly class="form-control"
                                        value="{{ $prestamo->cantidad }}">
                                </div>
                            </div>




                            <div class="col-md-6 mb-3">
                                <div class="input-area relative">
                                    <label for="largeInput" class="form-label">Observación</label>
                                    <input type="text" name="observacion" readonly class="form-control"
                                        value="{{ $prestamo->observacion }}">
                                </div>
                            </div>





                            <div class="col-md-6 mb-3">
                                <center>
                                    <img src="data:image/png;base64,{{ $prestamo->comprobante }}" alt="Vista previa"
                                        style="max-width: 200px; height: 200px;">
                                </center>
                            </div>


                            <div class="col-md-12" style="text-align: right;">
                                <button data-bs-toggle="modal" data-bs-target="#modal-create"
                                    class="btn btn-primary float-right" type="submit" role="tab"
                                    aria-selected="true">Crear recibo</button></a>


                            </div>
                        </div>

                    </div>

                </div>
            </div>


        </div>

    </div>


    <div class="modal fade" id="modal-create">
        <div class="modal-dialog" role="document">

            <form method="POST" action="{{ url('recibo_fijo_web') }}">
                @csrf
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Crear recibo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="col-md-12 mb-3">
                            <label for="largeInput" class="form-label">Fecha</label>
                            <input type="date" name="fecha" value="{{ date('Y-m-d') }}" required
                                class="form-control">
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="input-area relative">
                                <label for="largeInput" class="form-label">Nombre</label>
                                <input type="hidden" name="prestamo_fijo_id" value="{{ $prestamo->id }}">
                                <input type="text" name="nombre" value="{{ $prestamo->persona->nombre }}" readonly
                                    class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="largeInput" class="form-label">Deuda</label>
                            <input type="text" name="remanente" value="{{ $deuda }}" readonly
                                class="form-control">
                        </div>


                        <div class="col-md-12 mb-3">
                            <label for="largeInput" class="form-label">Cantidad</label>
                            <input type="number" step="0.01" min="0.01" max="{{ $deuda }}"
                                name="cantidad" id="cantidad" required class="form-control">
                        </div>

                        <div class="col-md-12 mb-3">
                            <div class="input-area relative">
                                <label for="largeInput" class="form-label">Comprobante</label>
                                <input type="file" name="img_comprobante" id="img_comprobante" accept="image/*"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <center>
                                <img id="preview" src="#" alt="Vista previa"
                                    style="max-width: 200px; height: auto;">
                            </center>
                        </div>
                        <input type="hidden" id="comprobante_base64" name="comprobante">
                    </div>
                    &nbsp;
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Aceptar</button>
                    </div>
                </div>
            </form>


        </div>
    </div>


    <script src="{{ asset('template/js/jquery-3.6.0.min.js') }}"></script>

    <script>
        document.getElementById('img_comprobante').addEventListener('change', function() {
            const file = this.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                const preview = document.getElementById('preview');
                preview.src = e.target.result;
                document.getElementById('comprobante_base64').value = e.target.result;
            };

            reader.readAsDataURL(file);
        });

        function getDataRecibo(id) {
            var apiUrl = "{{ url('api/recibo') }}/" + id;

            $.ajax({
                url: apiUrl,
                type: "GET",
                success: function(response) {
                    // Manejar la respuesta de la API aquí
                    console.log(response);
                    document.getElementById('nombre').value = response.data['nombre'];
                    document.getElementById('remanente').value = response.data['remanente'];
                    document.getElementById('interes').value = response.data['interes'];
                    document.getElementById('cantidad').value = response.data['total'];
                },
                error: function(xhr, status, error) {
                    // Manejar errores aquí
                    console.error(xhr.responseText);
                }
            });
        }
    </script>

@endsection
