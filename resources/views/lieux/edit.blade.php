@extends('layouts.app')

@section('header_title', 'Modifier le Lieu : ' . $lieu->nom_lieu)

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.css" />
@endsection

@section('content')
<div class="max-w-4xl mx-auto pb-12">

    <div class="mb-6 flex justify-between items-end">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Modification du Lieu</h1>
            <p class="text-sm text-slate-500 mt-1">Mise à jour des informations pour {{ $lieu->nom_lieu }}.</p>
        </div>
        <a href="{{ route('lieux.show', $lieu->id_lieu) }}"
            class="text-sm font-semibold text-slate-600 hover:text-slate-900">← Annuler</a>
    </div>

    @if($errors->any())
    <div class="mb-4 p-4 bg-red-50 text-red-700 rounded-lg border border-red-200 text-sm font-semibold">
        Veuillez vérifier les informations saisies.
        <ul class="list-disc ml-5 mt-2 font-normal">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('lieux.update', $lieu->id_lieu) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">📋 Identité de l'espace
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nom du Lieu
                        <span class="text-red-500">*</span></label>
                    <input type="text" name="nom_lieu" value="{{ old('nom_lieu', $lieu->nom_lieu) }}" required
                        class="w-full rounded-lg border-slate-300 focus:ring-slate-900 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Typologie /
                        Catégorie</label>
                    <input type="text" name="typologie_lieu" value="{{ old('typologie_lieu', $lieu->typologie_lieu) }}"
                        class="w-full rounded-lg border-slate-300 focus:ring-slate-900 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Surface
                        (m²)</label>
                    <input type="number" step="0.01" name="surface_m2"
                        value="{{ old('surface_m2', $lieu->surface_m2) }}"
                        class="w-full rounded-lg border-slate-300 focus:ring-slate-900 text-sm">
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">🕒 Horaires</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label
                        class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Ouverture</label>
                    <input type="time" name="horaire_ouverture"
                        value="{{ old('horaire_ouverture', $lieu->horaire_ouverture ? \Carbon\Carbon::parse($lieu->horaire_ouverture)->format('H:i') : '') }}"
                        class="w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div>
                    <label
                        class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Fermeture</label>
                    <input type="time" name="horaire_fermeture"
                        value="{{ old('horaire_fermeture', $lieu->horaire_fermeture ? \Carbon\Carbon::parse($lieu->horaire_fermeture)->format('H:i') : '') }}"
                        class="w-full rounded-lg border-slate-300 text-sm">
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">📍 Cadastre, Adresse &
                Rattachement
            </h2>
            <div class="space-y-4">

                {{-- NOUVEAU : Adresse optionnelle --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Adresse
                        (Optionnelle)</label>
                    <select name="id_adresse"
                        class="w-full rounded-lg border-slate-300 focus:ring-slate-900 text-sm bg-white">
                        <option value="">-- Aucune adresse liée --</option>
                        @foreach($adresses as $adr)
                        <option value="{{ $adr->id_adresse }}"
                            {{ old('id_adresse', $lieu->id_adresse) == $adr->id_adresse ? 'selected' : '' }}>
                            {{ $adr->num_rue }} {{ $adr->nom_voie }} ({{ $adr->ville }})
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- NOUVEAU : Sélection multiple pour les parcelles (N:N) --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Parcelles
                        Cadastrales (Optionnel)</label>
                    <select name="parcelles[]" multiple size="4"
                        class="w-full rounded-lg border-slate-300 focus:ring-slate-900 text-sm bg-slate-50">
                        @foreach($parcelles as $p)
                        @php
                        // Vérifie si la parcelle a été postée (en cas d'erreur formulaire) OU si elle est déjà
                        // rattachée au lieu en base
                        $isSelected = is_array(old('parcelles')) ? in_array($p->id_parcelle, old('parcelles')) :
                        $lieu->parcelles->contains('id_parcelle', $p->id_parcelle);
                        @endphp
                        <option value="{{ $p->id_parcelle }}" {{ $isSelected ? 'selected' : '' }}>
                            Section {{ $p->section_cadastrale }} - N° {{ $p->num_parcelle }} ({{ $p->nom_lieu_dit }})
                        </option>
                        @endforeach
                    </select>
                    <span class="text-[10px] text-slate-500 mt-1 block">Maintenez la touche CTRL (ou CMD sur Mac) pour
                        sélectionner plusieurs parcelles.</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Bâtiment
                            hôte</label>
                        <select name="id_batiment" class="w-full rounded-lg border-slate-300 text-sm">
                            <option value="">-- Aucun --</option>
                            @foreach($batiments as $bat)
                            <option value="{{ $bat->id_batiment }}"
                                {{ old('id_batiment', $lieu->id_batiment) == $bat->id_batiment ? 'selected' : '' }}>
                                {{ $bat->nom_bat }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Classification
                            ERP</label>
                        <select name="id_type_erp" class="w-full rounded-lg border-slate-300 text-sm">
                            <option value="">-- Non classé --</option>
                            @foreach($types_erp as $erp)
                            <option value="{{ $erp->id_type_erp }}"
                                {{ old('id_type_erp', $lieu->id_type_erp) == $erp->id_type_erp ? 'selected' : '' }}>Cat.
                                {{ $erp->categorie_erp }} - Type {{ $erp->type_erp }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm mt-5">
                    <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">📍 Localisation
                        Cartographique (Édition)</h2>
                    <input type="hidden" name="geojson_data" id="geojson_data"
                        value="{{ old('geojson_data', $lieu->geojson_lieu ?? '') }}">
                    <div id="map" data-geojson="{{ $lieu->geojson_lieu ?? '' }}" style="height: 350px; width: 100%;"
                        class="rounded-lg border border-slate-300 z-0 relative"></div>
                    <p class="text-xs text-slate-500 mt-2">ℹ️ <b>Utilisez l'icône de marqueur</b> pour placer ou
                        déplacer le lieu sur la carte.</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">💼 Comptabilité &
                Règlementation</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Immobilisation
                        Comptable</label>
                    <select name="id_immo" class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900">
                        <option value="">-- Aucune --</option>
                        @foreach($immos as $immo)
                        <option value="{{ $immo->id_immo }}"
                            {{ old('id_immo', $lieu->id_immo) == $immo->id_immo ? 'selected' : '' }}>
                            {{ $immo->num_inventaire }} ({{ $immo->libelle_comptable }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Arrêté ou
                        Décision Réglementaire</label>
                    <select name="id_decision_reglement"
                        class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900">
                        <option value="">-- Aucune décision liée --</option>
                        @foreach($decisions as $dec)
                        <option value="{{ $dec->id_decision }}"
                            {{ old('id_decision_reglement', $lieu->id_decision_reglement) == $dec->id_decision ? 'selected' : '' }}>
                            {{ $dec->numero_decision }} ({{ \Carbon\Carbon::parse($dec->date_decision)->format('Y') }})
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm mb-6">
            <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">🌿 Contrats & Prestations
                associés</h2>

            <div>
                <label for="id_contrats" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Contrats rattachés (Entretien espaces verts, Gardiennage, Assurance...)
                </label>
                <select name="id_contrats[]" id="id_contrats" multiple size="4"
                    class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 focus:ring-slate-900 text-sm">
                    @foreach($contrats as $c)
                    @php
                    $isSelected = isset($lieu) && $lieu->contratsAdministratifs->contains('id_contrat', $c->id_contrat);
                    $isOldSelected = is_array(old('id_contrats')) && in_array($c->id_contrat, old('id_contrats'));
                    @endphp
                    <option value="{{ $c->id_contrat }}" {{ ($isSelected || $isOldSelected) ? 'selected' : '' }}>
                        {{ $c->numero_contrat ?? 'Sans N°' }} - {{ $c->type_contrat }}
                    </option>
                    @endforeach
                </select>
                <span class="text-[10px] text-slate-500 mt-1 block">Maintenez la touche CTRL (ou CMD sur Mac) pour
                    sélectionner plusieurs lignes.</span>
            </div>
        </div>
        <div class="flex justify-end pt-4">
            <button type="submit"
                class="px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-lg shadow-md transition">
                💾 Sauvegarder les modifications
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var mapElement = document.getElementById('map');
    if (!mapElement) return;

    var map = L.map('map').setView([45.928, 6.223], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19
    }).addTo(map);

    // Sécurité pour forcer l'affichage correct de la carte
    setTimeout(function() {
        map.invalidateSize();
    }, 200);

    // Initialisation des outils d'édition Geoman
    if (map.pm) {
        map.pm.addControls({
            position: 'topleft',
            drawMarker: true,
            drawPolygon: false,
            drawRectangle: false,
            drawPolyline: false,
            drawCircle: false,
            drawCircleMarker: false,
            editMode: true,
            dragMode: true,
            removalMode: true
        });
    } else {
        console.error("Leaflet-Geoman n'a pas pu être chargé !");
    }

    var currentMarker = null;
    var geojsonInput = document.getElementById('geojson_data');
    // Gère aussi les erreurs de validation en récupérant d'abord l'input s'il existe
    var geojsonStr = geojsonInput.value || mapElement.getAttribute('data-geojson');

    // 1. Charger le point existant si présent en base (ou dans l'input via old())
    if (geojsonStr && geojsonStr.trim() !== '') {
        try {
            var geoData = JSON.parse(geojsonStr);
            if (geoData.type === 'FeatureCollection') {
                geoData = geoData.features[0].geometry;
            }
            var coords = [geoData.coordinates[1], geoData.coordinates[0]];
            currentMarker = L.marker(coords).addTo(map);
            currentMarker.on('pm:dragend', updateGeoJSON);
            map.setView(coords, 18); // Zoom sur le point
        } catch (e) {
            console.error("Erreur de chargement du marqueur existant", e);
        }
    }

    // 2. Gestion création nouveau point
    map.on('pm:create', function(e) {
        if (currentMarker) map.removeLayer(currentMarker);
        currentMarker = e.layer;
        updateGeoJSON();
        currentMarker.on('pm:dragend', updateGeoJSON);
    });

    // 3. Gestion suppression
    map.on('pm:remove', function() {
        currentMarker = null;
        geojsonInput.value = '';
    });

    function updateGeoJSON() {
        if (currentMarker) {
            var geoData = currentMarker.toGeoJSON();
            geojsonInput.value = JSON.stringify(geoData.geometry);
        }
    }
});
</script>
@endsection