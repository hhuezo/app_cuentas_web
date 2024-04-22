@extends('menu')
@section('contenido')

    <style>
        /* The switch - the box around the slider */
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            /* Ajusta el ancho según tu preferencia */
            height: 24px;
            /* Ajusta la altura según tu preferencia */
        }

        /* Hide default HTML checkbox */
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        /* The slider */
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
            border-radius: 30px;
            /* Ajusta el radio según tu preferencia */
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 20px;
            /* Ajusta la altura según tu preferencia */
            width: 20px;
            /* Ajusta el ancho según tu preferencia */
            left: 2px;
            /* Ajusta la posición según tu preferencia */
            bottom: 2px;
            /* Ajusta la posición según tu preferencia */
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #2196F3;
        }

        input:focus+.slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked+.slider:before {
            -webkit-transform: translateX(24px);
            /* Ajusta la posición según tu preferencia */
            -ms-transform: translateX(24px);
            /* Ajusta la posición según tu preferencia */
            transform: translateX(24px);
            /* Ajusta la posición según tu preferencia */
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 30px;
            /* Ajusta el radio según tu preferencia */
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>


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
                        <a href="{{ url('prestamo_web') }}">
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-create"
                                type="button" role="tab" aria-selected="true">Salir</button></a>
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
                                        <a href="{{ url('recibo_web') }}/{{ $recibo->id }}/edit">
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
                                                            <strong>Interes: </strong>${{ $recibo->interes }}
                                                            <br>
                                                            <strong>Total:
                                                            </strong>${{ $recibo->cantidad + $recibo->interes }}
                                                            <br>
                                                            <strong>Remanente: </strong>${{ $recibo->remanente }}
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
                                    <label for="largeInput" class="form-label">Fecha primer pago</label>
                                    <div class="input-hasicon mb-xl-0 mb-3">
                                        <input type="date" name="fecha" required class="form-control" readonly
                                            value="{{ $prestamo->primer_pago }}">
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
                                    <label for="largeInput" class="form-label">Interes</label>
                                    <input type="number" name="interes" readonly class="form-control"
                                        value="{{ $prestamo->interes }}">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="largeInput" class="form-label">Tipo pago</label>
                                <input type="test" class="form-control" readonly
                                    value="{{ $prestamo->tipoPago->nombre }}">
                            </div>


                            <div class="col-md-6 mb-3">
                                <div class="input-area relative">
                                    <label for="largeInput" class="form-label">Número de pagos</label>
                                    <input type="number" name="numero_pagos" readonly class="form-control"
                                        value="{{ $prestamo->numero_pagos }}">
                                </div>
                            </div>


                            <div class="col-md-6 mb-3">
                                <div class="input-area relative">
                                    <label class="switch">
                                        <input type="checkbox" disabled name="amortizacion">
                                        <span class="slider round"></span>
                                    </label>
                                    <label for="largeInput" class="form-label">&nbsp;Amortizacion</label>
                                </div>
                            </div>


                            <div class="col-md-6 mb-3">
                                <label for="largeInput" class="form-label">Administrador</label>
                                <input type="test" class="form-control" readonly
                                    value="{{ $prestamo->administradorUser->username }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="input-area relative">
                                    <label for="largeInput" class="form-label">Pago especifico</label>
                                    <input type="number" name="pago_especifico" readonly class="form-control"
                                        value="{{ $prestamo->pago_especifico }}">
                                </div>
                            </div>


                            <div class="col-md-6 mb-3">
                                <center>
                                    <img src="data:image/png;base64,{{ $prestamo->comprobante }}" alt="Vista previa"
                                        style="max-width: 200px; height: 200px;">
                                </center>
                            </div>


                        </div>

                    </div>

                </div>
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
        </script>

    @endsection
