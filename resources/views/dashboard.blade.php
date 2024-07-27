@extends("layouts.master")
@section("main-content")
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
                        <button class="btn btn-primary btn-sm edit-btn" data-no="{{ $nasabah->no }}" data-toggle="modal" data-target="#editModal">Edit</button>
                        <button class="btn btn-info btn-sm detail-btn" data-no="{{ $nasabah->no }}" data-toggle="modal" data-target="#detailModal">Detail</button>
                        <button class="btn btn-danger btn-sm delete-btn" data-no="{{ $nasabah->no }}" data-toggle="modal" data-target="#deleteModal">Delete</button>
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
            <form id="addForm" method="POST" action="{{ route('nasabah.store') }}">
            @csrf
                <div class="modal-body">
                <div class="modal-body">
                    <input type="hidden" id="addIdAdminKas" name="id_admin_kas" value="{{ $currentUser->pegawaiAdminKas->id_admin_kas?? '' }}">

                    <div class="form-group">
                        <label for="addNo">No</label>
                        <input type="text" class="form-control" id="addNo" name="no" required>
                    </div>
                    <div class="form-group">
                        <label for="addNama">Nama</label>
                        <input type="text" class="form-control" id="addNama" name="nama" required>
                    </div>
                    <div class="form-group">
                        <label for="addPokok">Pokok</label>
                        <input type="number" class="form-control" id="addPokok" name="pokok" required>
                    </div>
                    <div class="form-group">
                        <label for="addBunga">Bunga</label>
                        <input type="number" class="form-control" id="addBunga" name="bunga" required>
                    </div>
                    <div class="form-group">
                        <label for="addDenda">Denda</label>
                        <input type="number" class="form-control" id="addDenda" name="denda" required>
                    </div>
                    <div class="form-group">
                        <label for="addTotal">Total</label>
                        <input type="number" class="form-control" id="addTotal" name="total" readonly>
                    </div>
                    <div class="form-group">
                        <label for="addKeterangan">Keterangan</label>
                        <textarea class="form-control" id="addKeterangan" name="keterangan" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="addTtd">TTD</label>
                        <input type="datetime-local" class="form-control" id="addTtd" name="ttd" required>
                    </div>
                    <div class="form-group">
                        <label for="addKembali">Kembali</label>
                        <input type="datetime-local" class="form-control" id="addKembali" name="kembali" required>
                    </div>
                    <div class="form-group">
                        <label for="addCabang">Cabang</label>
                        <select class="form-control" id="addCabang" name="id_cabang" required>
                            @foreach($cabangs as $cabang)
                                <option value="{{ $cabang->id_cabang }}">{{ $cabang->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="addWilayah">Wilayah</label>
                        <select class="form-control" id="addWilayah" name="id_wilayah" required>
                            @foreach($wilayahs as $wilayah)
                                <option value="{{ $wilayah->id_wilayah }}">{{ $wilayah->nama_wilayah }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="addAccountOfficer">Account Officer</label>
                        <select class="form-control" id="addAccountOfficer" name="id_account_officer" required>
                        @foreach($nasabahs as $nasabah)
                            @if($nasabah->accountofficer)
                                <option value="{{ $nasabah->accountofficer->id_account_officer }}">{{ $nasabah->accountofficer->nama_account_officer }}</option>
                            @endif
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

<!-- Modal for Edit -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Data Nasabah</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editNo">No</label>
                        <input type="text" class="form-control" id="editNo" name="no" readonly>
                    </div>
                    <div class="form-group">
                        <label for="editNama">Nama</label>
                        <input type="text" class="form-control" id="editNama" name="nama" required>
                    </div>
                    <div class="form-group">
                        <label for="editPokok">Pokok</label>
                        <input type="number" class="form-control" id="editPokok" name="pokok" required>
                    </div>
                    <div class="form-group">
                        <label for="editBunga">Bunga</label>
                        <input type="number" class="form-control" id="editBunga" name="bunga" required>
                    </div>
                    <div class="form-group">
                        <label for="editDenda">Denda</label>
                        <input type="number" class="form-control" id="editDenda" name="denda" required>
                    </div>
                    <div class="form-group">
                        <label for="editTotal">Total</label>
                        <input type="number" class="form-control" id="editTotal" name="total" readonly>
                    </div>
                    <div class="form-group">
                        <label for="editKeterangan">Keterangan</label>
                        <textarea class="form-control" id="editKeterangan" name="keterangan" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editTtd">TTD</label>
                        <input type="datetime-local" class="form-control" id="editTtd" name="ttd" required>
                    </div>
                    <div class="form-group">
                        <label for="editKembali">Kembali</label>
                        <input type="datetime-local" class="form-control" id="editKembali" name="kembali" required>
                    </div>
                    <div class="form-group">
                        <label for="editCabang">Cabang</label>
                        <select class="form-control" id="editCabang" name="id_cabang" required>
                            @foreach($cabangs as $cabang)
                                <option value="{{ $cabang->id_cabang }}">{{ $cabang->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editWilayah">Wilayah</label>
                        <select class="form-control" id="editWilayah" name="id_wilayah" required>
                            @foreach($wilayahs as $wilayah)
                                <option value="{{ $wilayah->id_wilayah }}">{{ $wilayah->nama_wilayah }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editAccountOfficer">Account Officer</label>
                        <select class="form-control" id="editAccountOfficer" name="id_account_officer" required>
                        @foreach($nasabahs as $nasabah)
                            @if($nasabah->accountofficer)
                                <option value="{{ $nasabah->accountofficer->id_account_officer }}">{{ $nasabah->accountofficer->nama_account_officer }}</option>
                            @endif
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

<!-- Modal for Detail -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Data Nasabah</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="detailNo">No</label>
                    <input type="text" class="form-control" id="detailNo" name="no" readonly>
                </div>
                <div class="form-group">
                    <label for="detailNama">Nama</label>
                    <input type="text" class="form-control" id="detailNama" name="nama" readonly>
                </div>
                <div class="form-group">
                    <label for="detailPokok">Pokok</label>
                    <input type="number" class="form-control" id="detailPokok" name="pokok" readonly>
                </div>
                <div class="form-group">
                    <label for="detailBunga">Bunga</label>
                    <input type="number" class="form-control" id="detailBunga" name="bunga" readonly>
                </div>
                <div class="form-group">
                    <label for="detailDenda">Denda</label>
                    <input type="number" class="form-control" id="detailDenda" name="denda" readonly>
                </div>
                <div class="form-group">
                    <label for="detailTotal">Total</label>
                    <input type="number" class="form-control" id="detailTotal" name="total" readonly>
                </div>
                <div class="form-group">
                    <label for="detailKeterangan">Keterangan</label>
                    <textarea class="form-control" id="detailKeterangan" name="keterangan" readonly></textarea>
                </div>
                <div class="form-group">
                    <label for="detailTtd">TTD</label>
                    <input type="datetime-local" class="form-control" id="detailTtd" name="ttd" readonly>
                </div>
                <div class="form-group">
                    <label for="detailKembali">Kembali</label>
                    <input type="datetime-local" class="form-control" id="detailKembali" name="kembali" readonly>
                </div>
                <div class="form-group">
                    <label for="detailCabang">Cabang</label>
                    <input type="text" class="form-control" id="detailCabang" name="cabang" readonly>
                </div>
                <div class="form-group">
                    <label for="detailWilayah">Wilayah</label>
                    <input type="text" class="form-control" id="detailWilayah" name="wilayah" readonly>
                </div>
                <div class="form-group">
                    <label for="detailAccountOfficer">Account Officer</label>
                    <input type="text" class="form-control" id="detailAccountOfficer" name="account_officer" readonly>
                </div>
                <div class="form-group">
                    <label for="detailAdminKas">Admin Kas</label>
                    <p id="detailAdminKas"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Hapus Data Nasabah</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="deleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus data ini?</p>
                    <input type="hidden" id="deleteNo" name="no">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        function calculateTotal() {
        var pokok = parseFloat($('#addPokok').val()) || 0;
        var bunga = parseFloat($('#addBunga').val()) || 0;
        var denda = parseFloat($('#addDenda').val()) || 0;
        var total = pokok + bunga + denda;
        $('#addTotal').val(total);
    }

    // Calculate total on input change for add form
    $('#addPokok, #addBunga, #addDenda').on('input', calculateTotal);

    // Calculate total on input change for edit form
    $('#editPokok, #editBunga, #editDenda').on('input', function() {
        var pokok = parseFloat($('#editPokok').val()) || 0;
        var bunga = parseFloat($('#editBunga').val()) || 0;
        var denda = parseFloat($('#editDenda').val()) || 0;
        var total = pokok + bunga + denda;
        $('#editTotal').val(total);
    });
        $('.edit-btn').on('click', function() {
            var no = $(this).data('no');
            var nasabah = @json($nasabahs->keyBy('no'));
            var data = nasabah[no];

            $('#editNo').val(data.no);
            $('#editNama').val(data.nama);
            $('#editPokok').val(data.pokok);
            $('#editBunga').val(data.bunga);
            $('#editDenda').val(data.denda);
            $('#editTotal').val(data.total);
            $('#editKeterangan').val(data.keterangan);
            $('#editTtd').val(data.ttd);
            $('#editKembali').val(data.kembali);
            $('#editCabang').val(data.id_cabang);
            $('#editWilayah').val(data.id_wilayah);
            $('#editAccountOfficer').val(data.account_officer.nama_account_officer);

            $('#editForm').attr('action', '/nasabah/update/' + no);
        });

        // Detail button click event
        $('.detail-btn').on('click', function() {
            var no = $(this).data('no');
            var nasabah = @json($nasabahs->keyBy('no'));
            var data = nasabah[no];

            $('#detailNo').val(data.no);
            $('#detailNama').val(data.nama);
            $('#detailPokok').val(data.pokok);
            $('#detailBunga').val(data.bunga);
            $('#detailDenda').val(data.denda);
            $('#detailTotal').val(data.total);
            $('#detailKeterangan').val(data.keterangan);
            $('#detailTtd').val(data.ttd);
            $('#detailKembali').val(data.kembali);
            $('#detailCabang').val(data.id_cabang);
            $('#detailWilayah').val(data.id_wilayah);
            $('#detailAccountOfficer').val(data.account_officer.nama_account_officer);
        });

        // Delete button click event
        $('.delete-btn').on('click', function() {
            var no = $(this).data('no');
            $('#deleteNo').val(no);
            $('#deleteForm').attr('action', '/nasabah/delete/' + no);
        });
    });
</script>
@endsection
