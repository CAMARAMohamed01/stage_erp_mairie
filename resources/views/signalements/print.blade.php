<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Signalement #{{ $signalement->id_sig }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body class="p-10 bg-white" onload="window.print()">
    <div class="no-print mb-6 p-4 bg-blue-50 text-blue-800 rounded border border-blue-200">
        📌 <strong>Mode Impression :</strong> Le PDF s'ouvre automatiquement.
    </div>

    <div class="flex justify-between items-start border-b-2 border-slate-900 pb-6 mb-8">
        <div>
            <h1 class="text-3xl font-black text-slate-900">RÉCÉPISSÉ DE SIGNALEMENT</h1>
            <p class="text-slate-600 uppercase tracking-widest text-sm">Administration Communale</p>
        </div>
        <div class="text-right">
            <p class="font-bold text-xl">#{{ $signalement->id_sig }}</p>
            <p class="text-sm text-slate-500">Date :
                {{ \Carbon\Carbon::parse($signalement->date_creation)->format('d/m/Y H:i') }}
            </p>
        </div>
    </div>

    <div class="space-y-8">
        <section>
            <h2 class="bg-slate-100 p-2 font-bold uppercase text-sm mb-4">Détails du demandeur</h2>
            <div class="grid grid-cols-2 gap-4">
                <p><span class="text-slate-500">Nom :</span> {{ $signalement->emetteur_nom }}</p>
                <p><span class="text-slate-500">Contact :</span> {{ $signalement->emetteur_contact ?? 'Non renseigné' }}
                </p>
                <p><span class="text-slate-500">Mode de réception :</span> {{ $signalement->mode_reception }}</p>
            </div>
        </section>

        <section>
            <h2 class="bg-slate-100 p-2 font-bold uppercase text-sm mb-4">Description de l'incident</h2>
            <div class="border p-4 rounded-lg bg-slate-50">
                <p class="text-lg italic">"{{ $signalement->description }}"</p>
            </div>
        </section>

        <section class="grid grid-cols-2 gap-8">
            <div>
                <h2 class="bg-slate-100 p-2 font-bold uppercase text-sm mb-4">Classification</h2>
                <p><span class="text-slate-500">Catégorie :</span>
                    {{ $signalement->categorie->libelle ?? 'Non défini' }}</p>
                <p><span class="text-slate-500">Priorité :</span> {{ $signalement->priorite }}</p>
            </div>
            <div>
                <h2 class="bg-slate-100 p-2 font-bold uppercase text-sm mb-4">État de traitement</h2>
                <p><span class="text-slate-500">Statut actuel :</span>
                    <strong>{{ $signalement->statut_signalement }}</strong>
                </p>
            </div>
        </section>
    </div>

    <div class="mt-32 pt-10 border-t border-dashed border-slate-300 flex justify-between italic text-sm text-slate-400">
        <p>Généré le {{ date('d/m/Y H:i') }} par l'ERP Mairie</p>
        <p>Cachet de la Mairie</p>
    </div>
</body>

</html>