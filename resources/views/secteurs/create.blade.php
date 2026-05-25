@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.css" />
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h1 class="text-2xl font-bold text-slate-900 mb-6">🗺️ Créer un nouveau Secteur</h1>

        <form action="{{ route('secteurs.store') }}" method="POST" id="form-secteur">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nom du secteur <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="nom_secteur" required
                        class="w-full border-slate-300 rounded-md shadow-sm focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Code secteur</label>
                    <input type="text" name="code_secteur"
                        class="w-full border-slate-300 rounded-md shadow-sm focus:ring-blue-500"
                        placeholder="Ex: NORD-01">
                </div>
            </div>

            <input type="hidden" name="geojson_data" id="geojson_data">

            <div class="mb-6">
                <label class="block text-sm font-bold text-slate-700 mb-2">Tracé du secteur (Dessiner un
                    polygone)</label>
                <div id="map" style="height: 500px; width: 100%;"
                    class="rounded-lg border border-slate-300 z-0 relative"></div>
                <p class="text-xs text-slate-500 mt-2">ℹ️ Utilisez l'outil polygone sur la gauche de la carte pour
                    délimiter le secteur.</p>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('secteurs.index') }}"
                    class="px-4 py-2 border border-slate-300 text-slate-700 font-bold rounded-lg hover:bg-slate-50">Annuler</a>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 shadow-sm">
                    💾 Enregistrer le secteur
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
    // 1. Initialisation de la carte (centrée sur Dingy-Saint-Clair par exemple)
    var map = L.map('map').setView([45.928, 6.223], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap',
        maxZoom: 19
    }).addTo(map);

    // 2. Ajout des outils de dessin Geoman
    map.pm.addControls({
        position: 'topleft',
        drawMarker: false,
        drawCircleMarker: false,
        drawPolyline: false,
        drawRectangle: true,
        drawPolygon: true, // On autorise uniquement les polygones pour les secteurs
        drawCircle: false,
        drawText: false,
        editMode: true,
        dragMode: true,
        cutPolygon: false,
        removalMode: true,
    });

    // 3. Variables pour stocker la couche dessinée
    var currentLayer = null;
    var geojsonInput = document.getElementById('geojson_data');

    // 4. Écouteur d'événement : quand un dessin est créé
    map.on('pm:create', function(e) {
        // Si on a déjà dessiné un secteur, on l'efface (un seul tracé par secteur)
        if (currentLayer) {
            map.removeLayer(currentLayer);
        }

        currentLayer = e.layer;

        // On met à jour le champ caché avec les coordonnées GeoJSON
        updateGeoJSON();

        // On écoute aussi les modifications sur ce dessin (si l'utilisateur ajuste les points)
        currentLayer.on('pm:edit', updateGeoJSON);
    });

    // Écouteur d'événement : quand un dessin est supprimé
    map.on('pm:remove', function(e) {
        currentLayer = null;
        geojsonInput.value = ''; // On vide le champ caché
    });

    // Fonction pour convertir le dessin en texte pour le formulaire
    function updateGeoJSON() {
        if (currentLayer) {
            var geoData = currentLayer.toGeoJSON();
            // On ne garde que la partie "geometry" (coordinates et type)
            geojsonInput.value = JSON.stringify(geoData.geometry);
        }
    }
});
</script>
@endsection