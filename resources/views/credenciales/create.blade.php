<div class="modal fade" id="modal-create">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{ url('credenciales_web') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar credencial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12 mb-3">
                        <div class="input-area relative">
                            <label for="largeInput" class="form-label">Sitio</label>
                            <div class="input-hasicon mb-xl-0 mb-3">
                                <input type="text" name="sitio_web" required class="form-control"
                                    value="{{ old('sitio_web') }}">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="input-area relative">
                            <label for="largeInput" class="form-label">Usuario</label>
                            <div class="input-hasicon mb-xl-0 mb-3">
                                <input type="text" name="usuario" required class="form-control"
                                    value="{{ old('usuario') }}">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="input-area relative">
                            <label for="largeInput" class="form-label">Constraseña</label>
                            <div class="input-hasicon mb-xl-0 mb-3">
                                <input type="text" name="password" required class="form-control"
                                    value="{{ old('password') }}">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="input-area relative">
                            <label for="largeInput" class="form-label">Notas</label>
                            <div class="input-hasicon mb-xl-0 mb-3">
                                <textarea class="form-control" name="notas" >{{ old('notas') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="input-area relative">
                            <label for="largeInput" class="form-label">Logo</label>
                            <input type="file" name="logo" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Aceptar</button>
                </div>
            </div>
        </form>
    </div>
</div>
