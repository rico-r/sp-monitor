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
                <th>Rekening</th>
                <th>Nama</th>
                <th>SP</th>
                <th>Dibuat</th>
                <th>Diserahkan</th>
                <th>Kembali</th> 
                <th>Account Officer</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($suratPeringatans as $suratPeringatan)
                <tr>
                <td>{{ $suratPeringatan->no }}</td>
                <td>{{ $suratPeringatan->nasabah->nama }}</td>
                <td>{{ $suratPeringatan->tingkat }}</td>
                <td>{{ \Carbon\Carbon::parse($suratPeringatan->created_at)->format('Y-m-d') }}</td> 
                <td>{{ \Carbon\Carbon::parse($suratPeringatan->diserahkan)->format('Y-m-d') }}</td> 
                <td>{{ \Carbon\Carbon::parse($suratPeringatan->kembali)->format('Y-m-d') }}</td> 
                <td>{{ $suratPeringatan->accountOfficer->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>