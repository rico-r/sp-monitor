@extends("layouts.master")
@section("main-content")
<div class="container home">
    @if (session("success"))
    <div class="alert alert-success">
        {{session("success") }}
    </div>
    @endif

    <button class="btn btn-success mb-3" data-toggle="modal" data-target="#addModal">Tambah Kantorkas</button>

    <table class="table table-striped" id="nasabah-table">
        <thead>
        <tr>
            <th>ID Kantorkas</th>
            <th>Nama Kantorkas</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        @foreach($kantorkas as $wilayah)
            <tr>
                <td>{{ $wilayah->id_kantorkas }}</td>
                <td>{{ $wilayah->nama_kantorkas}}</td>
                <td>
                <button class="btn btn-danger btn-sm delete-btn" data-id_kantorkas="{{ $wilayah->id_kantorkas }}" data-toggle="modal" data-target="#deleteModal">Delete</button></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Tambah Data Kantorkas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addForm" method="POST" action="{{ route('super-admin.kantorkas.add') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="addKantorkas">Kantor kas</label>
                        <input type="text" class="form-control" id="addKantorkas" name="nama_kantorkas">
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
                    <p>Apakah Anda yakin ingin menghapus data nasabah ini?</p>
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

    // Delete button click event
    $('.delete-btn').on('click', function() {
    var id_kantorkas = $(this).data('id_kantorkas'); 
    $('#deleteForm').attr('action', '/super-admin/kantorkas/delete/' + id_kantorkas); 
});

</script>

@endsection