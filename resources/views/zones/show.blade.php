@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('zones.index') }}" class="text-slate-400 hover:text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-slate-900">Zone : {{ $zone->nom_zone }}</h1>
        </div>
        <a href="{{ route('zones.edit', $zone->id_zone) }}"
            class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 px-4 rounded-lg shadow-sm">
            ✏️ Modifier
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="col-span-1 space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-3 mb-4">Informations
                    administratives</h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-xs font-bold text-slate-500 uppercase">Code Zone</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ $zone->code_zone ?? 'Non défini' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-slate-500 uppercase">Secteur Attaché</dt>
                        <dd class="mt-1 text-sm text-slate-900">
                            @if($secteur)
                            <a href="{{ route('secteurs.show', $secteur->id_secteur) }}"
                                class="text-blue-600 hover:underline font-medium">
                                {{ $secteur->nom_secteur }}
                            </a>
                            @else
                            <span class="text-red-500">Aucun secteur lié</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="col-span-1 lg:col-span-2">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col h-full">
                <div class="p-4 border-b border-slate-100 bg-slate-50">
                    <h3 class="text-lg font-bold text-slate-800">Délimitation Géographique de la Zone</h3>
                </div>
                <div class="flex-grow p-0">
                    <div id="map" data-geojson="{{ $zone->geojson ?? '' }}" style="height: 500px; width: 100%;"
                        class="z-0 relative"></div>
                </div>
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

    var geojsonStr = mapElement.getAttribute('data-geojson');
    var map = L.map('map').setView([45.928, 6.223], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap',
        maxZoom: 19
    }).addTo(map);

    if (geojsonStr && geojsonStr.trim() !== '') {
        try {
            var geoData = JSON.parse(geojsonStr);
            var layer = L.geoJSON(geoData, {
                style: {
                    color: "#10b981",
                    weight: 3,
                    fillColor: "#10b981",
                    fillOpacity: 0.2
                } // Vert pour les zones
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