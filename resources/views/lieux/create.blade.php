@extends('layouts.app')

@section('header_title', 'Ajouter un Lieu / Espace Public')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.css" />
@endsection

@section('content')
    <div class="max-w-4xl mx-auto pb-12">

        <div class="mb-6 flex justify-between items-end">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Nouvel Espace Public</h1>
                <p class="text-sm text-slate-500 mt-1">Référencement d'un parc, terrain de sport, place ou cimetière.</p>
            </div>
            <a href="{{ route('lieux.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
                ← Annuler
            </a>
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

        <form action="{{ route('lieux.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">🌳 Identité de l'espace
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nom du Lieu
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="nom_lieu" required value="{{ old('nom_lieu') }}"
                            placeholder="Ex: Parc des Capucins, Place de la Mairie..."
                            class="w-full rounded-lg border-slate-300 focus:ring-slate-900 focus:border-slate-900 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Typologie /
                            Catégorie</label>
                        <input type="text" name="typologie_lieu" value="{{ old('typologie_lieu') }}"
                            placeholder="Ex: Espace Vert, Équipement Sportif..."
                            class="w-full rounded-lg border-slate-300 focus:ring-slate-900 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Surface Totale
                            (m²)</label>
                        <input type="number" step="0.01" name="surface_m2" value="{{ old('surface_m2') }}"
                            class="w-full rounded-lg border-slate-300 focus:ring-slate-900 text-sm font-mono">
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">🕒 Modalités d'accès au
                    public</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Heure
                            d'Ouverture</label>
                        <input type="time" name="horaire_ouverture" value="{{ old('horaire_ouverture') }}"
                            class="w-full rounded-lg border-slate-300 focus:ring-slate-900 text-sm text-slate-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Heure de
                            Fermeture</label>
                        <input type="time" name="horaire_fermeture" value="{{ old('horaire_fermeture') }}"
                            class="w-full rounded-lg border-slate-300 focus:ring-slate-900 text-sm text-slate-600">
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">📍 Cadastre, Adresse &
                    Rattachement</h2>

                <div class="space-y-5">
                    {{-- NOUVEAU : Adresse optionnelle --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Adresse
                            (Optionnelle)</label>
                        <select name="id_adresse"
                            class="w-full rounded-lg border-slate-300 focus:ring-slate-900 text-sm bg-white">
                            <option value="">-- Aucune adresse liée --</option>
                            @foreach($adresses as $adr)
                                <option value="{{ $adr->id_adresse }}" {{ old('id_adresse') == $adr->id_adresse ? 'selected' : '' }}>
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
                            @foreach($parcelles as $parcelle)
                                @php
                                    $isSelected = is_array(old('parcelles')) && in_array($parcelle->id_parcelle, old('parcelles'));
                                @endphp
                                <option value="{{ $parcelle->id_parcelle }}" {{ $isSelected ? 'selected' : '' }}>
                                    Section {{ $parcelle->section_cadastrale }} - N° {{ $parcelle->num_parcelle }}
                                    ({{ $parcelle->nom_lieu_dit }})
                                </option>
                            @endforeach
                        </select>
                        <span class="text-[10px] text-slate-500 mt-1 block">Maintenez la touche CTRL (ou CMD sur Mac) pour
                            sélectionner plusieurs parcelles.</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Bâtiment
                                Rattaché (Optionnel)</label>
                            <select name="id_batiment"
                                class="w-full rounded-lg border-slate-300 focus:ring-slate-900 text-sm bg-white">
                                <option value="">-- Aucun --</option>
                                @foreach($batiments as $bat)
                                    <option value="{{ $bat->id_batiment }}" {{ old('id_batiment') == $bat->id_batiment ? 'selected' : '' }}>
                                        {{ $bat->nom_bat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label
                                class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Classification
                                ERP (Optionnel)</label>
                            <select name="id_type_erp"
                                class="w-full rounded-lg border-slate-300 focus:ring-slate-900 text-sm bg-white">
                                <option value="">-- Non classé ERP --</option>
                                @foreach($types_erp as $erp)
                                    <option value="{{ $erp->id_type_erp }}" {{ old('id_type_erp') == $erp->id_type_erp ? 'selected' : '' }}>
                                        Cat. {{ $erp->categorie_erp }} - Type {{ $erp->type_erp }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm mt-5">
                        <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2"> Géolocalisation
                            (Point)
                        </h2>
                        <input type="hidden" name="geojson_data" id="geojson_data" value="{{ old('geojson_data') }}">
                        <div id="map" style="height: 350px; width: 100%;"
                            class="rounded-lg border border-slate-300 z-0 relative">
                        </div>
                        <p class="text-xs text-slate-500 mt-2">ℹ️ Utilisez l'outil marqueur à gauche pour placer le lieu sur
                            la carte.</p>
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
                        <select name="id_immo"
                            class="w-full rounded-lg border-slate-300 focus:ring-slate-900 text-sm bg-white">
                            <option value="">-- Aucune --</option>
                            @foreach($immos as $immo)
                                <option value="{{ $immo->id_immo }}" {{ old('id_immo') == $immo->id_immo ? 'selected' : '' }}>
                                    {{ $immo->num_inventaire }} ({{ $immo->libelle_comptable }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Arrêté ou
                            Décision Réglementaire</label>
                        <select name="id_decision_reglement"
                            class="w-full rounded-lg border-slate-300 focus:ring-slate-900 text-sm bg-white">
                            <option value="">-- Aucune décision liée --</option>
                            @foreach($decisions as $dec)
                                <option value="{{ $dec->id_decision }}" {{ old('id_decision_reglement') == $dec->id_decision ? 'selected' : '' }}>
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
                                $isOldSelected = is_array(old('id_contrats')) && in_array($c->id_contrat, old('id_contrats'));
                            @endphp
                            <option value="{{ $c->id_contrat }}" {{ $isOldSelected ? 'selected' : '' }}>
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
                    class="px-6 py-3 bg-green-700 hover:bg-green-600 text-white font-bold rounded-lg shadow-md transition">
                    💾 Valider la création du Lieu
                </button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var map = L.map('map').setView([45.928, 6.223], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(map);

            map.pm.addControls({
                position: 'topleft',
                drawMarker: true,
                drawPolygon: false,
                drawRectangle: false,
                editMode: true,
                dragMode: true,
                removalMode: true
            });

            var currentMarker = null;
            var geojsonInput = document.getElementById('geojson_data');

            // Restore marker on validation fail
            if (geojsonInput.value) {
                try {
                    var geojsonObj = JSON.parse(geojsonInput.value);
                    currentMarker = L.geoJSON(geojsonObj).addTo(map);
                } catch (e) { }
            }

            map.on('pm:create', function (e) {
                if (currentMarker) map.removeLayer(currentMarker);
                currentMarker = e.layer;
                updateGeoJSON();
                currentMarker.on('pm:dragend', updateGeoJSON);
            });

            function updateGeoJSON() {
                if (currentMarker) geojsonInput.value = JSON.stringify(currentMarker.toGeoJSON().geometry);
            }
        });
    </script>
@endsection