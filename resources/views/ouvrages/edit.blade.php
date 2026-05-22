@extends('layouts.app')

@section('header_title', 'Modifier l\'Ouvrage - ' . $ouvrage->nom_ouvrage)
@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.css" />
@endsection
@section('content')
<div class="max-w-5xl mx-auto">
    <form action="{{ route('ouvrages.update', $ouvrage->id_ouvrage) }}" method="POST"
        class="bg-white p-8 rounded-xl border border-slate-200 shadow-sm space-y-8">
        @csrf
        @method('PUT')

        <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
            <span class="text-3xl">🌉</span>
            <div>
                <h2 class="text-xl font-bold text-slate-900">Modification de l'Ouvrage d'Art</h2>
                <p class="text-sm text-slate-500">Mise à jour des informations techniques et réglementaires.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            <div class="space-y-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase bg-slate-50 p-2 rounded">1. Identification &
                    Nomenclature</h3>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Nom de l'ouvrage <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="nom_ouvrage" required maxlength="100"
                        value="{{ old('nom_ouvrage', $ouvrage->nom_ouvrage) }}"
                        class="w-full border-slate-300 rounded-lg">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Type d'ouvrage</label>
                        <select name="type_ouvrage" class="w-full border-slate-300 rounded-lg">
                            <option value="">-- Sélectionner --</option>
                            @foreach(['Pont', 'Mur de soutènement', 'Passerelle', 'Buse', 'Autre'] as $type)
                            <option value="{{ $type }}"
                                {{ old('type_ouvrage', $ouvrage->type_ouvrage) == $type ? 'selected' : '' }}>{{ $type }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Domaine</label>
                        <input type="text" name="domaine" maxlength="50" value="{{ old('domaine', $ouvrage->domaine) }}"
                            placeholder="Ex: Routier, Piétonnier..." class="w-full border-slate-300 rounded-lg">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Voie Rattachée (Mère)</label>
                    <select name="id_voie" class="w-full border-slate-300 rounded-lg focus:ring-blue-500">
                        <option value="">-- Indépendante / Aucune --</option>
                        @foreach($voies as $voie)
                        <option value="{{ $voie->id_voie }}"
                            {{ old('id_voie', $ouvrage->id_voie) == $voie->id_voie ? 'selected' : '' }}>
                            {{ $voie->nom_voie }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Voie Portée (Traversée)</label>
                    <input type="text" name="voie_portee" maxlength="100"
                        value="{{ old('voie_portee', $ouvrage->voie_portee) }}" placeholder="Ex: RD 903"
                        class="w-full border-slate-300 rounded-lg">
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase bg-slate-50 p-2 rounded">2. Caractéristiques &
                    Topographie</h3>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Franchissement (Ce que le pont
                        enjambe)</label>
                    <input type="text" name="franchissement" maxlength="50"
                        value="{{ old('franchissement', $ouvrage->franchissement) }}"
                        placeholder="Ex: Cours d'eau (Le Fier)" class="w-full border-slate-300 rounded-lg">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Classe / Longueur du Mur</label>
                    <input type="text" name="classe_longueur_mur" maxlength="50"
                        value="{{ old('classe_longueur_mur', $ouvrage->classe_longueur_mur) }}"
                        placeholder="Ex: Classe A, ou 45 ml" class="w-full border-slate-300 rounded-lg">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-50/50 p-3 border border-blue-100 rounded-lg">
                        <label class="block text-xs font-bold text-blue-800 uppercase mb-1">Latitude</label>
                        <input type="number" step="0.00000001" name="latitude"
                            value="{{ old('latitude', $ouvrage->latitude) }}"
                            class="w-full border-slate-300 rounded text-sm">
                    </div>
                    <div class="bg-blue-50/50 p-3 border border-blue-100 rounded-lg">
                        <label class="block text-xs font-bold text-blue-800 uppercase mb-1">Longitude</label>
                        <input type="number" step="0.00000001" name="longitude"
                            value="{{ old('longitude', $ouvrage->longitude) }}"
                            class="w-full border-slate-300 rounded text-sm">
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase bg-slate-50 p-2 rounded">3. Réglementation & Suivi
                </h3>

                <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg space-y-4">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="sous_loi_didier" value="1"
                            {{ old('sous_loi_didier', $ouvrage->sous_loi_didier) ? 'checked' : '' }}
                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-5 h-5">
                        <span class="text-sm font-bold text-slate-700">Soumis à la Loi Didier</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="est_programme_national" value="1"
                            {{ old('est_programme_national', $ouvrage->est_programme_national) ? 'checked' : '' }}
                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-5 h-5">
                        <span class="text-sm font-bold text-slate-700">Inscrit au Programme National (PNP)</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="dimension_sup_2m" value="1"
                            {{ old('dimension_sup_2m', $ouvrage->dimension_sup_2m) ? 'checked' : '' }}
                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-5 h-5">
                        <span class="text-sm font-bold text-slate-700">Dimension / Ouverture > 2 mètres</span>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Date transmission état
                        (Préfecture/Département)</label>
                    <input type="date" name="date_transmission_etat"
                        value="{{ old('date_transmission_etat', $ouvrage->date_transmission_etat) }}"
                        class="w-full border-slate-300 rounded-lg text-sm">
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase bg-slate-50 p-2 rounded">4. Observations</h3>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Commentaires / État général</label>
                    <textarea name="commentaire" rows="5" class="w-full border-slate-300 rounded-lg text-sm"
                        placeholder="Désordres observés, notes d'inspection...">{{ old('commentaire', $ouvrage->commentaire) }}</textarea>
                </div>
            </div>

        </div>
        <div class="space-y-4 md:col-span-2">
            <h3
                class="text-sm font-bold text-slate-800 uppercase bg-slate-50 p-2 rounded flex justify-between items-center">
                <span>📍 Localisation Géométrique</span>
                <span class="text-xs text-blue-600 font-normal normal-case">Placez un point ou dessinez l'emprise</span>
            </h3>

            <input type="hidden" name="geojson_data" id="geojson_data" value="{{ old('geojson_data') }}">
            <div id="map" class="h-96 w-full rounded-lg border border-slate-300 relative z-[1]"></div>
        </div>

        <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
            <button type="button" onclick="history.back()"
                class="px-5 py-2.5 border border-slate-300 text-slate-700 font-bold rounded-lg hover:bg-slate-50 transition">Annuler</button>
            <button type="submit"
                class="px-5 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition">✅
                Enregistrer les modifications</button>
        </div>
    </form>
</div>
@endsection
@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var map = L.map('map').setView([45.928, 6.223], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19
    }).addTo(map);

    // CONFIGURATION GEOMAN POUR UN OUVRAGE
    map.pm.addControls({
        position: 'topleft',
        drawMarker: true, // OUI (Pour placer un pont avec un simple clic)
        drawPolygon: true, // OUI (Pour dessiner l'emprise d'un grand pont)
        drawPolyline: true, // OUI (Pour un long mur de soutènement)
        drawCircleMarker: false,
        drawRectangle: true, // OUI (Très utile pour les bâtiments/ponts carrés)
        drawCircle: false,
        drawText: false,
        editMode: true,
        dragMode: true,
        cutPolygon: false,
        removalMode: true,
    });

    map.pm.setLang('fr');

    var geojsonInput = document.getElementById('geojson_data');
    var currentLayer = null;

    // 1. CHARGEMENT DU TRACÉ EXISTANT (Seulement pour edit.blade.php)
    var existingGeoJSON = `{!! old('geojson_data', $ouvrage->geojson ?? 'null') !!}`;

    if (existingGeoJSON !== 'null' && existingGeoJSON !== '') {
        try {
            var geoData = JSON.parse(existingGeoJSON);

            var layerGroup = L.geoJSON(geoData, {
                style: {
                    color: "#f59e0b",
                    weight: 4
                }, // Couleur Ambre pour les ouvrages
                pointToLayer: function(feature, latlng) {
                    return L.marker(latlng); // Transformation du point en marqueur visuel
                }
            }).addTo(map);

            layerGroup.eachLayer(function(layer) {
                currentLayer = layer;
                layer.on('pm:edit', updateGeoJSON);
                layer.on('pm:dragend', updateGeoJSON); // Spécifique aux marqueurs qu'on glisse
            });

            if (layerGroup.getBounds && layerGroup.getBounds().isValid()) {
                map.fitBounds(layerGroup.getBounds(), {
                    padding: [20, 20],
                    maxZoom: 17
                });
            } else if (currentLayer && currentLayer.getLatLng) {
                map.setView(currentLayer.getLatLng(), 17); // Zoom spécifique si c'est un point unique
            }

            updateGeoJSON();

        } catch (e) {
            console.error("Erreur de chargement de la géométrie existante", e);
        }
    }

    // 2. GESTION DU DESSIN
    map.on('pm:create', function(e) {
        if (currentLayer) {
            map.removeLayer(currentLayer); // On ne permet qu'une seule forme par ouvrage
        }
        currentLayer = e.layer;
        updateGeoJSON();

        currentLayer.on('pm:edit', updateGeoJSON);
        if (currentLayer.on) currentLayer.on('pm:dragend', updateGeoJSON);
    });

    map.on('pm:remove', function(e) {
        currentLayer = null;
        geojsonInput.value = '';
    });

    function updateGeoJSON() {
        if (currentLayer) {
            var geo = currentLayer.toGeoJSON().geometry;
            geojsonInput.value = JSON.stringify(geo);
        }
    }
});
</script>
@endsection