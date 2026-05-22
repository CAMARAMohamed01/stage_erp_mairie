@extends('layouts.app')
@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection
@section('header_title', 'Ouvrage - ' . $ouvrage->nom_ouvrage)

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        <div
            class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-l-4 border-l-orange-500">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <span class="text-3xl drop-shadow-sm">🌉</span>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $ouvrage->nom_ouvrage }}</h1>
                    <span
                        class="px-3 py-1 bg-slate-100 text-slate-700 border border-slate-200 rounded-full font-bold text-xs uppercase tracking-wide">
                        {{ $ouvrage->type_ouvrage ?? 'Type non défini' }}
                    </span>
                </div>
                <p class="text-sm text-slate-500">Rattaché à la voie :
                    @if($ouvrage->id_voie)
                        <a href="{{ route('voies.show', $ouvrage->id_voie) }}"
                            class="font-bold text-blue-600 hover:underline">{{ $ouvrage->nom_voie }}</a>
                    @else
                        <span class="italic">Indépendant / Non affecté</span>
                    @endif
                </p>
            </div>

            <div class="flex flex-wrap gap-2 w-full md:w-auto">
                <button onclick="history.back()"
                    class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-bold rounded-lg hover:bg-slate-50 transition">
                    ← Retour
                </button>
                @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                    <a href="{{ route('ouvrages.edit', $ouvrage->id_ouvrage) }}"
                        class="px-4 py-2 bg-amber-500 text-white text-sm font-bold rounded-lg hover:bg-amber-600 transition shadow-sm">
                        ✏️ Modifier
                    </a>
                @endcan
                @can('check-permission', ['Patrimoine & Equipements', 'suppression'])
                    <form action="{{ route('ouvrages.destroy', $ouvrage->id_ouvrage) }}" method="POST"
                        onsubmit="return confirm('Attention, la suppression de cet ouvrage est définitive et il sera détaché de tous les tronçons associés. Confirmer ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-50 text-red-600 border border-red-200 text-sm font-bold rounded-lg hover:bg-red-100 transition">
                            🗑️ Supprimer
                        </button>
                    </form>
                @endcan
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">📏 Caractéristiques Techniques
                        </h3>
                    </div>
                    <div class="p-6 grid grid-cols-2 gap-6 text-sm">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Franchissement
                                (obstacle enjambé)</p>
                            <p class="font-bold text-slate-900 text-lg">{{ $ouvrage->franchissement ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Voie Portée
                                (traversante)</p>
                            <p class="font-bold text-slate-900 text-lg">{{ $ouvrage->voie_portee ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Domaine</p>
                            <p class="font-bold text-slate-900">{{ $ouvrage->domaine ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Classe / Longueur
                                Mur</p>
                            <p class="font-bold text-slate-900">{{ $ouvrage->classe_longueur_mur ?? '-' }}</p>
                        </div>

                        <div class="col-span-2 grid grid-cols-2 gap-4 mt-2">
                            <div class="bg-blue-50/50 p-4 rounded-lg border border-blue-100">
                                <p class="text-[10px] text-blue-600 font-bold uppercase tracking-wider mb-1">Latitude</p>
                                <p class="text-slate-800 font-mono font-medium">{{ $ouvrage->latitude ?? 'Non définie' }}
                                </p>
                            </div>
                            <div class="bg-blue-50/50 p-4 rounded-lg border border-blue-100">
                                <p class="text-[10px] text-blue-600 font-bold uppercase tracking-wider mb-1">Longitude</p>
                                <p class="text-slate-800 font-mono font-medium">{{ $ouvrage->longitude ?? 'Non définie' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h3
                        class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">
                        📝 Commentaires / Observations</h3>
                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-100 text-slate-700 text-sm leading-relaxed">
                        {{ $ouvrage->commentaire ?? 'Aucun commentaire ou désordre signalé pour cet ouvrage.' }}
                    </div>
                </div>
            </div>

            <div class="space-y-6">

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-5 flex items-center gap-2">
                        <span>⚖️</span> Réglementation & Suivi
                    </h3>

                    <ul class="space-y-3">
                        <li
                            class="flex items-center gap-3 p-3 rounded-lg border {{ $ouvrage->sous_loi_didier ? 'bg-emerald-50 border-emerald-100 text-emerald-800' : 'bg-slate-50 border-slate-100 text-slate-400' }}">
                            <span class="text-lg">{{ $ouvrage->sous_loi_didier ? '✅' : '❌' }}</span>
                            <span class="text-sm font-bold">Soumis à la Loi Didier</span>
                        </li>
                        <li
                            class="flex items-center gap-3 p-3 rounded-lg border {{ $ouvrage->est_programme_national ? 'bg-emerald-50 border-emerald-100 text-emerald-800' : 'bg-slate-50 border-slate-100 text-slate-400' }}">
                            <span class="text-lg">{{ $ouvrage->est_programme_national ? '✅' : '❌' }}</span>
                            <span class="text-sm font-bold">Inscrit Programme National</span>
                        </li>
                        <li
                            class="flex items-center gap-3 p-3 rounded-lg border {{ $ouvrage->dimension_sup_2m ? 'bg-emerald-50 border-emerald-100 text-emerald-800' : 'bg-slate-50 border-slate-100 text-slate-400' }}">
                            <span class="text-lg">{{ $ouvrage->dimension_sup_2m ? '✅' : '❌' }}</span>
                            <span class="text-sm font-bold">Ouverture > 2 mètres</span>
                        </li>
                    </ul>

                    <div class="mt-6 border-t border-slate-100 pt-4">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Date transmission
                            état</p>
                        <p class="font-bold text-slate-800">
                            {{ $ouvrage->date_transmission_etat ? \Carbon\Carbon::parse($ouvrage->date_transmission_etat)->format('d/m/Y') : 'Non renseignée' }}
                        </p>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden p-1">
                    <div class="p-3 bg-slate-50 border-b border-slate-100 flex justify-between items-center rounded-t-lg">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">🗺️ Localisation de l'ouvrage
                        </h3>
                    </div>
                    <div id="map" data-lat="{{ $ouvrage->latitude ?? 45.928 }}"
                        data-lng="{{ $ouvrage->longitude ?? 6.223 }}" data-geojson="{{ $ouvrage->geojson ?? '' }}"
                        style="height: 350px; min-height: 350px; width: 100%;" class="rounded-b-lg z-0 relative">
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col">
                    <h3
                        class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2 flex items-center gap-2">
                        <span>🤝</span> Communes Partenaires
                    </h3>

                    <ul class="space-y-3 mb-6">
                        @forelse($communesLiees as $commune)
                            <li
                                class="flex items-center justify-between p-3 bg-indigo-50/50 border border-indigo-100 rounded-lg">
                                <div>
                                    <p class="text-sm font-bold text-indigo-900">{{ $commune->nom_commune }}</p>
                                    <p class="text-xs text-indigo-700/70">{{ $commune->code_postal }}
                                        {{ $commune->siret_mairie ? '• SIRET: ' . $commune->siret_mairie : '' }}
                                    </p>
                                </div>

                                @can('check-permission', ['Patrimoine & Equipements', 'suppression'])
                                    <form
                                        action="{{ route('ouvrages.communes.destroy', ['ouvrage' => $ouvrage->id_ouvrage, 'commune' => $commune->id_commune]) }}"
                                        method="POST"
                                        onsubmit="return confirm('Voulez-vous retirer cette commune de la gestion partagée de l\'ouvrage ?');">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="text-red-500 hover:text-red-700 bg-white p-1.5 rounded-md border border-red-100 shadow-sm transition"
                                            title="Retirer la commune">
                                            ❌
                                        </button>
                                    </form>
                                @endcan
                            </li>
                        @empty
                            <li class="text-sm text-slate-400 italic text-center py-4">Cet ouvrage est géré de manière
                                exclusive (aucune commune partenaire).</li>
                        @endforelse
                    </ul>

                    @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                        @if($communesDisponibles->count() > 0)
                            <form action="{{ route('ouvrages.communes.store', $ouvrage->id_ouvrage) }}" method="POST"
                                class="mt-auto border-t border-slate-100 pt-4">
                                @csrf
                                <label class="flex justify-between items-end mb-2">
                                    <span class="block text-xs font-bold text-slate-700 uppercase">Ajouter un partenariat</span>
                                    <a href="{{ route('communes.create') }}"
                                        class="text-[10px] font-bold text-blue-600 hover:underline">
                                        + Nouvelle commune manquante
                                    </a>
                                </label>

                                <div class="flex gap-2">
                                    <select name="id_commune" required
                                        class="w-full border-slate-300 rounded-md text-sm focus:ring-blue-500">
                                        <option value="">-- Sélectionner une commune --</option>
                                        @foreach($communesDisponibles as $cd)
                                            <option value="{{ $cd->id_commune }}">{{ $cd->nom_commune }} ({{ $cd->code_postal }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit"
                                        class="px-3 py-2 bg-blue-600 text-white font-bold rounded-md hover:bg-blue-700 transition shadow-sm">
                                        ➕
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="mt-auto border-t border-slate-100 pt-4 text-center space-y-2">
                                <p class="text-xs text-slate-400 italic">Toutes les communes disponibles sont déjà associées.
                                </p>
                                <a href="{{ route('communes.create') }}"
                                    class="inline-block text-xs font-bold text-blue-600 hover:underline bg-blue-50 px-3 py-1.5 rounded">
                                    + Créer une nouvelle commune dans la base
                                </a>
                            </div>
                        @endif
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
            // 1. On cible la carte dans le HTML
            var mapElement = document.getElementById('map');

            if (!mapElement) return;

            // 2. On récupère les données stockées dans les attributs data-* (Immunisé contre Prettier !)
            var lat = parseFloat(mapElement.getAttribute('data-lat'));
            var lng = parseFloat(mapElement.getAttribute('data-lng'));
            var geojsonStr = mapElement.getAttribute('data-geojson');

            // 3. Initialisation de la carte
            var map = L.map('map').setView([lat, lng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap',
                maxZoom: 19
            }).addTo(map);

            // 4. Traitement des géométries
            if (geojsonStr && geojsonStr.trim() !== '') {
                try {
                    var geoData = JSON.parse(geojsonStr);

                    var layer = L.geoJSON(geoData, {
                        style: {
                            color: "#f59e0b",
                            weight: 4
                        },
                        pointToLayer: function (feature, latlng) {
                            return L.marker(latlng);
                        }
                    }).addTo(map);

                    // Centrage
                    if (layer.getBounds && layer.getBounds().isValid()) {
                        map.fitBounds(layer.getBounds(), {
                            padding: [20, 20],
                            maxZoom: 17
                        });
                    }
                } catch (e) {
                    console.error("Erreur de lecture du GeoJSON :", e);
                }
            } else if (!isNaN(lat) && !isNaN(lng)) {
                // S'il n'y a pas de tracé GeoJSON, mais juste Lat/Lng
                L.marker([lat, lng]).addTo(map).bindPopup("<b>Emplacement de l'ouvrage</b>").openPopup();
            }
        });
    </script>
@endsection