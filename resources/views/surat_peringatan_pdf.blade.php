<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: sans-serif; /* Menggunakan font yang umum */
        }

        h1 {
            text-align: center; /* Judul di tengah */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid black; /* Border tabel lebih tebal */
        }

        th, td {
            border: 0.5px solid black; /* Border sel tabel lebih tebal */
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>

    <table>
        <thead>
            <tr> 
                <th>No</th>
                <th>Nama</th>
                <th>Tingkat</th>
                <th>Dibuat</th>
                <th>Diserahkan</th>
                <th>Kembali</th> 
            </tr>
        </thead>
        <tbody>
            @foreach ($suratPeringatans as $suratPeringatan)
                <tr>
                    <td>{{ $suratPeringatan->no }}</td>
                    <td>{{ $suratPeringatan->nasabah->nama }}</td>
                    <td>{{ $suratPeringatan->tingkat }}</td>
                    <td>{{ $suratPeringatan->created_at }}</td>
                    <td>{{ $suratPeringatan->diserahkan }}</td>
                    <td>{{ $suratPeringatan->kembali }}</td> 
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>