@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.css" />
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h1 class="text-2xl font-bold text-slate-900 mb-6">🗺️ Créer un nouvelle Zone</h1>

        <form action="{{ route('zones.store') }}" method="POST" id="form-zone">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nom de la zone <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="nom_zone" required
                        class="w-full border-slate-300 rounded-md shadow-sm focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Code zone</label>
                    <input type="text" name="code_zone"
                        class="w-full border-slate-300 rounded-md shadow-sm focus:ring-blue-500"
                        placeholder="Ex: ZA-02">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Secteur parent <span
                            class="text-red-500">*</span></label>
                    <select name="id_secteur" required
                        class="w-full border-slate-300 rounded-md shadow-sm focus:ring-blue-500">
                        <option value="">-- Choisir un secteur --</option>
                        @foreach($secteurs as $secteur)
                        <option value="{{ $secteur->id_secteur }}">{{ $secteur->nom_secteur }}
                            ({{ $secteur->code_secteur ?? 'Sans code' }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <input type="hidden" name="geojson_data" id="geojson_data">

            <div class="mb-6">
                <label class="block text-sm font-bold text-slate-700 mb-2">Tracé de la zone (Dessiner un
                    polygone)</label>
                <div id="map" style="height: 500px; width: 100%;"
                    class="rounded-lg border border-slate-300 z-0 relative"></div>
                <p class="text-xs text-slate-500 mt-2">ℹ️ Utilisez l'outil polygone ou rectangle sur la gauche pour
                    délimiter la zone.</p>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('zones.index') }}"
                    class="px-4 py-2 border border-slate-300 text-slate-700 font-bold rounded-lg hover:bg-slate-50">Annuler</a>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 shadow-sm">
                    💾 Enregistrer la zone
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
        drawCircle: false,
        editMode: true,
        dragMode: true,
        removalMode: true,
    });

    var currentLayer = null;
    var geojsonInput = document.getElementById('geojson_data');

    map.on('pm:create', function(e) {
        if (currentLayer) {
            map.removeLayer(currentLayer);
        }
        currentLayer = e.layer;
        updateGeoJSON();
        currentLayer.on('pm:edit', updateGeoJSON);
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