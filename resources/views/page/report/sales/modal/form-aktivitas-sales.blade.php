<!-- Modal -->
<div class="modal fade" id="modalAktivitasSales" tabindex="-1" aria-labelledby="formAktivitasSalesLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formAktivitasSalesLabel">Form Aktivitas Sales</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form id="formAktivitasSales" action="{{ route('storeSalesActivity') }}" enctype="multipart/form-data" method="POST">
                @csrf

                <div class="form-group">
                    <label for="platform">Platform <span class="text-danger">*</span></label>
                    <select class="form-control" id="platform" name="platform" required>
                        <option value="" selected disabled>Pilih Platform</option>
                        <option value="Whatsapp">Whatsapp</option>
                        <option value="Instagram">Instagram</option>
                        <option value="Facebook">Facebook</option>
                        <option value="Youtube">Youtube</option>
                        <option value="Tiktok">Tiktok</option>
                        <option value="Shopee">Shopee</option>
                        <option value="Tokopedia">Tokopedia</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="jenis_konten">Jenis Konten <span class="text-danger">*</span></label>
                    <select class="form-control" id="jenis_konten" name="jenis_konten" required>
                        <option value="" selected disabled>Pilih Jenis Konten</option>
                        <option value="Broadcast">Broadcast</option>
                        <option value="Penawaran">Penawaran</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="jumlah">Jumlah <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="jumlah" name="jumlah" placeholder="Jumlah Post" required>
                </div>

                <div class="form-group">
                    <label for="keterangan">Keterangan</label>
                    <input type="text" class="form-control" id="keterangan" name="keterangan" placeholder="Pagi/Sore">
                </div>

                <div class="form-group">
                    <label for="lampiran">Lampiran Foto <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="lampiran" name="lampiran[]" multiple="multiple">
                        <label class="custom-file-label" for="lampiran">Pilih file</label>
                      </div>
                    </div>
                </div>
                <p class="h6 text-sm">Tanggal Update : {{ date_format(now(), 'Y-m-d') }}</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary">Simpan</button>
            </div>
        </form>
        </div>
    </div>
</div>
