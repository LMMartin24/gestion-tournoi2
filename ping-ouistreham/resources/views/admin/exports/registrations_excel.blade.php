<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style>
        .num { mso-number-format:General; }
        .text { mso-number-format:"\@"; }
        th { background-color: #f3f4f6; font-weight: bold; border: 0.5pt solid #ccc; }
        td { border: 0.5pt solid #ccc; }
    </style>
</head>
<body>

    <table>
        <thead>
            <tr><th colspan="7" style="font-size: 16pt;">LISTE GLOBALE DES INSCRIPTIONS - {{ $tournament->name }}</th></tr>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Licence</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Tableau</th>
                <th>Prix</th>
            </tr>
        </thead>
        <tbody>
            @php $totalRecette = 0; @endphp
            @foreach($allRegistrations as $reg)
            <tr>
                <td>{{ strtoupper($reg->player_lastname) }}</td>
                <td>{{ $reg->player_firstname }}</td>
                <td class="text">{{ $reg->player_license }}</td>
                <td>{{ $reg->user->email ?? 'Non renseigné' }}</td>
                <td class="text">{{ $reg->user->phone ?? 'Non renseigné' }}</td>
                <td>{{ $reg->subTable->label }}</td>
                <td class="num">{{ $reg->subTable->price }} €</td>
            </tr>
            @php $totalRecette += $reg->subTable->price; @endphp
            @endforeach
            <tr>
                <td colspan="6" style="text-align: right; font-weight: bold;">TOTAL RECETTE :</td>
                <td style="font-weight: bold; background-color: #e5e7eb;">{{ $totalRecette }} €</td>
            </tr>
        </tbody>
    </table>

    <br><br>

    @foreach($registrationsByTable as $tableName => $regs)
    <table style="margin-top: 50px;">
        <thead>
            <tr><th colspan="5" style="font-size: 14pt; background-color: #dcfce7;">TABLEAU : {{ $tableName }}</th></tr>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Licence</th>
                <th>Email</th>
                <th>Téléphone</th>
            </tr>
        </thead>
        <tbody>
            @foreach($regs as $reg)
            <tr>
                <td>{{ strtoupper($reg->player_lastname) }}</td>
                <td>{{ $reg->player_firstname }}</td>
                <td class="text">{{ $reg->player_license }}</td>
                <td>{{ $reg->user->email ?? 'Non renseigné' }}</td>
                <td class="text">{{ $reg->user->phone ?? 'Non renseigné' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endforeach

</body>
</html>