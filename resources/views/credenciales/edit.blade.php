<div class="modal fade" id="modal-edit-{{$credencial->id}}">
    <div class="modal-dialog" role="document">

        <form method="POST" action="{{ route('credenciales_web.update', $credencial->id) }}">
            @method('PUT')
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar credencial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12 mb-3">
                        <div class="input-area relative">
                            <label for="largeInput" class="form-label">Sitio</label>
                            <div class="input-hasicon mb-xl-0 mb-3">
                                <input type="text" name="sitio_web" required class="form-control"
                                    value="{{ $credencial->sitio_web }}">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="input-area relative">
                            <label for="largeInput" class="form-label">Usuario</label>

                            <div class="input-group mb-3 input-warning-o">
                                <span class="input-group-text">@</span>
                                <input type="text" name="usuario" required class="form-control"
                                value="{{ $credencial->usuario }}">
                            </div>

                        
                        </div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="input-area relative">
                            <label for="largeInput" class="form-label">Constraseña</label>
                            <div class="input-hasicon mb-xl-0 mb-3">
                                <input type="text" name="password" required class="form-control"
                                    value="{{ $credencial->password }}">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="input-area relative">
                            <label for="largeInput" class="form-label">Notas</label>
                            <div class="input-hasicon mb-xl-0 mb-3">
                                <textarea class="form-control">{{ $credencial->notas }}</textarea>
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
