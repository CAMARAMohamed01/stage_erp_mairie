@extends('layouts.app')
@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection
@section('header_title', 'Fiche Tronçon - ' . $troncon->numero_troncon)

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        <div
            class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-l-4 border-l-slate-800">
            <div>
                <div class="flex flex-wrap items-center gap-3">
                    <span class="text-3xl drop-shadow-sm">✂️</span>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
                        Tronçon {{ $troncon->numero_troncon }}
                    </h1>
                    @if($troncon->nom_portion)
                        <span
                            class="px-3 py-1 text-xs font-bold rounded-full bg-slate-100 text-slate-700 border border-slate-200 uppercase tracking-wide">
                            {{ $troncon->nom_portion }}
                        </span>
                    @endif
                </div>
                <p class="text-sm text-slate-500 mt-2 flex items-center gap-2">
                    <span class="bg-slate-50 border border-slate-100 px-2 py-0.5 rounded">
                        Voie parente : <a href="{{ route('voies.show', $troncon->id_voie ?? 0) }}"
                            class="font-bold text-blue-600 hover:underline">{{ $troncon->nom_voie ?? 'Non définie' }}</a>
                    </span>
                </p>
            </div>

            <div class="flex flex-wrap gap-2 w-full md:w-auto">
                <button onclick="history.back()"
                    class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-bold rounded-lg hover:bg-slate-50 transition w-full md:w-auto">
                    ← Retour
                </button>
                @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                    <a href="{{ route('troncons.edit', $troncon->id_troncon) }}"
                        class="px-4 py-2 bg-amber-500 text-white text-sm font-bold rounded-lg hover:bg-amber-600 transition shadow-sm flex-1 md:flex-none text-center">
                        ✏️ Modifier
                    </a>
                @endcan
                @can('check-permission', ['Patrimoine & Equipements', 'suppression'])
                    <form action="{{ route('troncons.destroy', $troncon->id_troncon) }}" method="POST"
                        onsubmit="return confirm('Attention, la suppression de ce tronçon est définitive. Confirmer ?');"
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
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">📏 Topographie &
                            Caractéristiques</h3>
                    </div>

                    <div class="p-6 border-b border-slate-100 bg-blue-50/20">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-white p-4 rounded-lg border border-blue-100 shadow-sm">
                                <p class="text-[10px] text-blue-600 font-bold uppercase tracking-wider mb-1">Point
                                    Kilométrique Début</p>
                                <p class="text-2xl font-bold text-slate-900 mb-1">{{ $troncon->pk_debut ?? '?' }}</p>
                                <p class="text-xs text-slate-500 font-medium">📍 Repère : <span
                                        class="text-slate-700">{{ $troncon->repere_physique_debut ?? 'Aucun repère physique' }}</span>
                                </p>
                            </div>
                            <div class="bg-white p-4 rounded-lg border border-blue-100 shadow-sm">
                                <p class="text-[10px] text-blue-600 font-bold uppercase tracking-wider mb-1">Point
                                    Kilométrique Fin</p>
                                <p class="text-2xl font-bold text-slate-900 mb-1">{{ $troncon->pk_fin ?? '?' }}</p>
                                <p class="text-xs text-slate-500 font-medium">📍 Repère : <span
                                        class="text-slate-700">{{ $troncon->repere_physique_fin ?? 'Aucun repère physique' }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-6 text-sm">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Revêtement</p>
                            <p class="font-bold text-slate-900">{{ $troncon->type_revetement ?? 'Non défini' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Goudronnage</p>
                            <p class="font-bold text-slate-900">
                                {{ $troncon->date_dernier_goudronnage ? \Carbon\Carbon::parse($troncon->date_dernier_goudronnage)->format('d/m/Y') : '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Paysage / Env.</p>
                            <p class="font-bold text-slate-900">{{ $troncon->paysage_environnement ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">État Physique</p>
                            <span
                                class="px-2.5 py-1 rounded text-xs font-bold {{ str_contains(strtolower($troncon->etat_physique), 'mauvais') ? 'bg-red-50 text-red-700 border border-red-100' : (str_contains(strtolower($troncon->etat_physique), 'bon') ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-slate-100 text-slate-700 border border-slate-200') }}">
                                {{ $troncon->etat_physique ?? 'Non évalué' }}
                            </span>
                        </div>
                        <div class="col-span-2 md:col-span-4 mt-2">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Gabarit d'accessibilité
                            </p>
                            <p class="font-semibold text-slate-800 bg-slate-50 p-2 rounded border border-slate-100">
                                {{ $troncon->gabarit_accessibilite ?? 'Aucune restriction saisie' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">🛠️ Historique des
                            interventions ({{ $interventions->count() ?? 0 }})</h3>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse($interventions as $int)
                            <div class="p-4 flex justify-between items-center hover:bg-slate-50 transition">
                                <div>
                                    <p class="text-sm font-bold text-slate-900">{{ $int->type_intervention }}</p>
                                    <p class="text-xs text-slate-500">
                                        {{ \Carbon\Carbon::parse($int->date_ouverture)->format('d/m/Y') }}
                                    </p>
                                </div>
                                <span
                                    class="px-2.5 py-1 text-[10px] font-bold rounded-full border {{ $int->statut_global == 'Terminé' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                    {{ $int->statut_global }}
                                </span>
                                <a href="{{ route('interventions.show', $int->id_int) }}"
                                    class="text-xs text-blue-600 font-bold hover:underline bg-blue-50 px-2 py-1 rounded">Voir</a>
                            </div>
                        @empty
                            <p class="p-6 text-center text-sm text-slate-400 italic">Aucune intervention technique enregistrée
                                sur ce tronçon.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="space-y-6">

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-5 flex items-center gap-2">
                        <span>🔗</span> Liaisons & Dépendances
                    </h3>

                    <div class="space-y-4 text-sm">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Secteur / Zone</p>
                            <p class="font-bold text-indigo-700">{{ $troncon->nom_zone ?? 'Aucune zone' }}</p>
                        </div>

                        <div class="border-t border-slate-100 pt-3">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Ouvrage Lié
                                (Principal)</p>
                            <p class="font-semibold text-slate-800">{{ $troncon->nom_ouvrage_lie ?? '-' }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3 border-t border-slate-100 pt-3">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Ouvrage Début
                                </p>
                                <p class="font-medium text-slate-700 text-xs">{{ $troncon->nom_ouvrage_debut ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Ouvrage Fin</p>
                                <p class="font-medium text-slate-700 text-xs">{{ $troncon->nom_ouvrage_fin ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mt-6">
                    <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-2">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <span>🪑</span> Équipements sur ce tronçon
                        </h3>

                        @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                            <a href="{{ route('equipements.create', ['id_troncon' => $troncon->id_troncon]) }}"
                                class="text-xs font-bold text-blue-600 hover:underline bg-blue-50 px-3 py-1.5 rounded-lg transition hover:bg-blue-100">
                                + Ajouter un équipement
                            </a>
                        @endcan
                    </div>

                    <ul class="space-y-3">
                        @forelse($equipements as $eq)
                            <li
                                class="flex items-center justify-between p-3 bg-slate-50 border border-slate-100 rounded-lg hover:border-blue-200 transition">
                                <div>
                                    <p class="text-sm font-bold text-slate-900">{{ $eq->nom_equipement }}</p>
                                    <p class="text-xs text-slate-500">{{ $eq->type_equipement ?? 'Type non défini' }}</p>
                                </div>
                                <a href="{{ route('equipements.show', $eq->id_equipement) }}"
                                    class="text-xs font-bold text-slate-600 hover:text-blue-600 bg-white border border-slate-200 px-2 py-1 rounded shadow-sm">
                                    Voir
                                </a>
                            </li>
                        @empty
                            <li class="text-sm text-slate-400 italic py-2 text-center">Aucun équipement recensé sur ce tronçon.
                            </li>
                        @endforelse
                    </ul>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden p-1">
                    <div class="p-3 bg-slate-50 border-b border-slate-100 flex justify-between items-center rounded-t-lg">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">🗺️ Tracé Géographique</h3>
                    </div>
                    <div id="map" class="h-64 w-full rounded-b-lg z-0"></div>
                </div>

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 uppercase mb-4 border-b border-slate-100 pb-2">📂
                            Documents & Cartographies</h3>

                        <ul class="space-y-3 mb-6">
                            @forelse($documents as $doc)
                                <li
                                    class="flex items-center justify-between p-3 bg-slate-50 border border-slate-100 rounded-lg transition hover:bg-slate-100">
                                    <div class="flex items-center gap-3">
                                        <span class="text-2xl drop-shadow-sm">
                                            {{ in_array(strtolower($doc->type_doc), ['pdf']) ? '📄' : (in_array(strtolower($doc->type_doc), ['jpg', 'png', 'jpeg']) ? '🖼️' : '📁') }}
                                        </span>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800 line-clamp-1">{{ $doc->nom_fichier }}
                                            </p>
                                            <p class="text-xs text-slate-500">
                                                {{ \Carbon\Carbon::parse($doc->date_upload)->format('d/m/Y') }} •
                                                {{ number_format($doc->taille_ko, 0, ',', ' ') }} Ko
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ asset('storage/' . $doc->chemin_stockage) }}" target="_blank"
                                            class="text-blue-600 hover:text-blue-800 text-xs font-bold bg-blue-50 px-2 py-1 rounded border border-blue-100">
                                            Voir
                                        </a>
                                        @can('check-permission', ['Patrimoine & Equipements', 'suppression'])
                                            <form action="{{ route('documents.global.destroy', $doc->id_document) }}" method="POST"
                                                class="inline" onsubmit="return confirm('Supprimer ce document du tronçon ?');">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-800 text-xs font-bold bg-red-50 px-2 py-1 rounded border border-red-100">🗑️</button>
                                            </form>
                                        @endcan
                                    </div>
                                </li>
                            @empty
                                <li class="text-sm text-slate-400 italic text-center py-4">Aucun document technique associé à ce
                                    tronçon.</li>
                            @endforelse
                        </ul>
                    </div>

                    @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                        <form action="{{ route('troncons.documents.store', $troncon->id_troncon) }}" method="POST"
                            enctype="multipart/form-data"
                            class="bg-slate-50 p-4 rounded-lg border border-slate-200 border-dashed mt-auto">
                            @csrf
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Ajouter un document</label>
                            <p class="text-[10px] text-slate-500 mb-3">Formats acceptés : PDF, JPG, PNG, DOC, DOCX. (Max : 5 Mo)
                            </p>

                            <div class="flex items-start gap-2">
                                <div class="w-full">
                                    <input type="file" name="fichier" required
                                        class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:font-semibold file:bg-slate-900 file:text-white hover:file:bg-slate-800 cursor-pointer focus:outline-none">
                                    @error('fichier')
                                        <p class="text-xs text-red-600 font-bold mt-2">⚠️ {{ $message }}</p>
                                    @enderror
                                </div>
                                <button type="submit"
                                    class="px-3 py-1.5 bg-indigo-600 text-white font-bold rounded-md hover:bg-indigo-700 transition text-xs whitespace-nowrap shadow-sm">
                                    📤 Envoyer
                                </button>
                            </div>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Initialisation de la carte (Centrée par défaut vers Annecy / Dingy-Saint-Clair)
            var map = L.map('map').setView([45.928, 6.223], 13);

            // Ajout du fond de carte OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap',
                maxZoom: 19
            }).addTo(map);

            // Récupération de la donnée GeoJSON envoyée par le contrôleur
            var geojsonStr = `{!! $troncon->geojson ?? 'null' !!}`;

            if (geojsonStr !== 'null') {
                try {
                    var geoData = JSON.parse(geojsonStr);

                    // Dessiner le tronçon (Ligne bleue)
                    var layer = L.geoJSON(geoData, {
                        style: {
                            color: "#2563eb", // blue-600
                            weight: 5,
                            opacity: 0.8
                        }
                    }).addTo(map);

                    // Zoomer automatiquement pour que le tronçon prenne tout le cadre
                    map.fitBounds(layer.getBounds(), {
                        padding: [20, 20]
                    });

                } catch (e) {
                    console.error("Erreur de lecture du GeoJSON", e);
                }
            }
        });
    </script>
@endsection