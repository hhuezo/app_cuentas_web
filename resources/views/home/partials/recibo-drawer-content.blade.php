<form id="reciboDrawerForm" method="POST" action="{{ url('recibo_web/' . $recibo->id) }}">
    @csrf
    @method('PUT')

    @if ($pagosAnterioresPendientes->isNotEmpty())
        <div class="alert alert-danger">
            Existen pagos anteriores sin finalizar para este préstamo:
            <ul class="mb-0 mt-2">
                @foreach ($pagosAnterioresPendientes as $pagoPendiente)
                    <li>{{ date('d/m/Y', strtotime($pagoPendiente->fecha)) }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-12 mb-3">
            <label class="form-label">Fecha</label>
            <input type="date" name="fecha" required class="form-control" value="{{ $recibo->fecha }}">
        </div>

        <div class="col-12 mb-3">
            <label class="form-label">Persona</label>
            <input type="text" readonly class="form-control" value="{{ $recibo->prestamo->persona->nombre }}">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">Cantidad</label>
            <input type="number" name="cantidad" step="0.01" class="form-control" required
                value="{{ $recibo->cantidad }}">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">Interés</label>
            <input type="number" name="interes" step="0.01" class="form-control" required
                value="{{ $recibo->interes }}">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">Remanente</label>
            <input type="text" readonly class="form-control" value="${{ number_format($recibo->remanente, 2, '.', ',') }}">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">Estado actual</label>
            <input type="text" readonly class="form-control"
                value="{{ $recibo->estado == 1 ? 'Pendiente' : 'Pagado' }}">
        </div>

        <div class="col-12 mb-3">
            <label class="form-label">Comprobante</label>
            <input type="file" name="img_comprobante" id="reciboDrawerComprobante" accept="image/*"
                class="form-control">
        </div>

        @if ($recibo->comprobante_url)
            <div class="col-12 mb-3 text-center">
                <img id="reciboDrawerPreview" src="{{ asset('comprobantes/' . $recibo->comprobante_url) }}"
                    alt="Comprobante" style="max-width: 100%; height: auto;">
            </div>
        @else
            <div class="col-12 mb-3 text-center">
                <img id="reciboDrawerPreview" src="" alt="Comprobante" style="max-width: 100%; height: auto; display: none;">
            </div>
        @endif

        <input type="hidden" id="reciboDrawerComprobanteBase64" name="comprobante">

        <div class="col-12 mb-4">
            <div class="d-flex align-items-center gap-2">
                <label class="switch mb-0">
                    <input type="checkbox" name="estado" {{ $recibo->estado == 2 ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
                <label class="form-label mb-0">Finalizado</label>
            </div>
        </div>

        <div class="col-12 d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-danger" data-bs-dismiss="offcanvas">Cerrar</button>
            <button type="submit" class="btn btn-primary" id="reciboDrawerSubmit">Guardar</button>
        </div>
    </div>
</form>
