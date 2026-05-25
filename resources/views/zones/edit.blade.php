@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.css" />
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('zones.show', $zone->id_zone) }}" class="text-slate-400 hover:text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-slate-900">✏️ Modifier la Zone : {{ $zone->nom_zone }}</h1>
        </div>

        <form action="{{ route('zones.update', $zone->id_zone) }}" method="POST" id="form-zone">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nom de la zone <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="nom_zone" value="{{ old('nom_zone', $zone->nom_zone) }}" required
                        class="w-full border-slate-300 rounded-md shadow-sm focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Code zone</label>
                    <input type="text" name="code_zone" value="{{ old('code_zone', $zone->code_zone) }}"
                        class="w-full border-slate-300 rounded-md shadow-sm focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Secteur parent <span
                            class="text-red-500">*</span></label>
                    <select name="id_secteur" required
                        class="w-full border-slate-300 rounded-md shadow-sm focus:ring-blue-500">
                        @foreach($secteurs as $secteur)
                        <option value="{{ $secteur->id_secteur }}"
                            {{ $secteur->id_secteur == $zone->id_secteur ? 'selected' : '' }}>
                            {{ $secteur->nom_secteur }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <input type="hidden" name="geojson_data" id="geojson_data" value="{{ $zone->geojson ?? '' }}">

            <div class="mb-6">
                <label class="block text-sm font-bold text-slate-700 mb-2">Tracé de la zone</label>
                <div id="map" data-geojson="{{ $zone->geojson ?? '' }}" style="height: 500px; width: 100%;"
                    class="rounded-lg border border-slate-300 z-0 relative"></div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('zones.show', $zone->id_zone) }}"
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

    var map = L.map('map').setView([45.928, 6.223], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap',
        maxZoom: 19
    }).addTo(map);

    map.pm.addControls({
        position: 'topleft',
        drawMarker: false,
        drawCircleMarker: false,
        drawPolyline: false,
        drawRectangle: true,
        drawPolygon: true,
        editMode: true,
        dragMode: true,
        removalMode: true,
    });

    if (geojsonStr && geojsonStr.trim() !== '') {
        try {
            var geoData = JSON.parse(geojsonStr);
            var existingLayer = L.geoJSON(geoData, {
                style: {
                    color: "#f59e0b",
                    weight: 3,
                    fillColor: "#f59e0b",
                    fillOpacity: 0.2
                }
            }).addTo(map);

            existingLayer.eachLayer(function(layer) {
                currentLayer = layer;
                currentLayer.on('pm:edit', updateGeoJSON);
                currentLayer.on('pm:dragend', updateGeoJSON);
            });

            if (existingLayer.getBounds && existingLayer.getBounds().isValid()) {
                map.fitBounds(existingLayer.getBounds(), {
                    padding: [30, 30]
                });
            }
        } catch (e) {
            console.error("Erreur chargement tracé :", e);
        }
    }

    map.on('pm:create', function(e) {
        if (currentLayer) {
            map.removeLayer(currentLayer);
        }
        currentLayer = e.layer;
        updateGeoJSON();
        currentLayer.on('pm:edit', updateGeoJSON);
        currentLayer.on('pm:dragend', updateGeoJSON);
    });

    map.on('pm:remove', function(e) {
        currentLayer = null;
        geojsonInput.value = '';
    });

    function updateGeoJSON() {
        if (currentLayer) {
            var geoData = currentLayer.toGeoJSON();
            geojsonInput.value = JSON.stringify(geoData.geometry);
        }
    }
});
</script>
@endsection