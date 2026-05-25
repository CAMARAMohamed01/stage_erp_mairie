@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.css" />
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h1 class="text-2xl font-bold text-slate-900 mb-6">📄 Saisir une Parcelle Cadastrale</h1>

        <form action="{{ route('parcelles.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Section <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="section_cadastrale" required maxlength="1"
                        class="w-full border-slate-300 rounded-md shadow-sm uppercase placeholder:text-slate-400"
                        placeholder="A, B, C...">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">N° Parcelle <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="num_parcelle" required class="w-full border-slate-300 rounded-md shadow-sm"
                        placeholder="Ex: 124">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Surface (m²)</label>
                    <input type="number" step="0.01" name="surface_cadastrale"
                        class="w-full border-slate-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Lieu-dit <span
                            class="text-red-500">*</span></label>
                    <select name="id_lieu_dit" required class="w-full border-slate-300 rounded-md shadow-sm">
                        <option value="">-- Sélectionner --</option>
                        @foreach($lieuxDits as $lieuDit)
                        <option value="{{ $lieuDit->id_lieu_dit }}">{{ $lieuDit->nom_lieu_dit }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <input type="hidden" name="geojson_data" id="geojson_data">

            <div class="mb-6">
                <label class="block text-sm font-bold text-slate-700 mb-2">Tracé Cadastral</label>
                <div id="map" style="height: 500px; width: 100%;"
                    class="rounded-lg border border-slate-300 z-0 relative"></div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('parcelles.index') }}"
                    class="px-4 py-2 border border-slate-300 text-slate-700 font-bold rounded-lg hover:bg-slate-50">Annuler</a>
                <button type="submit"
                    class="px-4 py-2 bg-violet-600 text-white font-bold rounded-lg hover:bg-violet-700 shadow-sm">💾
                    Enregistrer</button>
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
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    map.pm.addControls({
        position: 'topleft',
        drawMarker: false,
        drawCircleMarker: false,
        drawPolyline: false,
        drawCircle: false,
        drawText: false
    });

    var currentLayer = null;
    var geojsonInput = document.getElementById('geojson_data');

    map.on('pm:create', function(e) {
        if (currentLayer) map.removeLayer(currentLayer);
        currentLayer = e.layer;
        updateGeoJSON();
        currentLayer.on('pm:edit pm:dragend', updateGeoJSON);
    });
    map.on('pm:remove', function() {
        currentLayer = null;
        geojsonInput.value = '';
    });

    function updateGeoJSON() {
        if (currentLayer) geojsonInput.value = JSON.stringify(currentLayer.toGeoJSON().geometry);
    }
});
</script>
@endsection