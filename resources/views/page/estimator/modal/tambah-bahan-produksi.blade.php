<div class="modal" tabindex="-1" id="modalTambahBahan" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Bahan Produksi</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form action="{{ route('tambahBahanProduksi') }}" method="POST">
                @csrf
                <input type="hidden" name="idBarang" value="{{ $barang->id }}">
                <input type="hidden" name="ticketOrder" value="{{ $barang->ticket_order }}">
                <div class="form-group">
                    <label for="nama_bahan">Nama Bahan</label>
                    <input type="text" name="nama_bahan" id="nama_bahan" class="form-control">
                </div>
                <div class="form-group">
                    <label for="harga">Harga</label>
                    <input type="number" name="harga" id="harga" class="form-control">
                </div>
                <div class="form-group">
                    <label for="qty">Qty</label>
                    <input type="number" name="qty" id="qty" class="form-control">
                </div>
                <div class="form-group">
                    <label for="note">Keterangan</label>
                    <textarea name="note" id="note" class="form-control"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </div>
  </div>