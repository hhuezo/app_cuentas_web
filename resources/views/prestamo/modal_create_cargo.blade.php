<div class="modal fade" id="modal-create-cargo">
    <div class="modal-dialog" role="document">

        <form method="POST" action="{{ url('cargo_web') }}">
            @csrf
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Crear cargo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12 mb-3">
                        <label for="largeInput" class="form-label">Fecha</label>
                        <input type="date" name="fecha" value="{{date('Y-m-d')}}"  required class="form-control">
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="input-area relative">
                            <label for="largeInput" class="form-label">Nombre</label>
                            <input type="hidden" name="prestamo_id" value="{{$prestamo->id}}">
                            <input type="text" name="nombre"  value="{{$prestamo->persona->nombre}}"  readonly class="form-control">
                        </div>
                    </div>



                    <div class="col-md-12 mb-3">
                        <label for="largeInput" class="form-label">Cantidad</label>
                        <input type="number" step="0.01" name="cantidad" required class="form-control">
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="input-area relative">
                            <label for="largeInput" class="form-label">Observacion</label>
                            <input type="text" name="observacion" accept="image/*"
                                class="form-control">
                        </div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="input-area relative">
                            <label for="largeInput" class="form-label">Comprobante</label>
                            <input type="file" name="img_comprobante" id="img_comprobante_cargo" accept="image/*"
                                class="form-control">
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <center>
                            <img id="preview_cargo" src="#" alt="Vista previa"
                                style="max-width: 200px; height: auto;">
                        </center>
                    </div>
                    <input type="hidden" id="comprobante_base64_cargo" name="comprobante">
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
