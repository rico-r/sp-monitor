@extends("layouts.master")
@section("main-content")
<div class="container home">
    @if (session("success"))
    <div class="alert alert-success">
        {{session("success") }}
    </div>
    @endif

    <div class="flex justify-between mb-4">
        <div>
            <form method="GET" action="{{ route('super-admin.dashboard') }}">
                <select name="date_filter" onchange="this.form.submit()"
                    class="bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-4 py-2 rounded">
                    <option value="">Last 30 days</option>
                    <option value="last_7_days" {{ request('date_filter')=='last_7_days' ? 'selected' : '' }}>Last 7
                        days</option>
                    <option value="last_30_days" {{ request('date_filter')=='last_30_days' ? 'selected' : '' }}>Last 30
                        days</option>
                    <option value="last_month" {{ request('date_filter')=='last_month' ? 'selected' : '' }}>Last month
                    </option>
                    <option value="last_year" {{ request('date_filter')=='last_year' ? 'selected' : '' }}>Last year
                    </option>
                </select>
            </form>
        </div>
        <div>
            <form method="GET" action="{{ route('super-admin.dashboard') }}">
                <input type="text" id="search" name="search" value="{{ request('search') }}"
                    placeholder="Search by name, branch, region"
                    class="bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-4 py-2 rounded">

                <select name="cabang_filter" onchange="this.form.submit()"
                    class="bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-4 py-2 rounded">
                    <option value="">Cabang</option>
                    @foreach($cabangs as $cabang)
                    <option value="{{ $cabang->id_cabang }}" {{ request('cabang_filter')==$cabang->id_cabang ?
                        'selected' : '' }}>{{ $cabang->nama_cabang }}</option>
                    @endforeach
                </select>

                <select name="wilayah_filter" onchange="this.form.submit()"
                    class="bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-4 py-2 rounded">
                    <option value="">Wilayah</option>
                    @foreach($wilayahs as $wilayah)
                    <option value="{{ $wilayah->id_wilayah }}" {{ request('wilayah_filter')==$wilayah->id_wilayah ?
                        'selected' : '' }}>{{ $wilayah->nama_wilayah }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <table class="table table-striped" id="nasabah-table">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Status</th>
                <th>Jabatan</th>
                <th>Cabang</th>
                <th>Wilayah</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($allUsers as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email}}</td>
                <td>{{ $user->infostatus ? $user->infostatus->nama_status : 'N/A' }}</td>
                <td>{{ $user->jabatan->nama_jabatan }}</td>
                <td>{{ $user->cabang ? $user->cabang->nama_cabang : 'N/A' }}</td>
                <td>{{ $user->wilayah ? $user->wilayah->nama_wilayah : 'N/A' }}</td>
                <td>
                    <button class="btn btn-primary btn-sm edit-btn" data-id="{{ $user->id }}" data-toggle="modal"
                        data-target="#editModal">Edit</button>
                    <button class="btn btn-info btn-sm detail-btn" data-id="{{ $user->id }}" data-toggle="modal"
                        data-target="#detailModal">Detail</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
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
            </form>
            <div class="modal-body">
                <div class="form-group">
                    <label for="editNama">Nama</label>
                    <input type="text" class="form-control" id="editNama" name="name" readonly required>
                </div>
                <div class="form-group">
                    <label for="editStatus">Status</label>
                    <select class="form-control" id="editStatus" name="status" required>
                        <option value="" disabled selected>Pilih Status</option>
                        <option value="1">Aktif</option>
                        <option value="2">Tidak Aktif</option>
                    </select>
                </div>
                <!-- <div class="form-group">
                    <label for="editJabatan">Jabatan</label>
                    <select class="form-control" id="editJabatan" name="jabatan" required>
                        <option value="" disabled selected>Pilih Jabatan</option>
                        @foreach ($statuses as $status)
                        <option value="{{ $status->id }}">{{ $status->nama_status }}</option>
                        @endforeach
                    </select>
                </div> -->
                <div class="form-group">
                    <label for="editJabatan">Jabatan</label>
                    <select class="form-control" id="editJabatan" name="jabatan_id" required>
                        <option value="" disabled selected>Pilih Jabatan</option>
                        @foreach ($jabatans as $jabatan)
                        <option value="{{ $jabatan->id_jabatan }}">{{ $jabatan->nama_jabatan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="editCabang">Cabang</label>
                    <select class="form-control" id="editCabang" name="id_cabang" required>
                        <option value="" disabled selected>Pilih Cabang</option>
                        @foreach($cabangs as $cabang)
                        <option value="{{ $cabang->id_cabang }}">{{ $cabang->nama_cabang }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="editWilayah">Wilayah</label>
                    <select class="form-control" id="editWilayah" name="id_wilayah" required>
                        <option value="" disabled selected>Pilih Wilayah</option>
                        @foreach($wilayahs as $wilayah)
                        <option value="{{ $wilayah->id_wilayah }}">{{ $wilayah->nama_wilayah }}</option>
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
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel"
    aria-hidden="true">
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
                    <input type="text" class="form-control" id="detailCabang" name="id_cabang" readonly>
                </div>
                <div class="form-group">
                    <label for="detailWilayah">Wilayah</label>
                    <input type="text" class="form-control" id="detailWilayah" name="id_wilayah" readonly>
                </div>
                <div class="form-group">
                    <label for="detailAccountOfficer">Account Officer</label>
                    <input type="text" class="form-control" id="detailAccountOfficer" name="id_account_officer"
                        readonly>
                </div>
                <div class="form-group">
                    <label for="detailAdminKas">Admin Kas</label>
                    <input type="text" class="form-control" id="detailAdminKas" readonly>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus Data Nasabah</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="deleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus data user ini?</p>
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
    document.getElementById('search').addEventListener('keyup', function (event) {
        const query = event.target.value;
        const table = document.getElementById('nasabah-table');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        for (let i = 0; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName('td');
            let match = false;

            for (let j = 0; j < cells.length; j++) {
                if (cells[j].innerText.toLowerCase().includes(query.toLowerCase())) {
                    match = true;
                    break;
                }
            }

            if (match) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    });
    // Edit button click event
    $('.edit-btn').on('click', function () {
        var id = $(this).data('id'); // Menggunakan 'id' sebagai pengenal pengguna

        $.ajax({
            url: '/super-admin/user/edit/' + id, // Ubah rute ke /super-admin/user/edit/{id}
            method: 'GET',
            success: function (data) {
                // Populate the modal with data
                $('#editNama').val(data.name);
                $('#editStatus').val(data.status); // Mengisi dropdown status dengan ID status yang sesuai
                $('#editJabatan').val(data.jabatan_id); // Mengisi dropdown jabatan dengan ID jabatan yang sesuai
                $('#editCabang').val(data.id_cabang); // Mengisi dropdown cabang dengan ID cabang yang sesuai
                $('#editWilayah').val(data.id_wilayah); // Mengisi dropdown wilayah dengan ID wilayah yang sesuai

                // Set the form action to the update route with the correct id
                $('#editForm').attr('action', '/user/update/' + id); // Ubah rute ke /super-admin/user/update/{id}
                $('#editForm').find('input[name="_method"]').val('PUT');

                // Menampilkan modal
                $('#editModal').modal('show');
            },
            error: function (xhr, status, error) {
                console.error('Error #editForm:', error); // Log error ke konsol browser
                alert('Terjadi kesalahan saat memuat data.');
            }
        });
    });
   

</script>

@endsection