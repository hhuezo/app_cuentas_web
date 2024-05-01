<div class="modal fade" id="modal-create">
    <div class="modal-dialog" role="document">

        <form method="POST" action="{{ url('recibo_web') }}">
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
                        <input type="date" name="fecha" value="{{date('Y-m-d')}}"  required class="form-control">
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="input-area relative">
                            <label for="largeInput" class="form-label">Nombre</label>
                            <input type="hidden" name="prestamo_id" value="{{$prestamo->id}}">
                            <input type="text" name="nombre" id="nombre" readonly class="form-control">
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="largeInput" class="form-label">Deuda</label>
                        <input type="text" name="remanente" id="remanente" readonly class="form-control">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="largeInput" class="form-label">Interés</label>
                        <input type="number" step="0.01" name="interes" id="interes" required class="form-control">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="largeInput" class="form-label">Cantidad</label>
                        <input type="number" step="0.01" name="cantidad" id="cantidad" required class="form-control">
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
