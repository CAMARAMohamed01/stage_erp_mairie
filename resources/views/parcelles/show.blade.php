@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('parcelles.index') }}" class="text-slate-400 hover:text-blue-600"><svg class="w-6 h-6"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg></a>
            <h1 class="text-2xl font-bold text-slate-900">Parcelle : {{ $parcelle->section_cadastrale }} -
                {{ $parcelle->num_parcelle }}
            </h1>
        </div>
        <a href="{{ route('parcelles.edit', $parcelle->id_parcelle) }}"
            class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 px-4 rounded-lg shadow-sm">✏️ Modifier</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="col-span-1 space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <dl class="space-y-4">
                    <div>
                        <dt class="text-xs font-bold text-slate-500 uppercase">Section & Numéro</dt>
                        <dd class="mt-1 text-sm font-bold text-slate-900">{{ $parcelle->section_cadastrale }} -
                            {{ $parcelle->num_parcelle }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-slate-500 uppercase">Lieu-dit</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ $lieuDit->nom_lieu_dit ?? 'Non défini' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-slate-500 uppercase">Surface Cadastrale</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ $parcelle->surface_cadastrale ?? 'N/A' }} m²</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="col-span-1 lg:col-span-2">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden h-full">
                <div id="map" data-geojson="{{ $parcelle->geojson ?? '' }}" style="height: 500px; width: 100%;"
                    class="z-0 relative"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var mapElement = document.getElementById('map');
    if (!mapElement) return;

    var map = L.map('map').setView([45.928, 6.223], 15); // Zoom plus proche pour les parcelles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19
    }).addTo(map);

    var geojsonStr = mapElement.getAttribute('data-geojson');
    if (geojsonStr && geojsonStr.trim() !== '') {
        try {
            var layer = L.geoJSON(JSON.parse(geojsonStr), {
                style: {
                    color: "#8b5cf6",
                    weight: 3,
                    fillColor: "#8b5cf6",
                    fillOpacity: 0.3
                } // Violet 
            }).addTo(map);
            if (layer.getBounds && layer.getBounds().isValid()) {
                map.fitBounds(layer.getBounds(), {
                    padding: [30, 30]
                });
            }
        } catch (e) {
            console.error("Erreur GeoJSON :", e);
        }
    }
});
</script>
@endsection