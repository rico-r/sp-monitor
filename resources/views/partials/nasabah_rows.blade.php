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
