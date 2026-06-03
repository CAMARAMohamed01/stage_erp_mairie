@extends('layouts.app')

@section('header_title', 'Ajouter un nouveau bâtiment')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.css" />
@endsection

@section('content')
<div class="max-w-4xl mx-auto pb-12">

    <div class="mb-6 flex justify-between items-end">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Nouvelle Fiche Patrimoine</h1>
            <p class="text-sm text-slate-500 mt-1">Saisie complète d'un bâtiment et création d'entités liées à la volée.
            </p>
        </div>
        <a href="{{ route('batiments.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">←
            Annuler</a>
    </div>

    <form action="{{ route('batiments.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- 📋 Informations Générales --}}
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">📋 Informations Générales
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nom du Bâtiment
                        <span class="text-red-500">*</span></label>
                    <input type="text" name="nom_bat" required placeholder="Ex: Groupe Scolaire Maurice Anjot"
                        class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900 focus:border-slate-900">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Surface totale
                        (m²)</label>
                    <input type="number" step="0.01" name="surface_totale_m2"
                        class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900 focus:border-slate-900">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Date de
                        construction</label>
                    <input type="date" name="date_construction"
                        class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900 focus:border-slate-900">
                </div>
            </div>
        </div>

        {{-- 📍 Localisation & Cadastre --}}
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">📍 Localisation & Cadastre
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Adresse Physique
                            <span class="text-red-500">*</span></label>
                        <button type="button" onclick="openModal('modalAdresse')"
                            class="text-xs text-blue-600 font-semibold hover:underline">➕ Créer adresse</button>
                    </div>
                    <select name="id_adresse" id="select_adresse" required
                        class="w-full rounded-lg border-slate-300 text-sm bg-slate-50">
                        <option value="">-- Sélectionner --</option>
                        @foreach($adresses as $adresse)
                        {{-- On embarque la latitude et la longitude de la BAN directement dans l'option --}}
                        <option value="{{ $adresse->id_adresse }}" data-lat="{{ $adresse->latitude }}"
                            data-lng="{{ $adresse->longitude }}">
                            {{ $adresse->num_rue }} {{ $adresse->nom_voie }}, {{ $adresse->ville }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Parcelle
                            Cadastrale <span class="text-red-500">*</span></label>
                        <button type="button" onclick="openModal('modalParcelle')"
                            class="text-xs text-blue-600 font-semibold hover:underline">➕ Créer parcelle</button>
                    </div>
                    <select name="id_parcelle" id="select_parcelle" required
                        class="w-full rounded-lg border-slate-300 text-sm bg-slate-50">
                        <option value="">-- Sélectionner --</option>
                        @foreach($parcelles as $parcelle)
                        <option value="{{ $parcelle->id_parcelle }}">Section {{ $parcelle->section_cadastrale }} - N°
                            {{ $parcelle->num_parcelle }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Localisation sur la
                    carte (Point GPS)</label>
                <input type="hidden" name="geojson_data" id="geojson_data">
                <div id="map" style="height: 350px; width: 100%;"
                    class="rounded-lg border border-slate-300 z-0 relative"></div>
                <p class="text-xs text-slate-500 mt-2">ℹ️ Utilisez l'outil marqueur à gauche pour placer le bâtiment
                    précisément sur la carte.</p>
            </div>
        </div>

        {{-- 💼 Administration & Classification --}}
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">💼 Administration &
                Classification</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                <div class="md:col-span-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Classification
                        ERP <span class="text-red-500">*</span></label>
                    <select name="id_type_erp" required class="w-full rounded-lg border-slate-300 text-sm bg-slate-50">
                        <option value="">-- Sélectionner --</option>
                        @foreach($types_erp as $erp)
                        <option value="{{ $erp->id_type_erp }}">Cat. {{ $erp->categorie_erp }} - Type
                            {{ $erp->type_erp }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Immobilisation
                        Comptable <span class="text-red-500">*</span></label>
                    <select name="id_immo" id="select_immo" required
                        class="w-full rounded-lg border-slate-300 text-sm bg-slate-50">
                        <option value="">-- Sélectionner --</option>
                        @foreach($immos_disponibles as $immo)
                        <option value="{{ $immo->id_immo }}">{{ $immo->num_inventaire }}
                            ({{ $immo->libelle_comptable }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-1 md:col-span-3 pt-2 border-t border-slate-100 mt-2">
                    <label for="id_contrats"
                        class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Contrats associés (Assurance, Maintenance, Nettoyage...)
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
                    <span class="text-[10px] text-slate-500 mt-1 block">Maintenez CTRL (ou CMD sur Mac) pour
                        sélectionner plusieurs contrats.</span>
                </div>

            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit"
                class="px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-lg shadow-md transition">
                💾 Valider l'enregistrement
            </button>
        </div>
    </form>
</div>

{{-- Modale Adresse --}}
<div id="modalAdresse"
    class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-xl border border-slate-200 max-w-md w-full p-6 space-y-4">
        <h3 class="text-base font-bold text-slate-900">➕ Ajouter une adresse</h3>
        <form id="formAdresse" class="space-y-3">
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-500">N° Rue</label>
                    <input type="number" name="num_rue" required class="w-full rounded-md border-slate-300 text-sm">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-slate-500">Nom de la Voie</label>
                    <input type="text" name="nom_voie" required class="w-full rounded-md border-slate-300 text-sm">
                </div>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-500">Code Postal</label>
                    <input type="text" name="code_postal" required class="w-full rounded-md border-slate-300 text-sm">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-slate-500">Ville</label>
                    <input type="text" name="ville" required class="w-full rounded-md border-slate-300 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500">Lieu-dit Rattaché</label>
                <select name="id_lieu_dit" required class="w-full rounded-md border-slate-300 text-sm bg-slate-50">
                    @foreach($lieu_dits as $ld)
                    <option value="{{ $ld->id_lieu_dit }}">{{ $ld->nom_lieu_dit }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeModal('modalAdresse')"
                    class="px-3 py-2 border rounded-md text-xs font-semibold text-slate-600">Fermer</button>
                <button type="button"
                    onclick="submitModal('formAdresse', '{{ route('api.adresse.store') }}', 'select_adresse')"
                    class="px-3 py-2 bg-blue-600 text-white rounded-md text-xs font-semibold">Créer</button>
            </div>
        </form>
    </div>
</div>

{{-- Modale Parcelle --}}
<div id="modalParcelle"
    class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-xl border border-slate-200 max-w-md w-full p-6 space-y-4">
        <h3 class="text-base font-bold text-slate-900">➕ Créer une parcelle cadastrale</h3>
        <form id="formParcelle" class="space-y-3">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-500">Section (ex: A)</label>
                    <input type="text" name="section_cadastrale" maxlength="1" required
                        class="w-full rounded-md border-slate-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500">N° Parcelle (ex: 0142)</label>
                    <input type="text" name="num_parcelle" maxlength="5" required
                        class="w-full rounded-md border-slate-300 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500">Type de Parcelle</label>
                <input type="text" name="type_parcelle" placeholder="Domaine Public, Privé..."
                    class="w-full rounded-md border-slate-300 text-sm">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-500">Lieu-dit</label>
                    <select name="id_lieu_dit" required class="w-full rounded-md border-slate-300 text-sm bg-slate-50">
                        @foreach($lieu_dits as $ld)
                        <option value="{{ $ld->id_lieu_dit }}">{{ $ld->nom_lieu_dit }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500">Immobilisation liée</label>
                    <select name="id_immo" required class="w-full rounded-md border-slate-300 text-sm bg-slate-50">
                        @foreach($immos_disponibles as $immo)
                        <option value="{{ $immo->id_immo }}">{{ $immo->num_inventaire }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeModal('modalParcelle')"
                    class="px-3 py-2 border rounded-md text-xs font-semibold text-slate-600">Fermer</button>
                <button type="button"
                    onclick="submitModal('formParcelle', '{{ route('api.parcelle.store') }}', 'select_parcelle')"
                    class="px-3 py-2 bg-blue-600 text-white rounded-md text-xs font-semibold">Créer</button>
            </div>
        </form>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.js"></script>

<script>
// --- GESTION DES FENÊTRES MODALES & AJAX ---
function openModal(id) {
    const modal = document.getElementById(id);
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

//"fn" est redevenu "function" et "class_list" est redevenu "classList"
function closeModal(id) {
    const modal = document.getElementById(id);
    modal.classList.remove('flex');
    modal.classList.add('hidden');
}

// "fn" est redevenu "function" et "class_list" est redevenu "classList"
function submitModal(formId, targetUrl, selectDestId) {
    const formElement = document.getElementById(formId);
    const formData = new FormData(formElement);

    fetch(targetUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.id) {
                const selectElement = document.getElementById(selectDestId);
                const newOption = new Option(data.label, data.id, true, true);
                selectElement.add(newOption);

                closeModal(formElement.closest('[id^="modal"]').id);
                formElement.reset();
            }
        })
        .catch(error => alert("Erreur d'insertion. Vérifiez la conformité des données foncières."));
}
document.addEventListener("DOMContentLoaded", function() {
    //  Configuration initiale de la carte
    var map = L.map('map', {
        maxZoom: 22
    }).setView([45.928, 6.223], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 22,
        maxNativeZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    map.pm.addControls({
        position: 'topleft',
        drawMarker: true,
        drawCircleMarker: false,
        drawPolyline: false,
        drawRectangle: false,
        drawPolygon: false,
        drawCircle: false,
        editMode: true,
        dragMode: true,
        removalMode: true
    });

    var currentMarker = null;
    var geojsonInput = document.getElementById('geojson_data');

    // Changement d'adresse pour auto-centrage
    var selectAdresse = document.getElementById('select_adresse');

    selectAdresse.addEventListener('change', function() {
        // On récupère l'option actuellement sélectionnée
        var selectedOption = this.options[this.selectedIndex];

        var lat = selectedOption.getAttribute('data-lat');
        var lng = selectedOption.getAttribute('data-lng');

        // Si l'adresse possède des coordonnées valides
        if (lat && lng && lat !== "" && lng !== "") {
            var targetLatLng = [parseFloat(lat), parseFloat(lng)];

            // On déplace la carte sur le point GPS officiel de la BAN avec un zoom précis
            map.setView(targetLatLng, 19);

            // On place automatiquement le marqueur Geoman à cet endroit !
            if (currentMarker) {
                map.removeLayer(currentMarker);
            }

            // On crée un nouveau marqueur Leaflet à l'emplacement précis
            currentMarker = L.marker(targetLatLng, {
                draggable: true
            }).addTo(map);

            // On active les capacités Geoman sur ce marqueur pour qu'il soit modifiable / supprimable
            currentMarker.pm.enable();

            // On met à jour le champ caché GeoJSON pour le contrôleur
            updateGeoJSON();

            // Si l'agent déplace le marqueur manuellement sur le toit, on met à jour la position
            currentMarker.on('pm:dragend', updateGeoJSON);
        }
    });

    // Événements classiques Geoman pour la création/suppression manuelle
    map.on('pm:create', function(e) {
        if (currentMarker) {
            map.removeLayer(currentMarker);
        }
        currentMarker = e.layer;
        updateGeoJSON();
        currentMarker.on('pm:dragend', updateGeoJSON);
    });

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