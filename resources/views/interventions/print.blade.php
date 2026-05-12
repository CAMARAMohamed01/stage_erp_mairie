<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Bon d'intervention #{{ $intervention->id_int }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 0;
            }
        }
    </style>
</head>

<body class="p-10 bg-white" onload="window.print()">
    <div class="no-print mb-10 p-4 bg-yellow-100 text-yellow-800 rounded">
        Ceci est l'aperçu avant impression. <strong>Appuyez sur "Enregistrer en PDF"</strong> dans votre navigateur.
    </div>

    <div class="border-b-4 border-blue-600 pb-4 mb-10 flex justify-between items-center">
        <div>
            <h1 class="text-4xl font-bold">BON D'INTERVENTION #{{ $intervention->id_int }}</h1>
            <p class="text-slate-500">Service Technique - Mairie de votre Commune</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-10 mb-10">
        <div class="border p-4 rounded">
            <h2 class="font-bold border-b mb-2">Détails</h2>
            <p><strong>Type :</strong> {{ $intervention->type_intervention }}</p>
            <p><strong>Date :</strong> {{ $intervention->date_ouverture }}</p>
            <p><strong>Statut :</strong> {{ $intervention->statut_global }}</p>
        </div>
        <div class="border p-4 rounded">
            <h2 class="font-bold border-b mb-2">Description</h2>
            <p>{{ $intervention->description }}</p>
        </div>
    </div>

    <div class="mt-20 flex justify-between">
        <div class="border-t-2 border-slate-300 w-48 pt-2 text-center text-sm">Visa Responsable</div>
        <div class="border-t-2 border-slate-300 w-48 pt-2 text-center text-sm">Visa Agent</div>
    </div>
</body>

</html>