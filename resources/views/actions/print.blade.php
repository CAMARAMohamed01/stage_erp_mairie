<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Signalement Citoyen #{{ $action->id_action }}</title>
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

<body class="p-10 bg-white text-slate-800 text-sm max-w-4xl mx-auto" onload="window.print()">

    @php
        // Détermination de l'adresse "en cascade" pour l'impression
        $adresseFinale = null;
        $sourceAdresse = '';

        if ($action->adresse) {
            $adresseFinale = $action->adresse;
        } elseif ($action->batiment && $action->batiment->adresse) {
            $adresseFinale = $action->batiment->adresse;
            $sourceAdresse = ' (via Bâtiment)';
        } elseif ($action->lieu && $action->lieu->adresse) {
            $adresseFinale = $action->lieu->adresse;
            $sourceAdresse = ' (via Lieu public)';
        }
    @endphp

    <div class="flex justify-between items-start border-b-2 border-slate-900 pb-6 mb-8">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">RÉCÉPISSÉ D'ACTION CITOYEN</h1>
            <p class="text-slate-500 uppercase tracking-widest text-xs font-bold mt-1">Administration Municipale —
                Dingy-Saint-Clair</p>
        </div>
        <div class="text-right">
            <p class="font-bold text-xl text-slate-900">N° #{{ $action->id_action }}</p>
            <p class="text-xs text-slate-500 mt-1">Enregistré le :
                <strong>{{ \Carbon\Carbon::parse($action->date_creation)->format('d/m/Y à H:i') }}</strong>
            </p>
        </div>
    </div>

    <div class="space-y-8">
        {{-- SECTION 1 : DEMANDEUR --}}
        <section>
            <h2 class="bg-slate-100 p-2 font-bold uppercase text-xs tracking-wider text-slate-700 mb-4 rounded">👤
                Origine & Émetteur</h2>
            <div class="grid grid-cols-2 gap-y-3 gap-x-8 text-sm">
                <p><span class="text-slate-400 font-medium">Nom / Organisme :</span> <span
                        class="font-bold text-slate-800">{{ $action->emetteur_nom }}</span></p>
                <p><span class="text-slate-400 font-medium">Coordonnées :</span> <span
                        class="font-semibold text-slate-800">{{ $action->emetteur_contact ?? 'Non renseigné' }}</span>
                </p>
                <p><span class="text-slate-400 font-medium">Canal de réception :</span> <span
                        class="font-medium text-slate-800">{{ $action->mode_reception }}</span></p>
                <p><span class="text-slate-400 font-medium">Agent instructeur :</span> <span
                        class="font-bold bg-slate-100 px-2 py-0.5 rounded text-xs text-slate-700 uppercase tracking-wider">{{ $action->createur->initiales ?? 'AG' }}</span>
                </p>
            </div>
        </section>

        {{-- SECTION 2 LOCALISATION TECHNIQUE --}}
        <section>
            <h2 class="bg-slate-100 p-2 font-bold uppercase text-xs tracking-wider text-slate-700 mb-4 rounded">📍
                Localisation Géographique / Patrimoine</h2>
            <div class="grid grid-cols-2 gap-6">
                {{-- Adresse Voirie (Mise à jour avec la cascade) --}}
                <div class="p-3 border border-slate-200 rounded-lg">
                    <span class="block text-xs font-bold text-slate-400 uppercase tracking-wide">
                        Domaine public / Voirie <span
                            class="text-slate-500 font-normal italic lowercase">{{ $sourceAdresse }}</span>
                    </span>
                    @if($adresseFinale)
                        <p class="font-bold text-slate-800 mt-1">{{ $adresseFinale->num_rue }}
                            {{ $adresseFinale->nom_voie }}
                        </p>
                        <p class="text-xs text-slate-500 font-medium">{{ $adresseFinale->code_postal }}
                            {{ $adresseFinale->ville }}
                        </p>
                    @else
                        <p class="text-slate-400 text-xs italic mt-1.5">Aucune adresse directe ou déduite spécifiée</p>
                    @endif
                </div>

                {{-- Local Bâtiment --}}
                <div class="p-3 border border-slate-200 rounded-lg">
                    <span class="block text-xs font-bold text-slate-400 uppercase tracking-wide">Bâtiment / Localisation
                        intérieure</span>
                    @if($action->id_local && $action->local)
                        <p class="font-bold text-slate-800 mt-1">{{ $action->local->nom_local }}</p>
                        <p class="text-xs text-slate-500 font-medium">Étage/Niveau : {{ $action->local->niveau ?? 'RDC' }}
                        </p>
                    @else
                        <p class="text-slate-400 text-xs italic mt-1.5">Aucun local intérieur rattaché</p>
                    @endif
                </div>
            </div>
        </section>

        {{-- SECTION 3 : DESCRIPTION --}}
        <section>
            <h2 class="bg-slate-100 p-2 font-bold uppercase text-xs tracking-wider text-slate-700 mb-4 rounded">📝
                Description des désordres ou doléances</h2>
            <div class="border border-slate-200 p-5 rounded-xl bg-slate-50/50">
                <p class="text-base italic leading-relaxed text-slate-700 font-medium">"{!!
    nl2br(e($action->description)) !!}"</p>
            </div>
        </section>

        {{-- SECTION 4 : INDEXATION ET AVANCEMENT --}}
        <section class="grid grid-cols-2 gap-8">
            <div>
                <h2 class="bg-slate-100 p-2 font-bold uppercase text-xs tracking-wider text-slate-700 mb-4 rounded">⚙️
                    Indexation Technique</h2>
                <div class="space-y-2">
                    <p><span class="text-slate-400 font-medium">Corps d'état assigné :</span> <span
                            class="font-bold text-slate-800">{{ $action->categorie->libelle ?? 'Non défini' }}</span>
                    </p>
                    <p><span class="text-slate-400 font-medium">Degré d'urgence :</span>
                        <span
                            class="font-extrabold {{ $action->priorite === 'Haute' ? 'text-red-600' : 'text-slate-700' }}">{{ $action->priorite }}</span>
                    </p>
                </div>
            </div>
            <div>
                <h2 class="bg-slate-100 p-2 font-bold uppercase text-xs tracking-wider text-slate-700 mb-4 rounded">📈
                    État de prise en charge</h2>
                <div class="space-y-2">
                    <p><span class="text-slate-400 font-medium">Statut d'instruction :</span> <span
                            class="font-black text-slate-900 border border-slate-400 rounded px-2 py-0.5 bg-slate-50">{{ strtoupper($action->statut_action) }}</span>
                    </p>
                    <p><span class="text-slate-400 font-medium">Attribution administrative :</span> <span
                            class="font-medium text-slate-800">Brigade des Services Techniques</span></p>
                </div>
            </div>
        </section>
    </div>

    {{-- ZONE DE SIGNATURE OFFICIELLE --}}
    <div
        class="mt-36 pt-8 border-t border-dashed border-slate-300 flex justify-between items-start text-xs text-slate-400 font-medium">
        <div>
            <p>Document officiel d'ordonnancement de travaux</p>
            <p class="mt-1">Généré via l'application ERP Mairie le {{ date('d/m/Y à H:i') }}</p>
        </div>
        <div class="text-center pr-12">
            <p class="uppercase font-bold tracking-wider text-slate-500 mb-16">Cachet de l'autorité</p>
            <p class="text-[10px] border-t border-slate-200 pt-1">Dingy-Saint-Clair, Haute-Savoie</p>
        </div>
    </div>
</body>

</html>