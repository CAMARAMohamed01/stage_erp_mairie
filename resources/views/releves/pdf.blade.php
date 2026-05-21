<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Relevés Compteur - {{ $compteur->point_comptage }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #1e293b;
            padding-bottom: 10px;
        }

        .info-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background-color: #1e293b;
            color: #fff;
            padding: 10px;
            text-align: left;
        }

        td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        .text-right {
            text-align: right;
        }

        .text-red {
            color: #dc2626;
            font-weight: bold;
        }

        .text-green {
            color: #16a34a;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>Rapport de Consommation</h1>
        <p>Mairie - Suivi du Patrimoine Énergétique</p>
    </div>

    <div class="info-box">
        <h3>Informations du Compteur</h3>
        <p><strong>Point de comptage (PDL) :</strong> {{ $compteur->point_comptage }}</p>
        <p><strong>Réseau :</strong> {{ $compteur->type_reseau }}</p>
        <p><strong>Bâtiment / Local :</strong> {{ $compteur->local->batiment->nom_bat ?? 'Inconnu' }} /
            {{ $compteur->local->nom_local ?? 'Inconnu' }}
        </p>
        <p><strong>Date d'édition du rapport :</strong> {{ date('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date Relevé</th>
                <th class="text-right">Index Saisi ({{ $compteur->unite_mesure }})</th>
                <th class="text-right">Consommation Périodique</th>
                <th>Observations</th>
            </tr>
        </thead>
        <tbody>
            @foreach($releves as $releve)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($releve->date_releve)->format('d/m/Y') }}</td>
                    <td class="text-right">{{ number_format($releve->valeur_index, 2, ',', ' ') }}</td>

                    <td class="text-right">
                        @if($releve->consommation !== null)
                            <span class="{{ $releve->consommation > 500 ? 'text-red' : 'text-green' }}">
                                {{ number_format($releve->consommation, 2, ',', ' ') }}
                            </span>
                        @else
                            <span style="color: #94a3b8;">Initial</span>
                        @endif
                    </td>

                    <td>{{ $releve->commentaire_releve }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>