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

    <div class="2xl:col-span-12 lg:col-span-12 col-span-12">
        <div class="card">
            <div class="card-header flex-wrap d-flex justify-content-between">
                <div>
                    <h4 class="card-title">Editar prestamo</h4>

                </div>
                <ul class="nav nav-tabs dzm-tabs" id="myTab-six" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="{{ url('persona_web') }}">
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
                <form method="POST" action="{{ route('prestamo_catalogo.update', $prestamo->id) }}">
                    @method('PUT')
                    @csrf



                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="input-area relative">
                                <label for="largeInput" class="form-label">Codigo</label>
                                <div class="input-hasicon mb-xl-0 mb-3">
                                    <input type="number" name="codigo" required class="form-control"
                                        value="{{ $prestamo->codigo }}">
                                    <div class="icon"><i class="far fa-calendar"></i></div>
                                </div>
                            </div>

                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="largeInput" class="form-label">Persona</label>
                            <select name="persona_id" class="form-control select2">
                                @foreach ($personas as $obj)
                                    <option value="{{ $obj->id }}"
                                        {{ $prestamo->persona_id == $obj->id ? 'selected' : '' }}>{{ $obj->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-md-6 mb-3">
                            <div class="input-area relative">
                                <label for="largeInput" class="form-label">Cantidad</label>
                                <input type="number" step="0.01" name="cantidad" required class="form-control"
                                    value="{{ $prestamo->cantidad }}">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="input-area relative">
                                <label for="largeInput" class="form-label">Interes</label>
                                <input type="number" name="interes" required class="form-control"
                                    value="{{ $prestamo->interes }}">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="largeInput" class="form-label">Tipo pago</label>
                            <select name="tipo_pago_id" class="default-select form-control">
                                @foreach ($tipos_pago as $obj)
                                    <option value="{{ $obj->id }}"
                                        {{ $prestamo->tipo_pago_id == $obj->id ? 'selected' : '' }}>{{ $obj->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-md-6 mb-3">
                            <div class="input-area relative">
                                <label for="largeInput" class="form-label">Número de pagos</label>
                                <input type="number" name="numero_pagos" required class="form-control"
                                    value="{{ $prestamo->numero_pagos }}">
                            </div>
                        </div>


                        <div class="col-md-6 mb-3">
                            <div class="input-area relative">
                                <label class="switch">
                                    <input type="checkbox" name="amortizacion"
                                        {{ $prestamo->amortizacion == 1 ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                                <label for="largeInput" class="form-label">&nbsp;Amortizacion</label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="input-area relative">
                                <label class="switch">
                                    <input type="checkbox" name="estado" {{ $prestamo->estado == 2 ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                                <label for="largeInput" class="form-label">&nbsp;Finalizado</label>
                            </div>
                        </div>


                        <div class="col-md-6 mb-3">
                            <label for="largeInput" class="form-label">Administrador</label>
                            <select name="administrador" class="default-select form-control">
                                @foreach ($usuarios as $obj)
                                    <option value="{{ $obj->id }}"
                                        {{ $prestamo->administrador == $obj->id ? 'selected' : '' }}>{{ $obj->username }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="input-area relative">
                                <label for="largeInput" class="form-label">Pago especifico</label>
                                <input type="number" name="pago_especifico" class="form-control"
                                    value="{{ $prestamo->pago_especifico }}">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="input-area relative">
                                <label for="largeInput" class="form-label">Observación</label>
                                <input type="text" name="observacion" class="form-control"
                                    value="{{ $prestamo->observacion }}">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="input-area relative">
                                <label for="largeInput" class="form-label">Comprobante</label>
                                <input type="file" name="img_comprobante" id="img_comprobante" accept="image/*"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <center>
                                <img id="preview" src="data:image/png;base64,{{ $prestamo->comprobante }}"
                                    alt="Vista previa" style="max-width: 200px; height: auto;">
                            </center>
                        </div>
                        <input type="hidden" id="comprobante_base64" name="comprobante">

                        <div class="col-md-12" style="text-align: right;">
                            <button class="btn btn-primary float-right" type="submit" role="tab"
                                aria-selected="true">Aceptar</button>
                        </div>

                    </div>


                </form>
            </div>

        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header flex-wrap d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">Recibos</h4>

                        </div>
                        <ul class="nav nav-tabs dzm-tabs" id="myTab-six" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="{{ url('recibo_catalogo/create') }}/{{ $prestamo->id }}">
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modal-create" type="button" role="tab"
                                        aria-selected="true">Nuevo</button></a>
                            </li>

                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-responsive-sm">
                                <thead>
                                    <tr>
                                        <th>Opciones</th>
                                        <th>Id</th>
                                        <th>Fecha</th>
                                        <th>Cantidad</th>
                                        <th>Interes</th>
                                        <th>Remanente</th>
                                        <th>Estado</th>
                                        <th>Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($prestamo->recibos as $recibo)
                                        <tr>
                                            <td>
                                                <div class="d-flex">

                                                    <a href="{{ url('recibo_catalogo') }}/{{ $recibo->id }}/edit"
                                                        class="btn btn-info shadow btn sharp me-1"><i
                                                            class="fas fa-edit"></i></a>
                                                    &nbsp;

                                                    <a href="#" data-bs-toggle="modal"
                                                        data-bs-target="#modal-delete-{{ $recibo->id }}"
                                                        class="btn btn-danger shadow btn sharp"><i
                                                            class="fa fa-trash"></i></a>

                                                </div>
                                            </td>
                                            <td>{{ $recibo->id }}</td>
                                            <td>{{ $recibo->fecha ? date('d/m/Y', strtotime($recibo->fecha)) : '' }}</td>
                                            <td>${{ $recibo->cantidad }}</td>
                                            <td>${{ $recibo->interes }}</td>
                                            <td>${{ $recibo->remanente }}</td>
                                            <td><input type="checkbox" {{ $recibo->estado == 2 ? 'checked' : '' }}></td>
                                            <td>${{ $recibo->saldo }}</td>
                                        </tr>
                                        @include('catalogo.recibo.modal')
                                    @endforeach

                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header flex-wrap d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">Cargos</h4>

                        </div>
                        <ul class="nav nav-tabs dzm-tabs" id="myTab-six" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="{{ url('recibo_catalogo/create') }}/{{ $prestamo->id }}">
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modal-create" type="button" role="tab"
                                        aria-selected="true">Nuevo</button></a>
                            </li>

                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-responsive-sm">
                                <thead>
                                    <tr>
                                        <th>Opciones</th>
                                        <th>Id</th>
                                        <th>Fecha</th>
                                        <th>Cantidad</th>
                                        <th>Interes</th>
                                        <th>Remanente</th>
                                        <th>Estado</th>
                                        <th>Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($prestamo->recibos as $recibo)
                                        <tr>
                                            <td>
                                                <div class="d-flex">

                                                    <a href="{{ url('recibo_catalogo') }}/{{ $recibo->id }}/edit"
                                                        class="btn btn-info shadow btn sharp me-1"><i
                                                            class="fas fa-edit"></i></a>
                                                    &nbsp;

                                                    <a href="#" data-bs-toggle="modal"
                                                        data-bs-target="#modal-delete-{{ $recibo->id }}"
                                                        class="btn btn-danger shadow btn sharp"><i
                                                            class="fa fa-trash"></i></a>

                                                </div>
                                            </td>
                                            <td>{{ $recibo->id }}</td>
                                            <td>{{ $recibo->fecha ? date('d/m/Y', strtotime($recibo->fecha)) : '' }}</td>
                                            <td>${{ $recibo->cantidad }}</td>
                                            <td>${{ $recibo->interes }}</td>
                                            <td>${{ $recibo->remanente }}</td>
                                            <td><input type="checkbox" {{ $recibo->estado == 2 ? 'checked' : '' }}></td>
                                            <td>${{ $recibo->saldo }}</td>
                                        </tr>
                                        @include('catalogo.recibo.modal')
                                    @endforeach

                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </div>









    <script>
        document.getElementById('img_comprobante').addEventListener('change', function() {
            const file = this.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                const preview = document.getElementById('preview');
                preview.src = e.target.result;
                var base64WithoutPrefix = e.target.result.replace(/^data:image\/(png|jpeg);base64,/, '');

                // Asignar el valor sin el prefijo al elemento deseado
                document.getElementById('comprobante_base64').value = base64WithoutPrefix;
            };

            reader.readAsDataURL(file);
        });
    </script>


@endsection
