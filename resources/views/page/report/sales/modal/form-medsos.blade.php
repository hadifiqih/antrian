<!-- Modal -->
<div class="modal fade" id="modalMedsos" tabindex="-1" aria-labelledby="formMedsosLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formMedsosLabel">Form Sosial Media</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form id="formUpdateFollowers" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="platform">Platform</label>
                    <select class="form-control" id="platform" name="platform" >
                        <option value="">Pilih Platform</option>
                        <option value="Instagram">Instagram</option>
                        <option value="Facebook">Facebook</option>
                        <option value="Youtube">Youtube</option>
                        <option value="Tiktok">Tiktok</option>
                        <option value="Shopee">Shopee</option>
                        <option value="Tokopedia">Tokopedia</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="akun">Akun</label>
                    <select class="form-control select2" id="akun" name="akun" style="width: 100%">

                    </select>
                </div>
                <div class="form-group">
                    <label for="followers">Jumlah Pengikut/Teman (Per Hari Ini)</label>
                    <input type="number" class="form-control" id="followers" name="followers" placeholder="Jumlah Followers">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
        </div>
    </div>
</div>
