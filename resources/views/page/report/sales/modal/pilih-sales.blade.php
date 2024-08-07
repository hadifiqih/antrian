<!-- Modal -->
<div class="modal fade" id="pilihSales" tabindex="-1" aria-labelledby="modalPilihSales" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPilihSales">Form Sosial Media</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form id="formPilihSales" action="{{ route('sales.summaryReport') }}" method="GET" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="sales">Sales <span class="text-danger">*</span></label>
                    <select class="form-control select2" id="sales_id" name="sales_id" style="width: 100%" required>
                        <option value="" selected disabled>Pilih Sales</option>
                        @php
                            $salesAll = App\Models\Sales::all();
                        @endphp
                        @foreach ($salesAll as $item)
                            <option value="{{ $item->id }}">{{ $item->sales_name }}</option>
                        @endforeach
                    </select>
                </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
        </div>
    </div>
</div>
