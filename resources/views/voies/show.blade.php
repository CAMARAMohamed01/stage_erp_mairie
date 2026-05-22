@extends('layouts.app')

@section('header_title', 'Fiche Voirie - ' . ($voie->nom_voie ?? 'Sans nom'))

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        <div
            class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-l-4 border-l-slate-800">
            <div>
                <div class="flex flex-wrap items-center gap-3">
                    <span class="text-3xl drop-shadow-sm">🛣️</span>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
                        {{ $voie->numero_voie ? $voie->numero_voie . ' - ' : '' }}{{ $voie->nom_voie ?? 'Voie sans nom' }}
                    </h1>
                    <span
                        class="px-3 py-1 text-xs font-bold rounded-full bg-slate-100 text-slate-700 border border-slate-200 uppercase tracking-wide">
                        {{ $voie->categorie_voie ?? 'Catégorie non définie' }}
                    </span>
                    @if($voie->est_pdipr)
                        <span
                            class="px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-800 border border-green-200 shadow-sm"
                            title="Plan Départemental des Itinéraires de Promenade et de Randonnée">
                            🥾 Inscrit PDIPR
                        </span>
                    @endif
                </div>

                <div class="text-sm text-slate-500 mt-3 flex flex-wrap gap-2 items-center">
                    <span class="bg-slate-50 border border-slate-100 px-2 py-0.5 rounded">
                        Statut : <span
                            class="font-bold text-slate-700">{{ $voie->statut_juridique ?? 'Non renseigné' }}</span>
                    </span>
                    @if($voie->ancien_numero)
                        <span class="text-slate-300">•</span>
                        <span>Ancien N° : <span class="font-medium text-slate-700">{{ $voie->ancien_numero }}</span></span>
                    @endif
                    @if($voie->num_provisoire)
                        <span class="text-slate-300">•</span>
                        <span>N° Prov. : <span class="font-medium text-slate-700">{{ $voie->num_provisoire }}</span></span>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap gap-2 w-full md:w-auto">
                <button onclick="history.back()"
                    class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-bold rounded-lg hover:bg-slate-50 transition w-full md:w-auto">
                    ← Retour
                </button>
                @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                    <a href="{{ route('voies.edit', $voie->id_voie) }}"
                        class="px-4 py-2 bg-amber-500 text-white text-sm font-bold rounded-lg hover:bg-amber-600 transition shadow-sm flex-1 md:flex-none text-center">
                        ✏️ Modifier
                    </a>
                @endcan
                @can('check-permission', ['Patrimoine & Equipements', 'suppression'])
                    <form action="{{ route('voies.destroy', $voie->id_voie) }}" method="POST"
                        onsubmit="return confirm('Attention, la suppression de cette voie est définitive. Confirmer ?');"
                        class="flex-1 md:flex-none">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full px-4 py-2 bg-red-50 text-red-600 border border-red-200 text-sm font-bold rounded-lg hover:bg-red-100 transition">
                            🗑️Supprimer
                        </button>
                    </form>
                @endcan
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">📏 Mensurations & Tracé</h3>
                    </div>
                    <div class="p-6 grid grid-cols-2 md:grid-cols-3 gap-6 text-sm">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Longueur Classée</p>
                            <p class="font-bold text-slate-900 text-xl">
                                {{ $voie->longueur_classee_ml ? number_format($voie->longueur_classee_ml, 0, ',', ' ') . ' ml' : '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Longueur Réelle</p>
                            <p class="font-bold text-slate-900 text-xl">
                                {{ $voie->longueur_reelle_ml ? number_format($voie->longueur_reelle_ml, 0, ',', ' ') . ' ml' : '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Largeur Moyenne</p>
                            <p class="font-bold text-slate-900 text-xl">
                                {{ $voie->largeur_moyenne_m ? number_format($voie->largeur_moyenne_m, 2, ',', ' ') . ' m' : '-' }}
                            </p>
                        </div>

                        <div class="col-span-2 md:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                            <div class="bg-blue-50/50 p-4 rounded-lg border border-blue-100">
                                <p class="text-[10px] text-blue-600 font-bold uppercase tracking-wider mb-1">Point d'Origine
                                </p>
                                <p class="text-slate-800 font-medium">{{ $voie->point_origine ?? 'Non défini' }}</p>
                            </div>
                            <div class="bg-blue-50/50 p-4 rounded-lg border border-blue-100">
                                <p class="text-[10px] text-blue-600 font-bold uppercase tracking-wider mb-1">Point
                                    d'Extrémité</p>
                                <p class="text-slate-800 font-medium">{{ $voie->point_extremite ?? 'Non défini' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div
                        class="p-4 bg-slate-50 border-b border-slate-200 flex flex-wrap justify-between items-center gap-2">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">✂️ Tronçons de voie
                            ({{ $troncons->count() ?? 0 }})</h3>
                        @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                            <a href="{{ route('troncons.create', ['id_voie' => $voie->id_voie]) }}"
                                class="text-xs bg-white border border-slate-300 text-slate-700 font-bold px-3 py-1.5 rounded-lg hover:bg-slate-100 transition shadow-sm">
                                + Ajouter un tronçon
                            </a>
                        @endcan
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="bg-slate-50 text-slate-500 border-b border-slate-100 text-xs">
                                <tr>
                                    <th class="px-4 py-3 font-bold uppercase">PK Début ➔ Fin</th>
                                    <th class="px-4 py-3 font-bold uppercase">Désignation</th>
                                    <th class="px-4 py-3 font-bold uppercase">Revêtement</th>
                                    <th class="px-4 py-3 font-bold uppercase">État</th>
                                    <th class="px-4 py-3 font-bold uppercase text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($troncons as $troncon)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="px-4 py-3 font-mono font-bold text-slate-700">
                                            {{ $troncon->pk_debut ?? '?' }} ➔ {{ $troncon->pk_fin ?? '?' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <p class="font-bold text-slate-800">{{ $troncon->numero_troncon }}</p>
                                            <p class="text-[10px] text-slate-500">{{ $troncon->nom_portion ?? '' }}</p>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span
                                                class="px-2.5 py-1 bg-slate-100 rounded text-slate-700 text-xs font-medium">{{ $troncon->type_revetement ?? 'Inconnu' }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span
                                                class="font-bold text-xs {{ str_contains(strtolower($troncon->etat_physique), 'mauvais') ? 'text-red-600 bg-red-50 px-2 py-1 rounded' : 'text-emerald-600 bg-emerald-50 px-2 py-1 rounded' }}">
                                                {{ $troncon->etat_physique ?? 'Non évalué' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('troncons.show', $troncon->id_troncon) }}"
                                                class="text-blue-600 font-bold hover:underline text-xs bg-blue-50 px-2 py-1 rounded">Détails</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-slate-400 italic text-sm">Aucun
                                            tronçon n'a été découpé sur cette voie.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div
                    class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden border-l-4 border-l-orange-400">
                    <div
                        class="p-4 bg-orange-50/30 border-b border-orange-100 flex flex-wrap justify-between items-center gap-2">
                        <h3 class="text-sm font-bold text-orange-900 uppercase tracking-wider">🌉 Ouvrages d'art rattachés
                            ({{ $ouvrages->count() ?? 0 }})</h3>
                        @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                            <a href="{{ route('ouvrages.create', ['id_voie' => $voie->id_voie]) }}"
                                class="text-xs bg-white border border-orange-200 text-orange-800 font-bold px-3 py-1.5 rounded-lg hover:bg-orange-50 transition shadow-sm">
                                + Ajouter un ouvrage
                            </a>
                        @endcan
                    </div>
                    <div class="divide-y divide-slate-50">
                        @forelse($ouvrages as $ouvrage)
                            <div class="p-4 flex justify-between items-center hover:bg-orange-50/20 transition">
                                <div>
                                    <p class="text-sm font-bold text-slate-800">{{ $ouvrage->nom_ouvrage }}</p>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        <span class="font-medium">Type:</span> {{ $ouvrage->type_ouvrage ?? 'Non défini' }}
                                        <span class="mx-1 text-slate-300">|</span>
                                        <span class="font-medium">Franchissement:</span> {{ $ouvrage->franchissement ?? '-' }}
                                    </p>
                                </div>
                                <a href="{{ route('ouvrages.show', $ouvrage->id_ouvrage) }}"
                                    class="text-xs font-bold text-orange-600 hover:underline bg-orange-50 px-3 py-1.5 rounded-lg">Fiche
                                    Ouvrage →</a>
                            </div>
                        @empty
                            <p class="p-6 text-sm text-slate-400 italic text-center">Aucun ouvrage d'art (pont, mur de
                                soutènement) recensé sur cet axe.</p>
                        @endforelse
                    </div>
                </div>

            </div>

            <div class="space-y-6">

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-5 flex items-center gap-2">
                        <span>📜</span> Historique & Foncier
                    </h3>

                    <div class="space-y-5 text-sm">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1.5">Historique
                                d'incorporation</p>
                            <div
                                class="bg-slate-50 p-3 rounded-lg border border-slate-100 text-slate-700 text-xs leading-relaxed">
                                {{ $voie->historique_incorporation ?? 'Aucune donnée historique saisie.' }}
                            </div>
                        </div>

                        <div class="border-t border-slate-100 pt-4">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Conformité
                                Cadastrale</p>
                            <p class="font-bold text-slate-800">{{ $voie->conformite_cadastrale ?? 'Non vérifiée' }}</p>
                        </div>

                        @if($voie->definition_trace)
                            <div class="border-t border-slate-100 pt-4">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Définition du tracé
                                </p>
                                <p class="text-slate-700 text-xs leading-relaxed">{{ $voie->definition_trace }}</p>
                            </div>
                        @endif

                        @if($voie->observations_statut)
                            <div
                                class="border-t border-slate-100 pt-4 bg-amber-50/50 -mx-6 px-6 pb-4 mb-[-1.5rem] rounded-b-xl">
                                <p class="text-[10px] font-bold text-amber-600 uppercase tracking-wide mb-1 pt-4">⚠️
                                    Observations sur le statut</p>
                                <p class="text-amber-900 text-xs italic">"{{ $voie->observations_statut }}"</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <span>📍</span> Zones Traversées
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @forelse($zones ?? [] as $zone)
                            <span
                                class="px-3 py-1.5 bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-bold rounded-lg shadow-sm"
                                title="{{ $zone->nom_zone }}">
                                {{ $zone->code_zone ?? $zone->nom_zone }}
                            </span>
                        @empty
                            <p class="text-xs text-slate-400 italic">La voie n'est rattachée à aucune zone définie.</p>
                        @endforelse
                    </div>
                </div>

                @if($voie->interet_touristique)
                    <div class="bg-emerald-50 rounded-xl border border-emerald-100 shadow-sm p-6 relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 text-4xl">📸</div>
                        <h3 class="text-sm font-bold text-emerald-800 uppercase tracking-wider mb-3 relative z-10">
                            Intérêt Touristique
                        </h3>
                        <p class="text-xs text-emerald-900 leading-relaxed relative z-10">
                            {{ $voie->interet_touristique }}
                        </p>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection