@extends('menu')
@section('contenido')


    <div class="2xl:col-span-12 lg:col-span-12 col-span-12">
        <div class="card">
            <div class="card-header flex-wrap d-flex justify-content-between">
                <div>
                    <h4 class="card-title">Editar credencial</h4>

                </div>
                <ul class="nav nav-tabs dzm-tabs" id="myTab-six" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="{{ url('credenciales_web') }}">
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
                <form method="POST" action="{{ route('credenciales_web.update', $credencial->id) }}">
                    @method('PUT')
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="input-area relative">
                                <label for="largeInput" class="form-label">Sitio</label>
                                <div class="input-hasicon mb-xl-0 mb-3">
                                    <input type="text" name="sitio_web" required class="form-control"
                                        value="{{ $credencial->sitio_web }}">
                                </div>
                            </div>
                        </div>





                        <div class="col-md-6 mb-3">
                            <div class="input-area relative">
                                <label for="largeInput" class="form-label">Usuario</label>
                                <div class="input-group mb-3 input-warning-o">
                                    <button type="button" class="input-group-text btn-primary" id="copyButtonUsuario">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                            viewBox="0 0 24 24">
                                            <path fill="white" fill-rule="evenodd"
                                                d="M3 3a1 1 0 0 1 1-1h12a1 1 0 1 1 0 2H5v12a1 1 0 1 1-2 0zm4 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v12a3 3 0 0 1-3 3h-8a3 3 0 0 1-3-3z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <input type="text" name="usuario" required class="form-control"
                                        value="{{ $credencial->usuario }}" id="usuarioInput">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="input-area relative">
                                <label for="largeInput" class="form-label">Constraseña</label>
                                <div class="input-group mb-3 input-warning-o">
                                    <button type="button" class="input-group-text  btn-primary"  id="copyButtonPass">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"><path fill="white" fill-rule="evenodd" d="M3 3a1 1 0 0 1 1-1h12a1 1 0 1 1 0 2H5v12a1 1 0 1 1-2 0zm4 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v12a3 3 0 0 1-3 3h-8a3 3 0 0 1-3-3z" clip-rule="evenodd"/></svg>
                                    </button>
                                    <input type="text" name="password" required class="form-control"
                                    value="{{ $credencial->password }}" id="passInput">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="largeInput" class="form-label">Notas</label>
                            <textarea class="form-control" name="notas">{{ $credencial->notas }}</textarea>
                        </div>


                        <div class="col-md-6 mb-3">
                            <label for="largeInput" class="form-label">Logo</label>
                            <input type="file" name="logo" class="form-control">
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
