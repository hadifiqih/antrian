<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form e-SPK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Roboto Mono', monospace;
            font-size: 14px;
        }

        .spesifikasi {
          white-space: pre-line;
        }
    </style>

</head>
<body>
  <div class="container-fluid">
    <div class="row mt-5">
      <div class="col-4"></div>
        <div class="col-4 text-center mt-5">
            <!-- Button trigger modal -->
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalspk">
            Buka Form e-SPK
          </button>
        </div>
      <div class="col-4"></div>
    </div>
  </div>

<!-- Modal -->
<div class="modal fade" id="modalspk" tabindex="-1" aria-labelledby="modalspkLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="modalspkLabel">Form e-SPK</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="printedSPK" class="container">
          <h4 class="text-center mt-3"><strong>Surat Perintah Kerja (e-SPK)</strong></h4>

          <div class="row table-responsive">
            <table class="table table-bordered table-striped mt-3">
              <tr class="bg-dark">
                <td class="text-center text-white" colspan="4">No. SPK : SPK-{{ $antrian->ticket_order }}</td>
              </tr>

              <tr>
                <td>Mulai</td>
                <td><strong>{{ $dataKerja->tgl_mulai }}</strong></td>
                <td>Selesai</td>
                <td><strong>{{ $dataKerja->tgl_selesai }}</strong></td>
              </tr>

              <tr class="bg-danger">
                <td class="text-center text-white" colspan="4">Pelanggan</td>
              </tr>

              <tr>
                <td>Customer</td>
                <td colspan="3"><strong>{{ $customer->nama }}</strong></td>
              </tr>

              <tr>
                <td>Alamat</td>
                <td colspan="3"><strong>{{ $customer->alamat }}</strong></td>
              </tr>

              <tr>
                <td>Instansi</td>
                <td colspan="3"><strong>{{ $customer->instansi }}</strong></td>
              </tr>

              <tr>
                <td>Telepon</td>
                <td colspan="3"><strong>{{ $customer->telepon }}</strong></td>
              </tr>

              <tr>
                <td>Sumber Pelanggan</td>
                <td colspan="3"><strong>{{ $customer->frekuensi_order > 1 ? 'Repeat Order' : $customer->infoPelanggan }}</strong></td>
              </tr>

              <tr class="bg-dark">
                <td class="text-center text-white" colspan="4">Gambar ACC Desain</td>
              </tr>

              <tr>
                <td class="text-center" colspan="4">
                  <div class="row justify-content-center">
                  @foreach ($barang as $item)
                    <div class="col-md-3">
                      @if($item->designQueue && $item->designQueue->file_cetak)
                        <img src="{{ asset('storage/acc_desain/'. $item->designQueue->acc_desain) }}" alt="Gambar ACC {{ $item->id }}" width="200px">
                        <p class="text-center">{{ $item->job->job_name }}</p>
                      @else
                        <img class="text-center" alt="Gambar ACC {{ $item->id }}" width="200px">
                        <p class="text-center">{{ $item->job->job_name }}</p>
                      @endif
                    </div>
                  @endforeach
                </div>
                </td>
              </tr>

              <tr class="bg-dark">
                <td class="text-center text-white" colspan="4">Daftar Pekerjaan</td>
              </tr>

              <tr>
                <td class="text-center"><strong>Nama Pekerjaan</strong></td>
                <td class="text-center"><strong>Jumlah</strong></td>
                <td class="text-center"><strong>Keterangan</strong></td>
                <td class="text-center"><strong>Desainer</strong></td>
              </tr>

              @foreach ($barang as $item)
              <tr>
                <td>{{ $item->job->job_name }}</td>
                <td class="text-center">{{ $item->qty }}</td>
                <td class="spesifikasi">{{ $item->note }}</td>
                <td>{{ isset($item->designQueue->designer->name) ? $item->designQueue->designer->name : '-' }}</td>
              </tr>
              @endforeach

              @foreach ($barang as $item)
              <tr class="bg-success text-white">
                <td colspan="4" class="text-center text-white">Penugasan - {{ $item->job->job_name }}</td>
              </tr>

              <tr>
                <td><strong>Operator</strong></td>
                <td colspan="2"><strong>Finishing</strong></td>
                <td><strong>Quality Control</strong></td>
              </tr>

              <tr>
                <td>
                  @php
                    $operator = \App\Models\DataKerja::where('ticket_order', $item->ticket_order)->where('barang_id', $item->id)->pluck('operator_id')->first();
                    // explode string
                    $explode = explode(',', $operator);
                    $operatorNames = [];
                    // for each
                    foreach ($explode as $value) {
                        if ($value == 'r') {
                            $operatorNames[] = 'Rekanan';
                        } else {
                            $employee = App\Models\Employee::find($value);
                            if ($employee) {
                                $operatorNames[] = $employee->name;
                            } else {
                                $operatorNames[] = 'Tidak Ditemukan';
                            }
                        }
                    }
                    // implode with ', <br>'
                    echo '- ' . implode(', <br>- ', $operatorNames);
                @endphp
                </td>

                <td colspan="2">
                  @php
                    $finishing = \App\Models\DataKerja::where('ticket_order', $item->ticket_order)->where('barang_id', $item->id)->pluck('finishing_id')->first();
                    // explode string
                    $explode = explode(',', $finishing);
                    $finishingNames = [];
                    // for each
                    foreach ($explode as $value) {
                        if ($value == 'r') {
                            $finishingNames[] = 'Rekanan';
                        } else {
                            $employee = \App\Models\Employee::find($value);
                            if ($employee) {
                                $finishingNames[] = $employee->name;
                            } else {
                                $finishingNames[] = 'Tidak Ditemukan';
                            }
                        }
                    }
                    // implode with ', <br>'
                    echo '- ' . implode(', <br>- ', $finishingNames);
                @endphp
                </td>
                <td>
                  @php
                    $qc = \App\Models\DataKerja::where('ticket_order', $item->ticket_order)->where('barang_id', $item->id)->pluck('qc_id')->first();
                    // explode string
                    $explode = explode(',', $qc);
                    $qcNames = [];
                    // for each
                    foreach ($explode as $value) {
                        $employee = \App\Models\Employee::find($value);
                        if ($employee) {
                            $qcNames[] = $employee->name;
                        } else {
                            $qcNames[] = 'Tidak Ditemukan';
                        }
                    }
                    // implode with ', <br>'
                    echo '- ' . implode(', <br>- ', $qcNames);
                @endphp
                </td>
              </tr>

              <tr>
                @php
                  $catatan = \App\Models\DataKerja::where('ticket_order', $item->ticket_order)->where('barang_id', $item->id)->pluck('admin_note')->first();
                @endphp
                <td class="text-center">Catatan</td>
                <td colspan="3">{{ $catatan ?? '-' }}</td>
              </tr>
              @endforeach

            </table>
          </div>
      </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
        <button id="downloadButton" type="button" class="btn btn-primary">Unduh</button>
      </div>
    </div>
  </div>
</div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" integrity="sha512-BNaRQnYJYiPSqHHDb58B0yaPfCu+Wgds8Gp/gU33kqBtgNS4tSPHuGibyoeqMV/TJlSKda6FXzoEyYGjTe+vXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script>
    $(document).ready(function(){
      $('#modalspk').modal('show');

      $('#downloadButton').click(function() {
          // convert printedSPK to image with html2canvas high resolution
          html2canvas(document.getElementById('printedSPK'), { scale: 2 })
            .then(function(canvas) {
              var link = document.createElement('a');
              link.href = canvas.toDataURL('image/png');
              link.download = 'e-SPK_'+ {{ $antrian->ticket_order }} +'.png';
              link.click();
            });
        });
      });
  </script>
</body>
</html>
