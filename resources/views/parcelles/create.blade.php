@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.css" />
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h1 class="text-2xl font-bold text-slate-900 mb-6">📄 Saisir une Parcelle Cadastrale</h1>

        @error('geojson_data')
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-sm font-semibold rounded-lg">
            🛑 Le tracé géométrique de la parcelle sur la carte est obligatoire.
        </div>
        @enderror

        <form action="{{ route('parcelles.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Section <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="section_cadastrale" value="{{ old('section_cadastrale') }}" required
                        maxlength="2"
                        class="w-full border-slate-300 rounded-md shadow-sm uppercase placeholder:text-slate-400 focus:ring-violet-500 focus:border-violet-500 @error('section_cadastrale') border-red-500 @enderror"
                        placeholder="A, AB, ZC...">
                    @error('section_cadastrale')
                    <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">N° Parcelle <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="num_parcelle" value="{{ old('num_parcelle') }}" required maxlength="5"
                        class="w-full border-slate-300 rounded-md shadow-sm focus:ring-violet-500 focus:border-violet-500 @error('num_parcelle') border-red-500 @enderror"
                        placeholder="Ex: 124">
                    @error('num_parcelle')
                    <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Surface (m²)</label>
                    <input type="number" step="0.01" name="surface_cadastrale" value="{{ old('surface_cadastrale') }}"
                        class="w-full border-slate-300 rounded-md shadow-sm focus:ring-violet-500 focus:border-violet-500 @error('surface_cadastrale') border-red-500 @enderror">
                    @error('surface_cadastrale')
                    <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Lieu-dit <span
                            class="text-slate-400 font-normal text-xs">(Optionnel)</span></label>
                    <select name="id_lieu_dit"
                        class="w-full border-slate-300 rounded-md shadow-sm focus:ring-violet-500 focus:border-violet-500 @error('id_lieu_dit') border-red-500 @enderror">
                        <option value="">-- Aucun --</option>
                        @foreach($lieuxDits as $lieuDit)
                        <option value="{{ $lieuDit->id_lieu_dit }}"
                            {{ old('id_lieu_dit') == $lieuDit->id_lieu_dit ? 'selected' : '' }}>
                            {{ $lieuDit->nom_lieu_dit }}
                        </option>
                        @endforeach
                    </select>
                    @error('id_lieu_dit')
                    <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <input type="hidden" name="geojson_data" id="geojson_data" value="{{ old('geojson_data') }}">

            <div class="mb-6">
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Tracé Cadastral <span class="text-slate-400 font-normal text-xs">(Utilisez l'outil polygone en haut
                        à gauche pour dessiner les limites)</span>
                </label>
                <div id="map" style="height: 500px; width: 100%;"
                    class="rounded-lg border @error('geojson_data') border-red-400 @else border-slate-300 @enderror z-0 relative">
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('parcelles.index') }}"
                    class="px-4 py-2 border border-slate-300 text-slate-700 font-bold rounded-lg hover:bg-slate-50 transition">Anuler</a>
                <button type="submit"
                    class="px-4 py-2 bg-violet-600 text-white font-bold rounded-lg hover:bg-violet-700 shadow-sm transition">💾
                    Enregistrer la parcelle</button>
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

    // Configuration des contrôles Geoman (Seuls le polygone et le rectangle sont utiles pour le cadastre)
    map.pm.addControls({
        position: 'topleft',
        drawMarker: false,
        drawCircleMarker: false,
        drawPolyline: false,
        drawCircle: false,
        drawText: false,
        cutPolygon: false
    });

    var currentLayer = null;
    var geojsonInput = document.getElementById('geojson_data');

    // Si la validation a échoué mais qu'un tracé avait déjà été dessiné, on le réaffiche
    if (geojsonInput.value) {
        try {
            var savedGeometry = JSON.parse(geojsonInput.value);
            currentLayer = L.geoJSON(savedGeometry, {
                style: {
                    color: "#8b5cf6",
                    weight: 3,
                    fillColor: "#8b5cf6",
                    fillOpacity: 0.3
                }
            }).addTo(map);

            // On centre la carte sur le tracé récupéré
            var bounds = currentLayer.getBounds();
            if (bounds.isValid()) {
                map.fitBounds(bounds, {
                    padding: [50, 50]
                });
            }

            // On réactive le mode édition sur la couche restaurée
            currentLayer.eachLayer(function(layer) {
                currentLayer = layer; // Extraire la couche brute pour Geoman
                currentLayer.on('pm:edit pm:dragend', updateGeoJSON);
            });
        } catch (e) {
            console.error("Impossible de restaurer le tracé précédent :", e);
        }
    }

    // Gestion de la création d'un nouveau tracé
    map.on('pm:create', function(e) {
        if (currentLayer) map.removeLayer(currentLayer);
        currentLayer = e.layer;
        updateGeoJSON();

        // Écoute des modifications une fois dessiné
        currentLayer.on('pm:edit pm:dragend', updateGeoJSON);
    });

    // Gestion de la suppression du tracé via la corbeille Geoman
    map.on('pm:remove', function() {
        currentLayer = null;
        geojsonInput.value = '';
    });

    function updateGeoJSON() {
        if (currentLayer) {
            geojsonInput.value = JSON.stringify(currentLayer.toGeoJSON().geometry);
        }
    }
});
</script>
@endsection