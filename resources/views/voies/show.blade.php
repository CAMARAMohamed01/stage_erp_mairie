@extends('layouts.app')

@section('header_title', 'Fiche Voirie - ' . $voie->nom_voie)

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        <div
            class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-wrap justify-between items-center gap-4 border-l-4 border-l-slate-800">
            <div>
                <div class="flex items-center gap-3">
                    <span class="text-3xl">🛣️</span>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
                        {{ $voie->numero_voie ? $voie->numero_voie . ' - ' : '' }}{{ $voie->nom_voie ?? 'Voie sans nom' }}
                    </h1>
                    <span
                        class="px-2.5 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                        {{ $voie->categorie_voie ?? 'Catégorie non définie' }}
                    </span>
                    @if($voie->est_pdipr)
                        <span
                            class="px-2.5 py-1 text-xs font-bold rounded-full bg-green-100 text-green-800 border border-green-200"
                            title="Plan Départemental des Itinéraires de Promenade et de Randonnée">
                            🥾 Inscrit PDIPR
                        </span>
                    @endif
                </div>
                <p class="text-sm text-slate-500 mt-2">
                    Statut Juridique : <span
                        class="font-bold text-slate-700">{{ $voie->statut_juridique ?? 'Non renseigné' }}</span>
                    @if($voie->ancien_numero) | Ancien N° : <span class="text-slate-600">{{ $voie->ancien_numero }}</span>
                    @endif
                    @if($voie->num_provisoire) | N° Provisoire : <span
                    class="text-slate-600">{{ $voie->num_provisoire }}</span> @endif
                </p>
            </div>
            <div class="flex gap-2">
                <button onclick="history.back()"
                    class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 transition">←
                    Retour</button>
                @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                    <a href="{{ route('voies.edit', $voie->id_voie) }}"
                        class="px-4 py-2 bg-amber-500 text-white text-sm font-semibold rounded-lg hover:bg-amber-600 transition">✏️
                        Modifier</a>
                @endcan
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">📏 Mensurations & Tracé</h3>
                    </div>
                    <div class="p-5 grid grid-cols-2 md:grid-cols-4 gap-6 text-sm">
                        <div>
                            <p class="text-slate-500 mb-1">Longueur Classée</p>
                            <p class="font-bold text-slate-900 text-lg">
                                {{ $voie->longueur_classee_ml ? $voie->longueur_classee_ml . ' ml' : '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-slate-500 mb-1">Longueur Réelle</p>
                            <p class="font-bold text-slate-900 text-lg">
                                {{ $voie->longueur_reelle_ml ? $voie->longueur_reelle_ml . ' ml' : '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-slate-500 mb-1">Largeur Moyenne</p>
                            <p class="font-bold text-slate-900 text-lg">
                                {{ $voie->largeur_moyenne_m ? $voie->largeur_moyenne_m . ' m' : '-' }}
                            </p>
                        </div>
                        <div
                            class="col-span-2 md:col-span-4 grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                            <div class="bg-blue-50/50 p-3 rounded border border-blue-100">
                                <p class="text-xs text-blue-600 font-bold uppercase mb-1">Point d'Origine</p>
                                <p class="text-slate-800">{{ $voie->point_origine ?? 'Non défini' }}</p>
                            </div>
                            <div class="bg-blue-50/50 p-3 rounded border border-blue-100">
                                <p class="text-xs text-blue-600 font-bold uppercase mb-1">Point d'Extrémité</p>
                                <p class="text-slate-800">{{ $voie->point_extremite ?? 'Non défini' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">✂️ Tronçons de voie
                            ({{ $troncons->count() }})</h3>
                        @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                            <a href="{{ route('troncons.create', ['id_voie' => $voie->id_voie]) }}"
                                class="text-xs bg-white border border-slate-300 text-slate-700 px-3 py-1 rounded-md hover:bg-slate-100 transition shadow-sm">+
                                Ajouter un tronçon</a>
                        @endcan
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 text-slate-500">
                                <tr>
                                    <th class="px-4 py-3 font-semibold">PK Début ➔ Fin</th>
                                    <th class="px-4 py-3 font-semibold">Désignation</th>
                                    <th class="px-4 py-3 font-semibold">Revêtement</th>
                                    <th class="px-4 py-3 font-semibold">État Physique</th>
                                    <th class="px-4 py-3 font-semibold text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($troncons as $troncon)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-4 py-3 font-mono font-bold text-slate-700">
                                            {{ $troncon->pk_debut ?? '?' }} ➔ {{ $troncon->pk_fin ?? '?' }}
                                        </td>
                                        <td class="px-4 py-3 font-semibold text-slate-800">
                                            {{ $troncon->numero_troncon }}<br>
                                            <span
                                                class="text-[10px] text-slate-500 font-normal">{{ $troncon->nom_portion ?? '' }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span
                                                class="px-2 py-1 bg-slate-100 rounded text-slate-700">{{ $troncon->type_revetement ?? 'Inconnu' }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span
                                                class="font-semibold {{ str_contains(strtolower($troncon->etat_physique), 'mauvais') ? 'text-red-600' : 'text-green-600' }}">
                                                {{ $troncon->etat_physique ?? 'Non évalué' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('troncons.show', $troncon->id_troncon) }}"
                                                class="text-blue-600 font-bold hover:underline">Voir</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-slate-400 italic">Cette voie n'est pas
                                            encore découpée en tronçons.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div
                    class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden border-l-4 border-l-orange-500">
                    <div class="p-4 bg-orange-50/50 border-b border-orange-100 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-orange-900 uppercase tracking-wider">🌉 Ouvrages d'art rattachés
                            ({{ $ouvrages->count() }})</h3>
                        @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                            <a href="{{ route('ouvrages.create', ['id_voie' => $voie->id_voie]) }}"
                                class="text-xs bg-white border border-slate-300 text-slate-700 px-3 py-1 rounded-md hover:bg-slate-100 transition shadow-sm">+
                                Ajouter un ouvrage</a>
                        @endcan
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse($ouvrages as $ouvrage)
                            <div class="p-4 flex justify-between items-center hover:bg-slate-50 transition">
                                <div>
                                    <p class="text-sm font-bold text-slate-800">{{ $ouvrage->nom_ouvrage }}</p>
                                    <p class="text-xs text-slate-500">Type: {{ $ouvrage->type_ouvrage ?? 'Non défini' }} |
                                        Franchissement: {{ $ouvrage->franchissement ?? '-' }}</p>
                                </div>
                                <a href="{{ route('ouvrages.show', $ouvrage->id_ouvrage) }}"
                                    class="text-xs font-bold text-blue-600 hover:underline">Détails →</a>
                            </div>
                        @empty
                            <p class="p-4 text-sm text-slate-400 italic text-center">Aucun ouvrage d'art (pont, mur de
                                soutènement) recensé sur cette voie.</p>
                        @endforelse
                    </div>
                </div>

            </div>

            <div class="space-y-6">

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h3
                        class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">
                        📜 Historique & Urbanisme</h3>

                    <div class="space-y-4 text-sm">
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase mb-1">Historique d'incorporation</p>
                            <div class="bg-slate-50 p-3 rounded border border-slate-100 text-slate-700 text-xs">
                                {{ $voie->historique_incorporation ?? 'Aucune donnée historique saisie.' }}
                            </div>
                        </div>

                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase mb-1">Conformité Cadastrale</p>
                            <p class="font-semibold text-slate-800">{{ $voie->conformite_cadastrale ?? 'Non vérifiée' }}</p>
                        </div>

                        @if($voie->definition_trace)
                            <div>
                                <p class="text-xs font-bold text-slate-500 uppercase mb-1">Définition du tracé</p>
                                <p class="text-slate-700 text-xs">{{ $voie->definition_trace }}</p>
                            </div>
                        @endif

                        @if($voie->observations_statut)
                            <div>
                                <p class="text-xs font-bold text-slate-500 uppercase mb-1">Observations sur le statut</p>
                                <p class="text-slate-700 text-xs italic">"{{ $voie->observations_statut }}"</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h3
                        class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">
                        📍 Zones Traversées</h3>
                    <div class="flex flex-wrap gap-2">
                        @forelse($zones as $zone)
                            <span
                                class="px-3 py-1 bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-bold rounded-lg"
                                title="{{ $zone->nom_zone }}">
                                {{ $zone->code_zone ?? $zone->nom_zone }}
                            </span>
                        @empty
                            <p class="text-xs text-slate-400 italic">Cette voie n'est rattachée à aucune zone globale.</p>
                        @endforelse
                    </div>
                </div>

                @if($voie->interet_touristique)
                    <div class="bg-emerald-50 rounded-xl border border-emerald-100 shadow-sm p-6">
                        <h3 class="text-sm font-bold text-emerald-800 uppercase tracking-wider mb-2 flex items-center gap-2">
                            📸 Intérêt Touristique
                        </h3>
                        <p class="text-xs text-emerald-900 leading-relaxed">
                            {{ $voie->interet_touristique }}
                        </p>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection