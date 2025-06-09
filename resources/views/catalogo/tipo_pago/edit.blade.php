@extends('menu')
@section('contenido')



    <div class="2xl:col-span-12 lg:col-span-12 col-span-12">
        <div class="card">
            <div class="card-header flex-wrap d-flex justify-content-between">
                <div>
                    <h4 class="card-title">Editar cargo</h4>

                </div>
                <ul class="nav nav-tabs dzm-tabs" id="myTab-six" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="{{ url('prestamo_catalogo') }}/{{$cargo->prestamo_id}}/edit">
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
                <form method="POST" action="{{ route('cargo_catalogo.update', $cargo->id) }}">
                    @method('PUT')
                    @csrf


                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="input-area relative">
                                <label for="largeInput" class="form-label">Prestamo</label>
                                <div class="input-hasicon mb-xl-0 mb-3">
                                    <input type="text" name="prestamo_id" class="form-control" readonly
                                        value="{{ $cargo->prestamo_id }}">
                                </div>
                            </div>

                        </div>




                        <div class="col-md-6 mb-3">
                            <div class="input-area relative">
                                <label for="largeInput" class="form-label">Fecha</label>
                                <input type="date" name="fecha" required class="form-control" required
                                    value="{{ $cargo->fecha }}">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="input-area relative">
                                <label for="largeInput" class="form-label">Cantidad</label>
                                <input type="number" name="cantidad" step="0.01" required class="form-control"
                                    value="{{ $cargo->cantidad }}">
                            </div>
                        </div>






                        <div class="col-md-6 mb-3">
                            <div class="input-area relative">
                                <label for="largeInput" class="form-label">Saldo</label>
                                <input type="number" name="saldo" step="0.01" class="form-control"
                                    value="{{ $cargo->saldo }}">
                            </div>
                        </div>


                        <div class="col-md-6 mb-3">
                            <div class="input-area relative">
                                <label for="largeInput" class="form-label">Observación</label>
                                <input type="text" name="observacion"  class="form-control"
                                    value="{{ $cargo->observacion }}">
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
                                <img id="preview" src="data:image/png;base64,{{ $cargo->comprobante }}" alt="Vista previa"
                                    style="max-width: 200px; height: auto;">
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
