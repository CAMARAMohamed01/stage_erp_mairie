@extends('layouts.app')

@section('header_title', 'Modifier le Tronçon - ' . $troncon->numero_troncon)

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.css" />
@endsection
@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Initialisation de la carte sur Dingy-Saint-Clair par défaut
    var map = L.map('map').setView([45.928, 6.223], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19
    }).addTo(map);

    // Ajout des outils de dessin (Geoman)
    map.pm.addControls({
        position: 'topleft',
        drawMarker: false,
        drawCircleMarker: false,
        drawPolyline: true, // On autorise uniquement les lignes pour un tronçon
        drawRectangle: false,
        drawPolygon: false,
        drawCircle: false,
        drawText: false,
        editMode: true,
        dragMode: true,
        cutPolygon: false,
        removalMode: true,
    });

    map.pm.setLang('fr'); // Traduction en français

    var geojsonInput = document.getElementById('geojson_data');
    var currentLayer = null;

    // ==========================================
    // 1. CHARGEMENT ET INJECTION DU TRACÉ EXISTANT
    // ==========================================
    var existingGeoJSON = `{!! old('geojson_data', $troncon->geojson ?? 'null') !!}`;

    if (existingGeoJSON !== 'null' && existingGeoJSON !== '') {
        try {
            var geoData = JSON.parse(existingGeoJSON);

            // Dessiner la ligne récupérée de la base PostgreSQL
            var layerGroup = L.geoJSON(geoData, {
                style: {
                    color: "#2563eb",
                    weight: 5
                }
            }).addTo(map);

            // Assigner la ligne à Geoman pour pouvoir la manipuler
            layerGroup.eachLayer(function(layer) {
                currentLayer = layer;
                // Si l'utilisateur déplace un point de cette ligne existante, on met à jour le champ caché
                layer.on('pm:edit', updateGeoJSON);
            });

            // Zoomer automatiquement pour afficher tout le tronçon existant à l'écran
            if (layerGroup.getBounds().isValid()) {
                map.fitBounds(layerGroup.getBounds(), {
                    padding: [20, 20]
                });
            }

            // Initialiser le champ caché avec la valeur actuelle
            updateGeoJSON();

        } catch (e) {
            console.error("Erreur de chargement du tracé existant", e);
        }
    }

    // ==========================================
    // 2. GESTION DES ACTIONS DE DESSIN
    // ==========================================

    // Événement : Quand l'utilisateur a fini de dessiner une nouvelle ligne
    map.on('pm:create', function(e) {
        if (currentLayer) {
            map.removeLayer(currentLayer); // On supprime l'ancien tracé s'il y en avait un
        }
        currentLayer = e.layer;
        updateGeoJSON();

        // Écouter si la nouvelle ligne est modifiée après sa création
        currentLayer.on('pm:edit', updateGeoJSON);
    });

    // Événement : Quand l'utilisateur clique sur la poubelle pour supprimer la géométrie
    map.on('pm:remove', function(e) {
        currentLayer = null;
        geojsonInput.value = ''; // On vide le input hidden
    });

    // Fonction pour extraire la géométrie pure et l'envoyer dans le formulaire
    function updateGeoJSON() {
        if (currentLayer) {
            var geo = currentLayer.toGeoJSON().geometry;
            geojsonInput.value = JSON.stringify(geo);
        }
    }
});
</script>
@endsection
@section('content')
<div class="max-w-5xl mx-auto">
    <form action="{{ route('troncons.update', $troncon->id_troncon) }}" method="POST"
        class="bg-white p-8 rounded-xl border border-slate-200 shadow-sm space-y-8">
        @csrf
        @method('PUT')

        <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
            <span class="text-3xl">✏️</span>
            <div>
                <h2 class="text-xl font-bold text-slate-900">Modification du Tronçon</h2>
                <p class="text-sm text-slate-500">Mise à jour technique de {{ $troncon->numero_troncon }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            <div class="space-y-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase bg-slate-50 p-2 rounded">1. Identification</h3>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Voie associée</label>
                    <select name="id_voie" class="w-full border-slate-300 rounded-lg focus:ring-blue-500">
                        <option value="">-- Aucune voie associée (Optionnel) --</option>
                        @foreach($voies as $v)
                        <option value="{{ $v->id_voie }}"
                            {{ old('id_voie', $troncon->id_voie) == $v->id_voie ? 'selected' : '' }}>
                            {{ $v->numero_voie ? $v->numero_voie . ' - ' : '' }}{{ $v->nom_voie }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Numéro Tronçon <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="numero_troncon" required maxlength="150"
                            value="{{ old('numero_troncon', $troncon->numero_troncon ?? '') }}"
                            placeholder="Ex: TR-VC01-A"
                            class="w-full rounded-lg @error('numero_troncon') border-red-500 bg-red-50 focus:ring-red-500 @else border-slate-300 focus:ring-blue-500 @enderror">

                        @error('numero_troncon')
                        <p class="text-xs text-red-600 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Nom Portion</label>
                        <input type="text" name="nom_portion" maxlength="100"
                            value="{{ old('nom_portion', $troncon->nom_portion) }}"
                            class="w-full border-slate-300 rounded-lg">
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase bg-slate-50 p-2 rounded">2. Point Kilométrique
                    (PK) & Repères</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-50/50 p-3 border border-blue-100 rounded-lg">
                        <label class="block text-xs font-bold text-blue-800 uppercase mb-2">Début du tronçon</label>
                        <input type="number" step="0.01" name="pk_debut"
                            value="{{ old('pk_debut', $troncon->pk_debut) }}"
                            class="w-full mb-2 border-slate-300 rounded text-sm">
                        <input type="text" name="repere_physique_debut" maxlength="100"
                            value="{{ old('repere_physique_debut', $troncon->repere_physique_debut) }}"
                            class="w-full border-slate-300 rounded text-sm">
                    </div>
                    <div class="bg-blue-50/50 p-3 border border-blue-100 rounded-lg">
                        <label class="block text-xs font-bold text-blue-800 uppercase mb-2">Fin du tronçon</label>
                        <input type="number" step="0.01" name="pk_fin" value="{{ old('pk_fin', $troncon->pk_fin) }}"
                            class="w-full mb-2 border-slate-300 rounded text-sm">
                        <input type="text" name="repere_physique_fin" maxlength="100"
                            value="{{ old('repere_physique_fin', $troncon->repere_physique_fin) }}"
                            class="w-full border-slate-300 rounded text-sm">
                    </div>
                </div>
            </div>

            <div class="space-y-4 md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="col-span-3">
                    <h3 class="text-sm font-bold text-slate-800 uppercase bg-slate-50 p-2 rounded">3. Caractéristiques
                        Techniques</h3>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Revêtement</label>
                    <input type="text" name="type_revetement" maxlength="50"
                        value="{{ old('type_revetement', $troncon->type_revetement) }}"
                        class="w-full border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Dernier Goudronnage</label>
                    <input type="date" name="date_dernier_goudronnage"
                        value="{{ old('date_dernier_goudronnage', $troncon->date_dernier_goudronnage) }}"
                        class="w-full border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">État Physique</label>
                    <select name="etat_physique" class="w-full border-slate-300 rounded-lg">
                        <option value="">-- Évaluer --</option>
                        <option value="Très Bon"
                            {{ old('etat_physique', $troncon->etat_physique) == 'Très Bon' ? 'selected' : '' }}>Très Bon
                        </option>
                        <option value="Bon"
                            {{ old('etat_physique', $troncon->etat_physique) == 'Bon' ? 'selected' : '' }}>Bon</option>
                        <option value="Moyen"
                            {{ old('etat_physique', $troncon->etat_physique) == 'Moyen' ? 'selected' : '' }}>Moyen
                        </option>
                        <option value="Mauvais"
                            {{ old('etat_physique', $troncon->etat_physique) == 'Mauvais' ? 'selected' : '' }}>Mauvais
                        </option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Gabarit d'accessibilité</label>
                    <input type="text" name="gabarit_accessibilite" maxlength="50"
                        value="{{ old('gabarit_accessibilite', $troncon->gabarit_accessibilite) }}"
                        class="w-full border-slate-300 rounded-lg">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-1">Paysage / Environnement</label>
                    <input type="text" name="paysage_environnement" maxlength="50"
                        value="{{ old('paysage_environnement', $troncon->paysage_environnement) }}"
                        class="w-full border-slate-300 rounded-lg">
                </div>
            </div>

            <div class="space-y-4 md:col-span-2">
                <h3 class="text-sm font-bold text-slate-800 uppercase bg-slate-50 p-2 rounded">4. Liaisons & Dépendances
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Zone (Secteur)</label>
                        <select name="id_zone" class="w-full border-slate-300 rounded-lg text-sm">
                            <option value="">-- Aucune --</option>
                            @foreach($zones as $z)
                            <option value="{{ $z->id_zone }}"
                                {{ old('id_zone', $troncon->id_zone) == $z->id_zone ? 'selected' : '' }}>
                                {{ $z->nom_zone }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Ouvrage Lié</label>
                        <select name="id_ouvrage_lie" class="w-full border-slate-300 rounded-lg text-sm">
                            <option value="">-- Aucun --</option>
                            @foreach($ouvrages as $o)
                            <option value="{{ $o->id_ouvrage }}"
                                {{ old('id_ouvrage_lie', $troncon->id_ouvrage_lie) == $o->id_ouvrage ? 'selected' : '' }}>
                                {{ $o->nom_ouvrage }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Ouvrage au Début</label>
                        <select name="id_ouvrage_debut" class="w-full border-slate-300 rounded-lg text-sm">
                            <option value="">-- Aucun --</option>
                            @foreach($ouvrages as $o)
                            <option value="{{ $o->id_ouvrage }}"
                                {{ old('id_ouvrage_debut', $troncon->id_ouvrage_debut) == $o->id_ouvrage ? 'selected' : '' }}>
                                {{ $o->nom_ouvrage }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Ouvrage à la Fin</label>
                        <select name="id_ouvrage_fin" class="w-full border-slate-300 rounded-lg text-sm">
                            <option value="">-- Aucun --</option>
                            @foreach($ouvrages as $o)
                            <option value="{{ $o->id_ouvrage }}"
                                {{ old('id_ouvrage_fin', $troncon->id_ouvrage_fin) == $o->id_ouvrage ? 'selected' : '' }}>
                                {{ $o->nom_ouvrage }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

        </div>
        <div class="space-y-4 md:col-span-2">
            <h3
                class="text-sm font-bold text-slate-800 uppercase bg-slate-50 p-2 rounded flex justify-between items-center">
                <span>📍 Tracé Géographique</span>
                <span class="text-xs text-blue-600 font-normal normal-case">Modifiez les points avec l'outil
                    d'édition</span>
            </h3>

            <input type="hidden" name="geojson_data" id="geojson_data" value="{{ old('geojson_data') }}">

            <div id="map" class="h-96 w-full rounded-lg border border-slate-300 z-0 relative z-[1]"></div>
        </div>

</div>
<div class="flex justify-end gap-3 pt-6 border-t border-slate-100"></div>

<div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
    <button type="button" onclick="history.back()"
        class="px-5 py-2.5 border border-slate-300 text-slate-700 font-bold rounded-lg hover:bg-slate-50 transition">Annuler</button>
    <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition">
        Enregistrer les modifications</button>
</div>
</form>
</div>
@endsection