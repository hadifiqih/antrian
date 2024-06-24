<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Biaya Produksi</title>
    </head>
    <body>
        <table>
            <thead>
                <tr>
                    <th colspan="12">Stempel</th>
                </tr>
            </thead>
            <tbody>
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
                    <td>{{ $item->dataKerja->tgl_mulai }}</td>
                    <td>{{ $item->ticket_order }}</td>
                    <td>{{ $item->user->sales->sales_name }}</td>
                    <td>{{ $item->job->job_name }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ $item->dataKerja->tgl_mulai }}</td>
                    <td>{{ $item->dataKerja->tgl_selesai }}</td>
                    <td>{{ $item->designQueue->designer->name }}</td>
                    @php 
                        if($item->dataKerja->operator_id == null){
                            return '<td">OPERATOR KOSONG</td>';
                        }else{
                            //explode string operator
                            $operator = explode(',', $item->dataKerja->operator_id);
                            $namaOperator = [];
                            foreach($operator as $o){
                                if($o == 'r'){
                                    $namaOperator[] = "Rekanan";
                                }else{
                                    $namaOperator[] = \App\Models\Employee::where('id', $o)->first()->name;
                                }
                            }
                            $kumpulanOperator = implode(', ', $namaOperator);
                        }
                        if($item->dataKerja->finishing_id == null){
                            return '<td">FINISHING KOSONG</td>';
                        }else{
                            //explode string operator
                            $finishing = explode(',', $item->dataKerja->finishing_id);
                            $namaFinishing = [];
                            foreach($finishing as $f){
                                if($f == 'r'){
                                    $namaFinishing[] = "Rekanan";
                                }else{
                                    $namaFinishing[] = \App\Models\Employee::where('id', $f)->first()->name;
                                }
                            }
                            $kumpulanFinishing = implode(', ', $namaFinishing);
                        }
                        if($item->dataKerja->qc_id == null){
                            return '<td">QC KOSONG</td>';
                        }else{
                            //explode string operator
                            $qc = explode(',', $item->dataKerja->qc_id);
                            $namaQc = [];
                            foreach($qc as $q){
                                if($q == 'r'){
                                    $namaQc[] = "Rekanan";
                                }else{
                                    $namaQc[] = \App\Models\Employee::where('id', $q)->first()->name;
                                }
                            }
                            $kumpulanQc = implode(', ', $namaQc);
                        }
                    @endphp
                    <td>{{ $kumpulanOperator }}</td>
                    <td>{{ $kumpulanFinishing }}</td>
                    <td>{{ $kumpulanQc }}</td>
                    <td>Rp{{ $item->price * $item->qty }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8"></td>
                    <td colspan="3">Total Omset</td>
                    <td>Rp 0</td>
                </tr>
            </tfoot>
        </table>

        <table>
            <thead>
                <tr>
                    <th colspan="12">Non Stempel</th>
                </tr>
            </thead>
            <tbody>
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
                    <td>{{ $item->ticket_order }}</td>
                    <td>{{ $item->user->sales->sales_name }}</td>
                    <td>{{ $item->job->job_name }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ $item->dataKerja->tgl_mulai }}</td>
                    <td>{{ $item->dataKerja->tgl_selesai }}</td>
                    <td>{{ $item->desainer->name }}</td>
                    @php 
                        if($item->dataKerja->operator_id == null){
                            return '<td">OPERATOR KOSONG</td>';
                        }else{
                            //explode string operator
                            $operator = explode(',', $item->dataKerja->operator_id);
                            $namaOperator = [];
                            foreach($operator as $o){
                                if($o == 'r'){
                                    $namaOperator[] = "Rekanan";
                                }else{
                                    $namaOperator[] = \App\Models\Employee::where('id', $o)->first()->name;
                                }
                            }
                            $kumpulanOperator = implode(', ', $namaOperator);
                        }
                        if($item->dataKerja->finishing_id == null){
                            return '<td">FINISHING KOSONG</td>';
                        }else{
                            //explode string operator
                            $finishing = explode(',', $item->dataKerja->finishing_id);
                            $namaFinishing = [];
                            foreach($finishing as $f){
                                if($f == 'r'){
                                    $namaFinishing[] = "Rekanan";
                                }else{
                                    $namaFinishing[] = \App\Models\Employee::where('id', $f)->first()->name;
                                }
                            }
                            $kumpulanFinishing = implode(', ', $namaFinishing);
                        }
                        if($item->dataKerja->qc_id == null){
                            return '<td">QC KOSONG</td>';
                        }else{
                            //explode string operator
                            $qc = explode(',', $item->dataKerja->qc_id);
                            $namaQc = [];
                            foreach($qc as $q){
                                if($q == 'r'){
                                    $namaQc[] = "Rekanan";
                                }else{
                                    $namaQc[] = \App\Models\Employee::where('id', $q)->first()->name;
                                }
                            }
                            $kumpulanQc = implode(', ', $namaQc);
                        }
                    @endphp
                    <td>{{ $kumpulanOperator }}</td>
                    <td>{{ $kumpulanFinishing }}</td>
                    <td>{{ $kumpulanQc }}</td>
                    <td>Rp{{ $item->price * $item->qty }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8"></td>
                    <td colspan="3">Total Omset</td>
                    <td>Rp 0</td>
                </tr>
            </tfoot>
        </table>

        <table>
            <thead>
                <tr>
                    <th colspan="12">Advertising</th>
                </tr>
            </thead>
            <tbody>
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
                    <td>{{ $item->ticket_order }}</td>
                    <td>{{ $item->user->sales->sales_name }}</td>
                    <td>{{ $item->job->job_name }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ $item->dataKerja->tgl_mulai }}</td>
                    <td>{{ $item->dataKerja->tgl_selesai }}</td>
                    <td>{{ $item->desainer->name }}</td>
                    @php 
                        if($item->dataKerja->operator_id == null){
                            return '<td">OPERATOR KOSONG</td>';
                        }else{
                            //explode string operator
                            $operator = explode(',', $item->dataKerja->operator_id);
                            $namaOperator = [];
                            foreach($operator as $o){
                                if($o == 'r'){
                                    $namaOperator[] = "Rekanan";
                                }else{
                                    $namaOperator[] = \App\Models\Employee::where('id', $o)->first()->name;
                                }
                            }
                            $kumpulanOperator = implode(', ', $namaOperator);
                        }
                        if($item->dataKerja->finishing_id == null){
                            return '<td">FINISHING KOSONG</td>';
                        }else{
                            //explode string operator
                            $finishing = explode(',', $item->dataKerja->finishing_id);
                            $namaFinishing = [];
                            foreach($finishing as $f){
                                if($f == 'r'){
                                    $namaFinishing[] = "Rekanan";
                                }else{
                                    $namaFinishing[] = \App\Models\Employee::where('id', $f)->first()->name;
                                }
                            }
                            $kumpulanFinishing = implode(', ', $namaFinishing);
                        }
                        if($item->dataKerja->qc_id == null){
                            return '<td">QC KOSONG</td>';
                        }else{
                            //explode string operator
                            $qc = explode(',', $item->dataKerja->qc_id);
                            $namaQc = [];
                            foreach($qc as $q){
                                if($q == 'r'){
                                    $namaQc[] = "Rekanan";
                                }else{
                                    $namaQc[] = \App\Models\Employee::where('id', $q)->first()->name;
                                }
                            }
                            $kumpulanQc = implode(', ', $namaQc);
                        }
                    @endphp
                    <td>{{ $kumpulanOperator }}</td>
                    <td>{{ $kumpulanFinishing }}</td>
                    <td>{{ $kumpulanQc }}</td>
                    <td>Rp{{ $item->price * $item->qty }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8"></td>
                    <td colspan="3">Total Omset</td>
                    <td>Rp 0</td>
                </tr>
            </tfoot>
        </table>

        <table>
            <thead>
                <tr>
                    <th colspan="12">Digital Printing</th>
                </tr>
            </thead>
            <tbody>
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
                    <td>{{ $item->ticket_order }}</td>
                    <td>{{ $item->user->sales->sales_name }}</td>
                    <td>{{ $item->job->job_name }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ $item->dataKerja->tgl_mulai }}</td>
                    <td>{{ $item->dataKerja->tgl_selesai }}</td>
                    <td>{{ $item->desainer->name }}</td>
                    @php 
                        if($item->dataKerja->operator_id == null){
                            return '<td">OPERATOR KOSONG</td>';
                        }else{
                            //explode string operator
                            $operator = explode(',', $item->dataKerja->operator_id);
                            $namaOperator = [];
                            foreach($operator as $o){
                                if($o == 'r'){
                                    $namaOperator[] = "Rekanan";
                                }else{
                                    $namaOperator[] = \App\Models\Employee::where('id', $o)->first()->name;
                                }
                            }
                            $kumpulanOperator = implode(', ', $namaOperator);
                        }
                        if($item->dataKerja->finishing_id == null){
                            return '<td">FINISHING KOSONG</td>';
                        }else{
                            //explode string operator
                            $finishing = explode(',', $item->dataKerja->finishing_id);
                            $namaFinishing = [];
                            foreach($finishing as $f){
                                if($f == 'r'){
                                    $namaFinishing[] = "Rekanan";
                                }else{
                                    $namaFinishing[] = \App\Models\Employee::where('id', $f)->first()->name;
                                }
                            }
                            $kumpulanFinishing = implode(', ', $namaFinishing);
                        }
                        if($item->dataKerja->qc_id == null){
                            return '<td">QC KOSONG</td>';
                        }else{
                            //explode string operator
                            $qc = explode(',', $item->dataKerja->qc_id);
                            $namaQc = [];
                            foreach($qc as $q){
                                if($q == 'r'){
                                    $namaQc[] = "Rekanan";
                                }else{
                                    $namaQc[] = \App\Models\Employee::where('id', $q)->first()->name;
                                }
                            }
                            $kumpulanQc = implode(', ', $namaQc);
                        }
                    @endphp
                    <td>{{ $kumpulanOperator }}</td>
                    <td>{{ $kumpulanFinishing }}</td>
                    <td>{{ $kumpulanQc }}</td>
                    <td>Rp{{ $item->price * $item->qty }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8"></td>
                    <td colspan="3">Total Omset</td>
                    <td>Rp 0</td>
                </tr>
            </tfoot>
        </table>

        <table>
            <thead>
                <tr>
                    <th colspan="12">Jasa Servis</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($servis as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->created_at }}</td>
                    <td>{{ $item->ticket_order }}</td>
                    <td>{{ $item->user->sales->sales_name }}</td>
                    <td>{{ $item->job->job_name }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ $item->dataKerja->tgl_mulai }}</td>
                    <td>{{ $item->dataKerja->tgl_selesai }}</td>
                    <td>{{ $item->desainer->name }}</td>
                    @php 
                        if($item->dataKerja->operator_id == null){
                            return '<td">OPERATOR KOSONG</td>';
                        }else{
                            //explode string operator
                            $operator = explode(',', $item->dataKerja->operator_id);
                            $namaOperator = [];
                            foreach($operator as $o){
                                if($o == 'r'){
                                    $namaOperator[] = "Rekanan";
                                }else{
                                    $namaOperator[] = \App\Models\Employee::where('id', $o)->first()->name;
                                }
                            }
                            $kumpulanOperator = implode(', ', $namaOperator);
                        }
                        if($item->dataKerja->finishing_id == null){
                            return '<td">FINISHING KOSONG</td>';
                        }else{
                            //explode string operator
                            $finishing = explode(',', $item->dataKerja->finishing_id);
                            $namaFinishing = [];
                            foreach($finishing as $f){
                                if($f == 'r'){
                                    $namaFinishing[] = "Rekanan";
                                }else{
                                    $namaFinishing[] = \App\Models\Employee::where('id', $f)->first()->name;
                                }
                            }
                            $kumpulanFinishing = implode(', ', $namaFinishing);
                        }
                        if($item->dataKerja->qc_id == null){
                            return '<td">QC KOSONG</td>';
                        }else{
                            //explode string operator
                            $qc = explode(',', $item->dataKerja->qc_id);
                            $namaQc = [];
                            foreach($qc as $q){
                                if($q == 'r'){
                                    $namaQc[] = "Rekanan";
                                }else{
                                    $namaQc[] = \App\Models\Employee::where('id', $q)->first()->name;
                                }
                            }
                            $kumpulanQc = implode(', ', $namaQc);
                        }
                    @endphp
                    <td>{{ $kumpulanOperator }}</td>
                    <td>{{ $kumpulanFinishing }}</td>
                    <td>{{ $kumpulanQc }}</td>
                    <td>Rp{{ $item->price * $item->qty }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8"></td>
                    <td colspan="3">Total Omset</td>
                    <td>Rp 0</td>
                </tr>
            </tfoot>
        </table>
    </body>
</html>