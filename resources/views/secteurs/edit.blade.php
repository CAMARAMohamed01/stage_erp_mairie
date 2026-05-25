@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.css" />
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('secteurs.show', $secteur->id_secteur) }}" class="text-slate-400 hover:text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-slate-900">✏️ Modifier le Secteur : {{ $secteur->nom_secteur }}</h1>
        </div>

        <form action="{{ route('secteurs.update', $secteur->id_secteur) }}" method="POST" id="form-secteur">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nom du secteur <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="nom_secteur" value="{{ old('nom_secteur', $secteur->nom_secteur) }}"
                        required class="w-full border-slate-300 rounded-md shadow-sm focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Code secteur</label>
                    <input type="text" name="code_secteur" value="{{ old('code_secteur', $secteur->code_secteur) }}"
                        class="w-full border-slate-300 rounded-md shadow-sm focus:ring-blue-500">
                </div>
            </div>

            <input type="hidden" name="geojson_data" id="geojson_data" value="{{ $secteur->geojson ?? '' }}">

            <div class="mb-6">
                <label class="block text-sm font-bold text-slate-700 mb-2">Tracé du secteur</label>
                <div id="map" data-geojson="{{ $secteur->geojson ?? '' }}" style="height: 500px; width: 100%;"
                    class="rounded-lg border border-slate-300 z-0 relative"></div>
                <p class="text-xs text-slate-500 mt-2">ℹ️ Vous pouvez modifier les points du tracé existant ou le
                    supprimer (bouton poubelle) pour en redessiner un nouveau.</p>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('secteurs.show', $secteur->id_secteur) }}"
                    class="px-4 py-2 border border-slate-300 text-slate-700 font-bold rounded-lg hover:bg-slate-50">Annuler</a>
                <button type="submit"
                    class="px-4 py-2 bg-amber-500 text-white font-bold rounded-lg hover:bg-amber-600 shadow-sm">
                    💾 Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var mapElement = document.getElementById('map');
    if (!mapElement) return;

    var geojsonInput = document.getElementById('geojson_data');
    var geojsonStr = mapElement.getAttribute('data-geojson');
    var currentLayer = null;

    // 1. Initialisation
    var map = L.map('map').setView([45.928, 6.223], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap',
        maxZoom: 19
    }).addTo(map);

    // 2. Outils de dessin
    map.pm.addControls({
        position: 'topleft',
        drawMarker: false,
        drawCircleMarker: false,
        drawPolyline: false,
        drawRectangle: true,
        drawPolygon: true,
        drawCircle: false,
        drawText: false,
        editMode: true,
        dragMode: true,
        cutPolygon: false,
        removalMode: true,
    });

    // 3. Charger le tracé existant
    if (geojsonStr && geojsonStr.trim() !== '') {
        try {
            var geoData = JSON.parse(geojsonStr);
            var existingLayer = L.geoJSON(geoData, {
                style: {
                    color: "#f59e0b",
                    weight: 3,
                    fillColor: "#f59e0b",
                    fillOpacity: 0.2
                } // Couleur ambre pour l'édition
            }).addTo(map);

            // On récupère le polygone à l'intérieur du groupe GeoJSON pour pouvoir l'éditer
            existingLayer.eachLayer(function(layer) {
                currentLayer = layer;

                // On écoute les modifications sur ce tracé existant
                currentLayer.on('pm:edit', updateGeoJSON);
                currentLayer.on('pm:dragend', updateGeoJSON);
            });

            if (existingLayer.getBounds && existingLayer.getBounds().isValid()) {
                map.fitBounds(existingLayer.getBounds(), {
                    padding: [30, 30]
                });
            }
        } catch (e) {
            console.error("Erreur au chargement du tracé :", e);
        }
    }

    // 4. Gestion de la création d'un nouveau tracé (si l'ancien a été effacé)
    map.on('pm:create', function(e) {
        if (currentLayer) {
            map.removeLayer(currentLayer); // Un seul polygone autorisé
        }
        currentLayer = e.layer;
        updateGeoJSON();

        currentLayer.on('pm:edit', updateGeoJSON);
        currentLayer.on('pm:dragend', updateGeoJSON);
    });

    // 5. Gestion de la suppression
    map.on('pm:remove', function(e) {
        currentLayer = null;
        geojsonInput.value = ''; // On vide le champ pour supprimer la géométrie en base
    });

    // 6. Mise à jour du champ caché
    function updateGeoJSON() {
        if (currentLayer) {
            var geoData = currentLayer.toGeoJSON();
            geojsonInput.value = JSON.stringify(geoData.geometry);
        }
    }
});
</script>
@endsection