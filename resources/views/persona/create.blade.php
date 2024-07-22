@extends('menu')
@section('contenido')


    <div class="2xl:col-span-12 lg:col-span-12 col-span-12">
        <div class="card">
            <div class="card-header flex-wrap d-flex justify-content-between">
                <div>
                    <h4 class="card-title">Nueva persona</h4>

                </div>
                <ul class="nav nav-tabs dzm-tabs" id="myTab-six" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="{{ url('persona') }}">
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
                <form method="POST" action="{{ url('persona') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="input-area relative">
                                <label for="largeInput" class="form-label">Nombre</label>
                                <div class="input-hasicon mb-xl-0 mb-3">
                                    <input type="text" name="nombre" required class="form-control"  onblur="this.value = this.value.toUpperCase()"
                                        value="{{ old('persona') }}">
                                </div>
                            </div>
                        </div>





                        <div class="col-md-6 mb-3">
                            <div class="input-area relative">
                                <label for="largeInput" class="form-label">Documento</label>
                                <input type="text" step="0.01" name="documento"  class="form-control"
                                    value="{{ old('documento') }}">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="input-area relative">
                                <label for="largeInput" class="form-label">Teléfono</label>
                                <input type="text" name="telefono"  class="form-control"
                                    value="{{ old('telefono') }}">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="largeInput" class="form-label">Banco</label>
                            <input type="text" name="banco"  class="form-control" value="{{ old('banco') }}">
                        </div>


                        <div class="col-md-6 mb-3">
                            <label for="largeInput" class="form-label">Cuenta</label>
                            <input type="text" name="cuenta" class="form-control" value="{{ old('cuenta') }}">
                        </div>

                        <div class="col-md-12" style="text-align: right;">
                            <button class="btn btn-primary float-right" type="submit" role="tab"
                                aria-selected="true">Aceptar</button>
                        </div>

                    </div>


                </form>
            </div>

        </div>
    </div>
@endsection
