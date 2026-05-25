@extends('layouts.app')

@section('header_title', 'Superviseur Cartographique Global')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
#map {
    height: calc(100vh - 180px);
    width: 100%;
    z-index: 1;
}

.leaflet-popup-content-wrapper {
    border-radius: 8px;
    box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
}

.leaflet-popup-content {
    margin: 16px;
}

/* Styles pour les textes permanents sur la carte */
.map-label {
    background: transparent;
    border: none;
    box-shadow: none;
    font-weight: 800;
    /* Effet de contour blanc pour que le texte soit toujours lisible */
    text-shadow: -1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff, 1px 1px 0 #fff;
}

/* Tailles et couleurs spécifiques par niveau */
.label-secteur {
    color: #1e3a8a;
    font-size: 16px;
}

/* Bleu très foncé */
.label-zone {
    color: #064e3b;
    font-size: 13px;
}

/* Vert très foncé */
.label-parcelle {
    color: #4c1d95;
    font-size: 11px;
}

/* Violet très foncé */
</style>
@endsection

@section('content')
<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div id="map-data" data-secteurs="{{ json_encode($secteurs) }}" data-zones="{{ json_encode($zones) }}"
        data-parcelles="{{ json_encode($parcelles) }}" data-troncons="{{ json_encode($troncons) }}" class="hidden">
    </div>
    <div id="map"></div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var map = L.map('map').setView([45.928, 6.223], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap',
        maxZoom: 19
    }).addTo(map);

    var secteursGroup = L.layerGroup();
    var zonesGroup = L.layerGroup();
    var parcellesGroup = L.layerGroup();
    var tronconsGroup = L.layerGroup();

    var dataDiv = document.getElementById('map-data');
    var secteurs = JSON.parse(dataDiv.getAttribute('data-secteurs') || '[]');
    var zones = JSON.parse(dataDiv.getAttribute('data-zones') || '[]');
    var parcelles = JSON.parse(dataDiv.getAttribute('data-parcelles') || '[]');
    var troncons = JSON.parse(dataDiv.getAttribute('data-troncons') || '[]');

    // --- SECTEURS ---
    secteurs.forEach(function(item) {
        if (item.geojson) {
            var geoData = JSON.parse(item.geojson);
            var layer = L.geoJSON(geoData, {
                style: {
                    color: "#3b82f6",
                    weight: 4,
                    fillOpacity: 0.1
                }
            });

            var url = `/secteurs/${item.id_secteur}`;
            layer.bindPopup(
                `<div class="font-bold text-slate-800 text-base mb-1">Secteur: ${item.nom_secteur}</div><a href="${url}" class="text-sm text-blue-600 hover:underline">Accéder à la fiche &rarr;</a>`
            );

            // Ajout du texte permanent (Code, ou Nom si le code est vide)
            var labelText = item.code_secteur ? item.code_secteur : item.nom_secteur;
            layer.bindTooltip(labelText, {
                permanent: true,
                direction: 'center',
                className: 'map-label label-secteur'
            });

            secteursGroup.addLayer(layer);
        }
    });

    // --- ZONES ---
    zones.forEach(function(item) {
        if (item.geojson) {
            var geoData = JSON.parse(item.geojson);
            var layer = L.geoJSON(geoData, {
                style: {
                    color: "#10b981",
                    weight: 3,
                    fillOpacity: 0.2,
                    dashArray: '5, 5'
                }
            });

            var url = `/zones/${item.id_zone}`;
            layer.bindPopup(
                `<div class="font-bold text-slate-800 text-base mb-1">Zone: ${item.nom_zone}</div><a href="${url}" class="text-sm text-blue-600 hover:underline">Accéder à la fiche &rarr;</a>`
            );

            // Ajout du texte permanent : Nom de la zone + Code
            var labelText = item.code_zone ? `${item.nom_zone}<br>(${item.code_zone})` : item.nom_zone;
            layer.bindTooltip(labelText, {
                permanent: true,
                direction: 'center',
                className: 'map-label label-zone'
            });

            zonesGroup.addLayer(layer);
        }
    });

    // --- PARCELLES ---
    parcelles.forEach(function(item) {
        if (item.geojson) {
            var geoData = JSON.parse(item.geojson);
            var layer = L.geoJSON(geoData, {
                style: {
                    color: "#8b5cf6",
                    weight: 2,
                    fillOpacity: 0.3
                }
            });

            var url = `/parcelles/${item.id_parcelle}`;
            layer.bindPopup(
                `<div class="font-bold text-slate-800 text-base mb-1">Parcelle ${item.section_cadastrale}-${item.num_parcelle}</div><a href="${url}" class="text-sm text-blue-600 hover:underline">Accéder à la fiche &rarr;</a>`
            );

            // Ajout du texte permanent : Section et Numéro
            var labelText = `${item.section_cadastrale}-${item.num_parcelle}`;
            layer.bindTooltip(labelText, {
                permanent: true,
                direction: 'center',
                className: 'map-label label-parcelle'
            });

            parcellesGroup.addLayer(layer);
        }
    });

    // --- TRONÇONS DE VOIRIE ---
    troncons.forEach(function(item) {
        if (item.geojson) {
            var geoData = JSON.parse(item.geojson);
            var lineColor = "#64748b";
            if (item.etat_physique === "Dégradé" || item.etat_physique === "Mauvais") lineColor =
                "#ef4444";
            if (item.etat_physique === "Neuf" || item.etat_physique === "Bon") lineColor = "#22c55e";

            var layer = L.geoJSON(geoData, {
                style: {
                    color: lineColor,
                    weight: 5
                }
            });
            var url = `/voies`;
            layer.bindPopup(
                `<div class="font-bold text-slate-800 text-base mb-1">Portion: ${item.nom_portion || 'Non nommé'}</div><div class="text-xs text-slate-500 mb-2">État: ${item.etat_physique || 'Inconnu'}</div><a href="${url}" class="text-sm text-blue-600 hover:underline">Gérer la voirie &rarr;</a>`
            );
            tronconsGroup.addLayer(layer);
        }
    });

    secteursGroup.addTo(map);
    zonesGroup.addTo(map);
    tronconsGroup.addTo(map);

    var overlays = {
        "<span class='text-blue-600 font-bold'>🟦 Secteurs</span>": secteursGroup,
        "<span class='text-emerald-500 font-bold'>🟩 Zones</span>": zonesGroup,
        "<span class='text-violet-500 font-bold'>🟪 Parcelles</span>": parcellesGroup,
        "<span class='text-slate-600 font-bold'>🛣️ Voirie (Tronçons)</span>": tronconsGroup
    };

    L.control.layers(null, overlays, {
        collapsed: false
    }).addTo(map);
});
</script>
@endsection