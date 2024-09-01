<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: sans-serif; 
        }

        h1 {
            text-align: center; 
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid black; 
        }

        th, td {
            border: 0.5px solid black; 
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        /* Tambahkan aturan @page untuk mengatur orientasi landscape */
        @page {
            size: landscape; /* atau bisa juga menggunakan 'A4 landscape' */
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
                <td>{{ \Carbon\Carbon::parse($suratPeringatan->created_at)->format('d-m-Y') }}</td> 
                <td>{{ \Carbon\Carbon::parse($suratPeringatan->diserahkan)->format('d-m-Y') }}</td> 
                <td>{{ \Carbon\Carbon::parse($suratPeringatan->kembali)->format('d-m-Yp') }}</td> 
                <td>{{ $suratPeringatan->accountOfficer->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>