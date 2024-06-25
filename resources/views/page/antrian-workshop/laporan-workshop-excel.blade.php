<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Biaya Produksi</title>
    </head>
    <body>
        @php
        $data = \App\Models\Barang::with(['dataKerja', 'job', 'user', 'designQueue'])
                        ->where('ticket_order', '!=', null)
                        ->get();
        // Kumpulkan semua ID employee yang unik
        $employeeIds = [];
        foreach ($data as $antrian) {
            $employeeIds = array_merge($employeeIds, explode(',', $antrian->dataKerja->operator_id ?? ''));
            $employeeIds = array_merge($employeeIds, explode(',', $antrian->dataKerja->finishing_id ?? ''));
            $employeeIds = array_merge($employeeIds, explode(',', $antrian->dataKerja->qc_id ?? ''));
        }
        $employeeIds = array_unique(array_filter($employeeIds, function($id) { return $id !== 'r'; }));

        // Ambil data semua employee yang dibutuhkan sekaligus
        $employees = \App\Models\Employee::whereIn('id', $employeeIds)->pluck('name', 'id');
        @endphp

        <table>
            <tbody>
                <tr>
                    <th colspan="13">Stempel</th>
                </tr>
                <tr>
                    <td>No</td>
                    <td>Tanggal</td>
                    <td>Tiket Order</td>
                    <td>Sales</td>
                    <td>Nama Produk</td>
                    <td>Qty</td>
                    <td>Mulai</td>
                    <td>Selesai</td>
                    <td>Desainer</td>
                    <td>Operator</td>
                    <td>Finishing</td>
                    <td>QC</td>
                    <td>Omset</td>
                </tr>
                @foreach ($stempels as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->antrian->created_at ?? 'Belum ditugaskan' }}</td>
                    <td>{{ $item->ticket_order }}</td>
                    <td>{{ $item->user->sales->sales_name }}</td>
                    <td>{{ $item->job->job_name }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ $item->dataKerja->tgl_mulai ?? 'Belum ditugaskan' }}</td>
                    <td>{{ $item->dataKerja->tgl_selesai ?? 'Belum ditugaskan' }}</td>
                    <td>{{ $item->designQueue->designer->name ?? 'Kosong' }}</td>
                    @php
                    if($item && $item->dataKerja){
                        $operatorIds = array_filter(explode(',', $item->dataKerja->operator_id ?? ''));
                        $finishingIds = array_filter(explode(',', $item->dataKerja->finishing_id ?? ''));
                        $qcIds = array_filter(explode(',', $item->dataKerja->qc_id ?? ''));

                        $kumpulanOperator = collect($operatorIds)->map(function($id) use ($employees) {
                            return $id === 'r' ? "Rekanan" : $employees->get($id, '-');
                        })->implode(', ');
                        $kumpulanOperator = $kumpulanOperator ?: 'OPERATOR KOSONG';

                        $kumpulanFinishing = collect($finishingIds)->map(function($id) use ($employees) {
                            return $id === 'r' ? "Rekanan" : $employees->get($id, '-');
                        })->implode(', ');
                        $kumpulanFinishing = $kumpulanFinishing ?: 'FINISHING KOSONG';

                        $kumpulanQc = collect($qcIds)->map(function($id) use ($employees) {
                            return $id === 'r' ? "Rekanan" : $employees->get($id, '-');
                        })->implode(', ');
                        $kumpulanQc = $kumpulanQc ?: 'QC KOSONG';
                    } else {
                        $kumpulanOperator = 'OPERATOR KOSONG';
                        $kumpulanFinishing = 'FINISHING KOSONG';
                        $kumpulanQc = 'QC KOSONG';
                    }
                    @endphp
                    <td>{{ $kumpulanOperator }}</td>
                    <td>{{ $kumpulanFinishing }}</td>
                    <td>{{ $kumpulanQc }}</td>
                    <td>{{ $item->price * $item->qty }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="8"></td>
                    <td colspan="4">Total Omset</td>
                    <td>{{ $totalStempel }}</td>
                </tr>
            </tbody>
        </table>

        <table>
            <tbody>
                <tr>
                    <th colspan="13">Non Stempel</th>
                </tr>
                <tr>
                    <td>No</td>
                    <td>Tanggal</td>
                    <td>Tiket Order</td>
                    <td>Sales</td>
                    <td>Nama Produk</td>
                    <td>Qty</td>
                    <td>Mulai</td>
                    <td>Selesai</td>
                    <td>Desainer</td>
                    <td>Operator</td>
                    <td>Finishing</td>
                    <td>QC</td>
                    <td>Omset</td>
                </tr>
                @foreach ($nonStempels as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->antrian->created_at ?? 'Belum ditugaskan' }}</td>
                    <td>{{ $item->ticket_order }}</td>
                    <td>{{ $item->user->sales->sales_name }}</td>
                    <td>{{ $item->job->job_name }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ $item->dataKerja->tgl_mulai ?? 'Belum ditugaskan' }}</td>
                    <td>{{ $item->dataKerja->tgl_selesai ?? 'Belum ditugaskan' }}</td>
                    <td>{{ $item->designQueue->designer->name ?? 'Kosong' }}</td>
                    @php
                    if($item && $item->dataKerja){
                        $operatorIds = array_filter(explode(',', $item->dataKerja->operator_id ?? ''));
                        $finishingIds = array_filter(explode(',', $item->dataKerja->finishing_id ?? ''));
                        $qcIds = array_filter(explode(',', $item->dataKerja->qc_id ?? ''));

                        $kumpulanOperator = collect($operatorIds)->map(function($id) use ($employees) {
                            return $id === 'r' ? "Rekanan" : $employees->get($id, '-');
                        })->implode(', ');
                        $kumpulanOperator = $kumpulanOperator ?: 'OPERATOR KOSONG';

                        $kumpulanFinishing = collect($finishingIds)->map(function($id) use ($employees) {
                            return $id === 'r' ? "Rekanan" : $employees->get($id, '-');
                        })->implode(', ');
                        $kumpulanFinishing = $kumpulanFinishing ?: 'FINISHING KOSONG';

                        $kumpulanQc = collect($qcIds)->map(function($id) use ($employees) {
                            return $id === 'r' ? "Rekanan" : $employees->get($id, '-');
                        })->implode(', ');
                        $kumpulanQc = $kumpulanQc ?: 'QC KOSONG';
                    } else {
                        $kumpulanOperator = 'OPERATOR KOSONG';
                        $kumpulanFinishing = 'FINISHING KOSONG';
                        $kumpulanQc = 'QC KOSONG';
                    }
                    @endphp
                    <td>{{ $kumpulanOperator }}</td>
                    <td>{{ $kumpulanFinishing }}</td>
                    <td>{{ $kumpulanQc }}</td>
                    <td>{{ $item->price * $item->qty }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="8"></td>
                    <td colspan="4">Total Omset</td>
                    <td>{{ $totalNonStempel }}</td>
                </tr>
            </tbody>
        </table>

        <table>
            <tbody>
                <tr>
                    <th colspan="13">Advertising</th>
                </tr>
                <tr>
                    <td>No</td>
                    <td>Tanggal</td>
                    <td>Tiket Order</td>
                    <td>Sales</td>
                    <td>Nama Produk</td>
                    <td>Qty</td>
                    <td>Mulai</td>
                    <td>Selesai</td>
                    <td>Desainer</td>
                    <td>Operator</td>
                    <td>Finishing</td>
                    <td>QC</td>
                    <td>Omset</td>
                </tr>
                @foreach ($advertisings as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->antrian->created_at ?? 'Belum ditugaskan' }}</td>
                    <td>{{ $item->ticket_order }}</td>
                    <td>{{ $item->user->sales->sales_name }}</td>
                    <td>{{ $item->job->job_name }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ $item->dataKerja->tgl_mulai ?? 'Belum ditugaskan' }}</td>
                    <td>{{ $item->dataKerja->tgl_selesai ?? 'Belum ditugaskan' }}</td>
                    <td>{{ $item->designQueue->designer->name ?? 'Kosong' }}</td>
                    @php 
                    if($item && $item->dataKerja){
                        $operatorIds = array_filter(explode(',', $item->dataKerja->operator_id ?? ''));
                        $finishingIds = array_filter(explode(',', $item->dataKerja->finishing_id ?? ''));
                        $qcIds = array_filter(explode(',', $item->dataKerja->qc_id ?? ''));

                        $kumpulanOperator = collect($operatorIds)->map(function($id) use ($employees) {
                            return $id === 'r' ? "Rekanan" : $employees->get($id, '-');
                        })->implode(', ');
                        $kumpulanOperator = $kumpulanOperator ?: 'OPERATOR KOSONG';

                        $kumpulanFinishing = collect($finishingIds)->map(function($id) use ($employees) {
                            return $id === 'r' ? "Rekanan" : $employees->get($id, '-');
                        })->implode(', ');
                        $kumpulanFinishing = $kumpulanFinishing ?: 'FINISHING KOSONG';

                        $kumpulanQc = collect($qcIds)->map(function($id) use ($employees) {
                            return $id === 'r' ? "Rekanan" : $employees->get($id, '-');
                        })->implode(', ');
                        $kumpulanQc = $kumpulanQc ?: 'QC KOSONG';
                    } else {
                        $kumpulanOperator = 'OPERATOR KOSONG';
                        $kumpulanFinishing = 'FINISHING KOSONG';
                        $kumpulanQc = 'QC KOSONG';
                    }
                    @endphp
                    <td>{{ $kumpulanOperator }}</td>
                    <td>{{ $kumpulanFinishing }}</td>
                    <td>{{ $kumpulanQc }}</td>
                    <td>{{ $item->price * $item->qty }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="8"></td>
                    <td colspan="4">Total Omset</td>
                    <td>{{ $totalAdvertising }}</td>
                </tr>
            </tbody>
        </table>

        <table>
            <tbody>
                <tr>
                    <th colspan="13">Digital Printing</th>
                </tr>
                <tr>
                    <td>No</td>
                    <td>Tanggal</td>
                    <td>Tiket Order</td>
                    <td>Sales</td>
                    <td>Nama Produk</td>
                    <td>Qty</td>
                    <td>Mulai</td>
                    <td>Selesai</td>
                    <td>Desainer</td>
                    <td>Operator</td>
                    <td>Finishing</td>
                    <td>QC</td>
                    <td>Omset</td>
                </tr>
                @foreach ($digitals as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->antrian->created_at ?? 'Belum ditugaskan' }}</td>
                    <td>{{ $item->ticket_order }}</td>
                    <td>{{ $item->user->sales->sales_name }}</td>
                    <td>{{ $item->job->job_name }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ $item->dataKerja->tgl_mulai ?? 'Belum ditugaskan' }}</td>
                    <td>{{ $item->dataKerja->tgl_selesai ?? 'Belum ditugaskan' }}</td>
                    <td>{{ $item->designQueue->designer->name ?? 'Kosong' }}</td>
                    @php 
                    if($item && $item->dataKerja){
                        $operatorIds = array_filter(explode(',', $item->dataKerja->operator_id ?? ''));
                        $finishingIds = array_filter(explode(',', $item->dataKerja->finishing_id ?? ''));
                        $qcIds = array_filter(explode(',', $item->dataKerja->qc_id ?? ''));

                        $kumpulanOperator = collect($operatorIds)->map(function($id) use ($employees) {
                            return $id === 'r' ? "Rekanan" : $employees->get($id, '-');
                        })->implode(', ');
                        $kumpulanOperator = $kumpulanOperator ?: 'OPERATOR KOSONG';

                        $kumpulanFinishing = collect($finishingIds)->map(function($id) use ($employees) {
                            return $id === 'r' ? "Rekanan" : $employees->get($id, '-');
                        })->implode(', ');
                        $kumpulanFinishing = $kumpulanFinishing ?: 'FINISHING KOSONG';

                        $kumpulanQc = collect($qcIds)->map(function($id) use ($employees) {
                            return $id === 'r' ? "Rekanan" : $employees->get($id, '-');
                        })->implode(', ');
                        $kumpulanQc = $kumpulanQc ?: 'QC KOSONG';
                    } else {
                        $kumpulanOperator = 'OPERATOR KOSONG';
                        $kumpulanFinishing = 'FINISHING KOSONG';
                        $kumpulanQc = 'QC KOSONG';
                    }
                    @endphp
                    <td>{{ $kumpulanOperator }}</td>
                    <td>{{ $kumpulanFinishing }}</td>
                    <td>{{ $kumpulanQc }}</td>
                    <td>{{ $item->price * $item->qty }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="8"></td>
                    <td colspan="4">Total Omset</td>
                    <td>{{ $totalDigital }}</td>
                </tr>
            </tbody>
        </table>

        <table>
            <tbody>
                <tr>
                    <th colspan="13">Jasa Servis</th>
                </tr>
                <tr>
                    <td>No</td>
                    <td>Tanggal</td>
                    <td>Tiket Order</td>
                    <td>Sales</td>
                    <td>Nama Produk</td>
                    <td>Qty</td>
                    <td>Mulai</td>
                    <td>Selesai</td>
                    <td>Desainer</td>
                    <td>Operator</td>
                    <td>Finishing</td>
                    <td>QC</td>
                    <td>Omset</td>
                </tr>
                @foreach ($servis as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->antrian->created_at ?? 'Belum ditugaskan' }}</td>
                    <td>{{ $item->ticket_order }}</td>
                    <td>{{ $item->user->sales->sales_name }}</td>
                    <td>{{ $item->job->job_name }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ $item->dataKerja->tgl_mulai ?? 'Belum ditugaskan' }}</td>
                    <td>{{ $item->dataKerja->tgl_selesai ?? 'Belum ditugaskan' }}</td>
                    <td>{{ $item->designQueue->designer->name ?? 'Kosong' }}</td>
                    @php 
                    if($item && $item->dataKerja){
                        $operatorIds = array_filter(explode(',', $item->dataKerja->operator_id ?? ''));
                        $finishingIds = array_filter(explode(',', $item->dataKerja->finishing_id ?? ''));
                        $qcIds = array_filter(explode(',', $item->dataKerja->qc_id ?? ''));

                        $kumpulanOperator = collect($operatorIds)->map(function($id) use ($employees) {
                            return $id === 'r' ? "Rekanan" : $employees->get($id, '-');
                        })->implode(', ');
                        $kumpulanOperator = $kumpulanOperator ?: 'OPERATOR KOSONG';

                        $kumpulanFinishing = collect($finishingIds)->map(function($id) use ($employees) {
                            return $id === 'r' ? "Rekanan" : $employees->get($id, '-');
                        })->implode(', ');
                        $kumpulanFinishing = $kumpulanFinishing ?: 'FINISHING KOSONG';

                        $kumpulanQc = collect($qcIds)->map(function($id) use ($employees) {
                            return $id === 'r' ? "Rekanan" : $employees->get($id, '-');
                        })->implode(', ');
                        $kumpulanQc = $kumpulanQc ?: 'QC KOSONG';
                    } else {
                        $kumpulanOperator = 'OPERATOR KOSONG';
                        $kumpulanFinishing = 'FINISHING KOSONG';
                        $kumpulanQc = 'QC KOSONG';
                    }
                    @endphp
                    <td>{{ $kumpulanOperator }}</td>
                    <td>{{ $kumpulanFinishing }}</td>
                    <td>{{ $kumpulanQc }}</td>
                    <td>{{ $item->price * $item->qty }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="8"></td>
                    <td colspan="4">Total Omset</td>
                    <td>{{ $totalServis }}</td>
                </tr>
            </tbody>
        </table>
    </body>
</html>