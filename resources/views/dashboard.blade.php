@extends("layouts.master")
@section("main-content")
<div class="container home">
    @if (session("success"))
    <div class="alert alert-success">
        {{ session("success") }}
    </div>
    @endif

    <button class="btn btn-success mb-3" data-toggle="modal" data-target="#addModal">Tambah Data</button>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Total</th>
                <th>Keterangan</th>
                <th>Progres SP</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($nasabahs as $index => $nasabah)
                <tr>
                    <td>{{ $nasabah->no }}</td>
                    <td>{{ $nasabah->nama }}</td>
                    <td>{{ $nasabah->total }}</td>
                    <td>{{ $nasabah->keterangan }}</td>
                    <td>
                        @php
                            $progresSp = $suratPeringatans->firstWhere('nasabah_no', $nasabah->no);
                        @endphp
                        {{ $progresSp ? $progresSp->tingkat : 'N/A' }}
                    </td>
                    <td>
                        <button class="btn btn-primary btn-sm edit-btn" data-id="{{ $nasabah->no }}">Edit</button>
                        <button class="btn btn-info btn-sm detail-btn" data-id="{{ $nasabah->no }}">Detail</button>
                        <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $nasabah->no }}">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal for Add -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Tambah Data Nasabah</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="addNo">No</label>
                        <input type="text" class="form-control" id="addNo" required>
                    </div>
                    <div class="form-group">
                        <label for="addNama">Nama</label>
                        <input type="text" class="form-control" id="addNama" required>
                    </div>
                    <div class="form-group">
                        <label for="addPokok">Pokok</label>
                        <input type="number" class="form-control" id="addPokok" required>
                    </div>
                    <div class="form-group">
                        <label for="addBunga">Bunga</label>
                        <input type="number" class="form-control" id="addBunga" required>
                    </div>
                    <div class="form-group">
                        <label for="addDenda">Denda</label>
                        <input type="number" class="form-control" id="addDenda" required>
                    </div>
                    <div class="form-group">
                        <label for="addTotal">Total</label>
                        <input type="number" class="form-control" id="addTotal" readonly>
                    </div>
                    <div class="form-group">
                        <label for="addKeterangan">Keterangan</label>
                        <textarea class="form-control" id="addKeterangan" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="addTtd">TTD</label>
                        <input type="datetime-local" class="form-control" id="addTtd" required>
                    </div>
                    <div class="form-group">
                        <label for="addKembali">Kembali</label>
                        <input type="datetime-local" class="form-control" id="addKembali" required>
                    </div>
                    <div class="form-group">
                        <label for="addCabang">Cabang</label>
                        <select class="form-control" id="addCabang" required>
                            @foreach($cabangs as $cabang)
                                <option value="{{ $cabang->id_cabang }}">{{ $cabang->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="addWilayah">Wilayah</label>
                        <select class="form-control" id="addWilayah" required>
                            @foreach($wilayahs as $wilayah)
                                <option value="{{ $wilayah->id_wilayah }}">{{ $wilayah->nama_wilayah }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="addAccountOfficer">Account Officer</label>
                        <select class="form-control" id="addAccountOfficer" required>
                            @foreach($accountOfficers as $accountOfficer)
                                <option value="{{ $accountOfficer->id }}">{{ $accountOfficer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#addPokok, #addBunga, #addDenda').on('input', function() {
        var pokok = parseFloat($('#addPokok').val()) || 0;
        var bunga = parseFloat($('#addBunga').val()) || 0;
        var denda = parseFloat($('#addDenda').val()) || 0;
        $('#addTotal').val(pokok + bunga + denda);
    });

    $('#addForm').on('submit', function(e) {
        e.preventDefault();
        var formData = {
            no: $('#addNo').val(),
            nama: $('#addNama').val(),
            pokok: $('#addPokok').val(),
            bunga: $('#addBunga').val(),
            denda: $('#addDenda').val(),
            total: $('#addTotal').val(),
            keterangan: $('#addKeterangan').val(),
            ttd: $('#addTtd').val(),
            kembali: $('#addKembali').val(),
            id_cabang: $('#addCabang').val(),
            id_wilayah: $('#addWilayah').val(),
            id_account_officer: $('#addAccountOfficer').val(),
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: '{{ route('nasabah.store') }}',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#addModal').modal('hide');
                    location.reload(); // Refresh the page to show the new data
                }
            },
            error: function(response) {
                // Handle error response
                console.log(response.responseJSON.errors);
            }
        });
    });
});
</script>
@endsection
