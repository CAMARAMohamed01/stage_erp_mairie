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
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body class="p-8 max-w-4xl mx-auto bg-white text-slate-800 text-sm" onload="window.print()">

    <div class="no-print mb-8 p-4 bg-blue-50 text-blue-800 rounded border border-blue-200 flex items-center shadow-sm">
        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
            </path>
        </svg>
        <span>Ceci est l'aperçu avant impression. <strong>Appuyez sur "Imprimer" ou "Enregistrer en PDF"</strong> dans
            la boîte de dialogue de votre navigateur.</span>
    </div>

    <div class="border-b-2 border-slate-800 pb-4 mb-6 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-bold uppercase tracking-widest">Bon de Travaux <span
                    class="text-slate-400 font-normal">#{{ $intervention->id_int }}</span></h1>
            <p class="text-slate-500 mt-1 font-medium">Service Technique - Mairie de Dingy-Saint-Clair</p>
        </div>
        <div class="text-right">
            <p class="font-bold text-lg">{{ strtoupper($intervention->statut_global) }}</p>
            <p class="text-slate-500 text-xs mt-1">Édité le {{ date('d/m/Y à H:i') }}</p>
        </div>
    </div>

    {{-- GRILLE COMPOSÉE : INFORMATIONS, BUDGET & LOCALISATION --}}
    <div class="grid grid-cols-2 gap-6 mb-6">
        <div class="border border-slate-200 p-4 rounded bg-slate-50">
            <h2 class="font-bold uppercase text-xs text-slate-500 mb-3 border-b border-slate-200 pb-1">Informations du
                dossier</h2>
            <table class="w-full text-sm">
                <tr>
                    <td class="py-1 text-slate-500 w-1/3">Type :</td>
                    <td class="py-1 font-semibold">{{ $intervention->type_intervention }}</td>
                </tr>
                <tr>
                    <td class="py-1 text-slate-500">Ouverture :</td>
                    <td class="py-1 font-semibold">
                        {{ \Carbon\Carbon::parse($intervention->date_ouverture)->format('d/m/Y') }}
                    </td>
                </tr>
                <tr>
                    <td class="py-1 text-slate-500">Clôture :</td>
                    <td class="py-1 font-semibold text-green-700">
                        {{ $intervention->date_cloture ? \Carbon\Carbon::parse($intervention->date_cloture)->format('d/m/Y') : 'Non clôturé' }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="border border-slate-200 p-4 rounded bg-slate-50">
            <h2 class="font-bold uppercase text-xs text-slate-500 mb-3 border-b border-slate-200 pb-1">Classification &
                Budget</h2>
            <table class="w-full text-sm">
                <tr>
                    <td class="py-1 text-slate-500 w-1/3">Catégorie :</td>
                    <td class="py-1 font-semibold">{{ $intervention->categorie->libelle ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="py-1 text-slate-500">Budget / Code :</td>
                    <td class="py-1 font-semibold">
                        {{ $intervention->code_budget ? strtoupper($intervention->code_budget) : 'N/A' }}
                    </td>
                </tr>
                <tr>
                    <td class="py-1 text-slate-500">Origine :</td>
                    <td class="py-1 font-semibold">
                        {{ $intervention->id_action ? 'action #' . $intervention->id_action : 'Interne' }}
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- NOUVEAU BLOC : LOCALISATION DU CHANTIER SUR LE TERRAIN --}}
    <div class="mb-6 border border-slate-200 p-4 rounded bg-slate-50/30">
        <h2 class="font-bold uppercase text-xs text-slate-700 mb-3 border-b border-slate-300 pb-1">📍 Localisation du
            Chantier</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">Emplacement Immeuble /
                    Pièce :</span>
                @if($intervention->local)
                    <p class="font-semibold text-slate-800 mt-0.5">🏢 Local : {{ $intervention->local->nom_local }}</p>
                    <p class="text-xs text-slate-500 font-medium">Niveau :
                        {{ $intervention->local->niveau ?? 'Non spécifié' }}
                    </p>
                @else
                    <p class="text-slate-400 italic mt-0.5">Aucun local intérieur spécifique</p>
                @endif
            </div>

            <div>
                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">Bâtiment ou Espace Public
                    :</span>
                @if($intervention->batiment)
                    <p class="font-semibold text-slate-800 mt-0.5">🏛️ Bâtiment : {{ $intervention->batiment->nom_bat }}</p>
                @elseif($intervention->local && $intervention->local->batiment)
                    <p class="font-semibold text-slate-800 mt-0.5">🏛️ Bâtiment :
                        {{ $intervention->local->batiment->nom_bat }} <span
                            class="text-xs font-normal text-slate-400 italic">(via local)</span>
                    </p>
                @elseif($intervention->lieuxPublicis && $intervention->lieuxPublicis->count() > 0)
                    @foreach($intervention->lieuxPublicis as $lieu)
                        <p class="font-semibold text-slate-800 mt-0.5">🌳 Espace : {{ $lieu->nom_lieu }} <span
                                class="text-xs font-normal text-slate-400 italic">({{ $lieu->typologie_lieu }})</span></p>
                    @endforeach
                @else
                    <p class="text-slate-400 italic mt-0.5">Non spécifié (Intervention globale)</p>
                @endif
            </div>
        </div>
    </div>

    {{-- RECAPITULATIF FINANCIER POUR L'IMPRESSION --}}
    <div class="mb-6 p-4 border-2 border-slate-300 rounded bg-slate-50/50 grid grid-cols-3 gap-4 text-center">
        <div>
            <span class="text-xs uppercase font-bold text-slate-400">Total Matériels</span>
            <p class="text-base font-bold text-slate-800 mt-0.5">
                {{ number_format($intervention->achatsMateriels->sum(fn($m) => $m->quantite * $m->prix_unitaire_ht), 2, ',', ' ') }}
                €
            </p>
        </div>
        <div>
            <span class="text-xs uppercase font-bold text-slate-400">Prestations / Suivis</span>
            <p class="text-base font-bold text-slate-800 mt-0.5">
                {{ number_format($intervention->suiviActions->sum('cout_associe'), 2, ',', ' ') }} €
            </p>
        </div>
        <div class="bg-slate-200/60 rounded p-1">
            <span class="text-xs uppercase font-extrabold text-slate-600">Coût Consolidé Total</span>
            <p class="text-base font-black text-slate-900 mt-0.5">
                @php
                    $totMat = $intervention->achatsMateriels->sum(fn($m) => $m->quantite * $m->prix_unitaire_ht);
                    $totSui = $intervention->suiviActions->sum('cout_associe');
                @endphp
                {{ number_format($totMat + $totSui, 2, ',', ' ') }} € HT
            </p>
        </div>
    </div>

    @if($intervention->equipements && $intervention->equipements->count() > 0)
        <div class="mb-6">
            <h2 class="font-bold uppercase text-xs text-slate-800 mb-2 border-b border-slate-800 pb-1">Équipement(s)
                concerné(s)</h2>
            <ul class="list-disc list-inside text-sm mt-2">
                @foreach($intervention->equipements as $equip)
                    <li class="py-1 font-semibold">{{ $equip->nom_equipement }} <span class="font-normal text-slate-500">(Réf:
                            {{ $equip->reference_serie ?? 'Non renseignée' }})</span></li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-6">
        <h2 class="font-bold uppercase text-xs text-slate-800 mb-2 border-b border-slate-800 pb-1">Demande initiale /
            Descriptif</h2>
        <div class="border border-slate-200 p-4 rounded text-sm bg-white">{{ $intervention->description }}</div>
    </div>

    <div class="mb-12">
        <h2 class="font-bold uppercase text-xs text-slate-800 mb-2 border-b border-slate-800 pb-1">Historique &
            Compte-rendus de terrain</h2>
        @if($intervention->suiviActions && $intervention->suiviActions->count() > 0)
            <table class="w-full text-sm border-collapse border border-slate-200 mt-2">
                <thead>
                    <tr class="bg-slate-100 text-left">
                        <th class="border border-slate-200 p-2 w-24">Date</th>
                        <th class="border border-slate-200 p-2 w-20 text-center">Temps</th>
                        <th class="border border-slate-200 p-2 w-24 text-right">Coût</th>
                        <th class="border border-slate-200 p-2">Observations / Travaux réalisés</th>
                        <th class="border border-slate-200 p-2 w-32 text-center">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($intervention->suiviActions as $action)
                        <tr>
                            <td class="border border-slate-200 p-2">
                                {{ \Carbon\Carbon::parse($action->date_action_suivi)->format('d/m/Y') }}
                            </td>
                            <td class="border border-slate-200 p-2 text-center font-bold">{{ $action->temps_passe_heures }}h
                            </td>
                            <td class="border border-slate-200 p-2 text-right font-medium text-slate-600">
                                {{ $action->cout_associe > 0 ? number_format($action->cout_associe, 2, ',', ' ') . ' €' : '0,00 €' }}
                            </td>
                            <td class="border border-slate-200 p-2">{!! nl2br(e($action->description_etape)) !!}</td>
                            <td class="border border-slate-200 p-2 text-center text-xs font-semibold">
                                {{ $action->statut_apres_action }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-slate-500 italic text-sm mt-2">Aucun compte-rendu d'intervention n'a été enregistré à ce jour.
            </p>
        @endif
    </div>

    <div class="mt-16 flex justify-between px-10">
        <div class="text-center w-56">
            <p class="mb-20 text-xs uppercase font-bold text-slate-500">Visa du Responsable</p>
            <div class="border-t border-slate-400 pt-2 text-xs text-slate-500">Date et Signature</div>
        </div>
        <div class="text-center w-56">
            <p class="mb-20 text-xs uppercase font-bold text-slate-500">Visa du Technicien</p>
            <div class="border-t border-slate-400 pt-2 text-xs text-slate-500">Date et Signature</div>
        </div>
    </div>

</body>

</html>