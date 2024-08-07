<!-- Modal -->
<div class="modal fade" id="modalEditPelanggan" tabindex="-1" aria-labelledby="modalEditPelangganLabel" aria-hidden="true">
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title" id="modalEditPelangganLabel">Edit Pelanggan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        </div>
        <div class="modal-body">
            <form id="pelanggan-form" method="POST" enctype="multipart/form-data" action="">
            @csrf
            <div class="form-group">
                <label for="nama">Nama Pelanggan <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="modalNama" placeholder="Nama Pelanggan" name="namaPelanggan" required>
            </div>

            <div class="form-group">
                <label for="noHp">No. HP / WA <span class="text-danger">*</span></label>
                <input type="tel" class="form-control" id="modalTelepon" placeholder="Nomor Telepon" name="telepon" required>
            </div>

            <div class="form-group">
                <label for="alamat">Alamat <span class="text-danger">*</span></label>
                <textarea class="form-control" id="modalAlamat" placeholder="Alamat Pelanggan" name="alamat" required></textarea>
            </div>
            <div class="form-group">
                <label for="instansi">Instansi <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="modalInstansi" placeholder="Instansi Pelanggan" name="instansi" required>
                <p class="text-muted mt-2">*Jika tidak tau, beri tanda "-"</p>
            </div>
            <div class="form-group">
                <label for="infoPelanggan">Sumber Pelanggan <span class="text-danger">*</span></label>
                <select class="custom-select select2" id="infoPelanggan" name="infoPelanggan" required>
                    <option value="" selected>Pilih Sumber Pelanggan</option>
                    @foreach($infoPelanggan as $info)
                        <option value="{{ $info->id }}">{{ $info->nama_sumber }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="provinsi">Provinsi <span class="text-danger">*</span></label>
                <select class="custom-select" name="provinsi" id="modalProvinsi" required>
                    <option value="" selected>Pilih Provinsi</option>
                </select>
            </div>
            <div class="form-group" id="groupKota" style="display: none">
                <label for="kota">Kabupaten/Kota <span class="text-danger">*</span></label>
                <select class="custom-select " name="kota" id="modalKota" required>
                    <option value="" selected>Pilih Kota</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <input type="submit" class="btn btn-primary" id="submitPelanggan" value="Update"><span id="loader" class="loader" style="display: none;"></span>
        </div>
    </form>
    </div>
    </div>
</div>