@extends('layouts.app')

@section('header_title', 'Fiche Espace Public - ' . $lieu->nom_lieu)

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        <div
            class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-wrap justify-between items-center gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <span class="text-3xl">🌳</span>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $lieu->nom_lieu }}</h1>
                    <span
                        class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-50 text-green-700 border border-green-100">
                        {{ $lieu->typologie_lieu ?? 'Espace Extérieur' }}
                    </span>
                    @if($lieu->categorie_erp)
                        <span
                            class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-100">
                            ERP Cat. {{ $lieu->categorie_erp }}
                        </span>
                    @endif
                </div>

                {{-- NOUVEAU : Affichage de l'adresse et de la surface --}}
                <p class="text-sm text-slate-500 mt-2 flex items-center gap-1.5">
                    📍 Adresse :
                    <span class="text-slate-700 font-medium">
                        @if($lieu->nom_voie)
                            {{ $lieu->num_rue }} {{ $lieu->nom_voie }}, {{ $lieu->code_postal }} {{ $lieu->ville }}
                        @else
                            <span class="italic text-slate-400">Non renseignée</span>
                        @endif
                    </span>
                    | 📏
                    {{ $lieu->surface_m2 ? number_format($lieu->surface_m2, 2, ',', ' ') . ' m²' : 'Surface inconnue' }}
                </p>

                {{-- NOUVEAU : Affichage des parcelles multiples --}}
                <p class="text-xs text-slate-500 mt-1">
                    🗺️ Cadastre :
                    @forelse($parcelles as $p)
                        <span class="font-medium text-slate-700">Sec. {{ $p->section_cadastrale }} N°{{ $p->num_parcelle }}
                            ({{ $p->nom_lieu_dit }})</span>{{ $loop->last ? '' : ', ' }}
                    @empty
                        <span class="italic text-slate-400">Aucune parcelle cadastrale rattachée</span>
                    @endforelse
                </p>

                @if($lieu->horaire_ouverture)
                    <p class="text-xs text-slate-400 mt-1">🕒 Ouvert de
                        {{ \Carbon\Carbon::parse($lieu->horaire_ouverture)->format('H:i') }} à
                        {{ \Carbon\Carbon::parse($lieu->horaire_fermeture)->format('H:i') }}
                    </p>
                @endif

            </div>

            <div class="flex gap-2">
                <button onclick="history.back()"
                    class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 transition">
                    ← Retour
                </button>
                @can('check-permission', ['Patrimoine & Equipements', 'suppression'])
                    <form action="{{ route('lieux.destroy', $lieu->id_lieu) }}" method=" POST"
                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce lieu ?');">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-50 border border-red-200 text-red-600 text-sm font-semibold rounded-lg hover:bg-red-100 transition">
                            🗑️ Supprimer
                        </button>
                    </form>
                @endcan
                @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                    <a href="{{ route('lieux.edit', $lieu->id_lieu) }}"
                        class=" px-4 py-2 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition">
                        Modifier
                    </a>
                @endcan
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 bg-slate-50 border-b border-slate-200 font-bold text-sm text-slate-800">🌍 Localisation de
                l'Espace Public</div>

            {{-- NOUVEAU : On prépare un tableau JSON avec les géométries de TOUTES les parcelles --}}
            @php
                $parcellesGeoJson = $parcelles->pluck('geojson_parcelle')->filter()->toArray();
            @endphp

            <div id="map" data-lieu="{{ $lieu->geojson_lieu ?? '' }}" data-parcelles="{{ json_encode($parcellesGeoJson) }}"
                style="height: 350px; width: 100%; z-index: 1;" class="relative">
            </div>
        </div>

        @if(session('error'))
            <div class="p-4 bg-red-50 text-red-700 rounded-lg border border-red-200 text-sm font-semibold">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 flex items-center gap-4">
                        <div class="p-3 bg-indigo-50 text-indigo-600 rounded-lg">💼</div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Immobilisation</p>
                            <p class="text-sm font-semibold text-slate-900">
                                {{ $lieu->num_inventaire ?? 'Non inventorié' }}
                            </p>
                            <p class="text-xs text-slate-500">{{ $lieu->libelle_comptable ?? 'Aucun libellé associé' }}</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 flex items-center gap-4">
                        <div class="p-3 bg-amber-50 text-amber-600 rounded-lg">📜</div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Décision Réglementaire
                            </p>
                            <p class="text-sm font-semibold text-slate-900">
                                {{ $lieu->numero_decision ?? 'Aucune décision' }}
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ $lieu->date_decision ? \Carbon\Carbon::parse($lieu->date_decision)->format('d/m/Y') : 'Date non définie' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">🚪 Locaux Isolés
                            ({{ $locaux->count() }})</h3>
                        @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                            <a href="{{ route('locaux.create', ['id_lieu' => $lieu->id_lieu]) }}"
                                class=" text-xs text-blue-600 font-semibold hover:underline">➕ Ajouter un local</a>
                        @endcan
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse($locaux as $loc)
                            <div class="p-3 hover:bg-slate-50 flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-semibold">{{ $loc->nom_local }}</p>
                                    <p class="text-xs text-slate-500">{{ $loc->libelle_usage ?? 'Usage non défini' }}</p>
                                </div>
                                <a href="{{ route('locaux.show', $loc->id_local) }}" class=" text-xs text-blue-600">Voir →</a>
                            </div>
                        @empty
                            <p class="p-4 text-xs text-slate-400 italic">Aucun local bâti dans cet espace.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mt-6">
                    <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            ⚡ Réseaux & Compteurs ({{ $compteurs->count() }})
                        </h3>
                        @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                            <a href="{{ route('compteurs.create') }}"
                                class=" text-xs text-blue-600 font-semibold hover:underline">
                                ➕ Ajouter
                            </a>
                        @endcan
                    </div>
                    <div class="divide-y divide-slate-100 max-h-48 overflow-y-auto">
                        @forelse($compteurs as $compteur)
                            <div class="p-3 flex justify-between items-center hover:bg-slate-50">
                                <div class="flex items-center gap-3">
                                    <div class="text-xl">
                                        @if($compteur->type_reseau == 'Électricité') ⚡
                                        @elseif($compteur->type_reseau == 'Eau Potable') 💧
                                        @elseif($compteur->type_reseau == 'Gaz') 🔥
                                        @else ⚙️ @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">{{ $compteur->point_comptage }}</p>
                                        <p class="text-[10px] text-slate-500 uppercase">{{ $compteur->type_reseau }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($compteur->date_arret && \Carbon\Carbon::parse($compteur->date_arret)->isPast())
                                        <span
                                            class="text-[10px] font-bold px-2 py-0.5 rounded bg-red-100 text-red-700">Déposé</span>
                                    @else
                                        <span
                                            class="text-[10px] font-bold px-2 py-0.5 rounded bg-green-100 text-green-700">Actif</span>
                                    @endif
                                    <a href="{{ route('compteurs.show', $compteur->id_compteur) }}"
                                        class=" text-xs text-blue-600 font-bold hover:underline">
                                        Voir →
                                    </a>
                                </div>
                            </div>
                        @empty
                            <p class="p-4 text-xs text-slate-400 italic">Aucun compteur de fluide associé à ce lieu ou ses
                                locaux.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">⚙️ Équipements Extérieurs
                            ({{ $equipements->count() }})</h3>
                        @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                            <a href="{{ route('equipements.create', ['id_lieu' => $lieu->id_lieu]) }}"
                                class=" text-xs text-blue-600 font-semibold hover:underline">➕ Ajouter un équipement</a>
                        @endcan
                    </div>
                    <div class="divide-y divide-slate-100 max-h-48 overflow-y-auto">
                        @forelse($equipements as $equip)
                            <div class="p-3 flex justify-between items-center hover:bg-slate-50">
                                <span class="text-sm font-semibold">{{ $equip->nom_equipement }}</span>
                                <span
                                    class="text-xs px-2 py-1 bg-slate-100 rounded text-slate-600">{{ $equip->etat_fonctionnement ?? 'Opérationnel' }}</span>
                            </div>
                        @empty
                            <p class="p-4 text-xs text-slate-400 italic">Aucun équipement de type mobilier urbain ou jeu
                                répertorié.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200">
                        <h3 class="text-sm font-bold text-slate-800 tracking-tight flex items-center gap-2">
                            📋 Contrôles Réglementaires
                        </h3>
                    </div>
                    <div class="p-4">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="text-slate-400 font-bold border-b border-slate-100 pb-2">
                                    <th class="pb-2">Désignation</th>
                                    <th class="pb-2">Fréquence</th>
                                    <th class="pb-2 text-right">Obligatoire</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($controles as $ctrl)
                                    <tr>
                                        <td class="py-2.5 font-semibold text-slate-800">{{ $ctrl->designation }}</td>
                                        <td class="py-2.5 text-slate-500">{{ $ctrl->frequence_mois }} mois</td>
                                        <td class="py-2.5 text-right font-medium">
                                            @if($ctrl->est_legalement_obligatoire)
                                                <span
                                                    class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-700">OUI</span>
                                            @else
                                                <span
                                                    class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600">NON</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-4 text-center text-slate-400 italic">Aucun contrôle
                                            réglementaire requis pour ce lieu.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if(
                        $emplacements->count() > 0 || str_contains(strtolower($lieu->nom_lieu), 'cimetiere') ||
                        str_contains(strtolower($lieu->nom_lieu), 'cimetière')
                    )
                    <div
                        class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden border-l-4 border-l-purple-500">
                        <div class="p-4 bg-purple-50/50 border-b border-purple-100">
                            <h3 class="text-sm font-bold text-purple-800 flex items-center gap-2">⚰️ Emplacements Funéraires
                                ({{ $emplacements->count() }})</h3>
                        </div>
                        <div class="p-4 text-sm text-slate-600">
                            <p>Ce lieu dispose d'une configuration funéraire. Consultez le module dédié au cimetière pour gérer
                                les concessions et les défunts.</p>
                        </div>
                    </div>
                @endif

            </div>

            <div class="space-y-6">

                <div
                    class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden border-l-4 border-l-green-500">
                    <div class="p-4 bg-green-50/50 border-b border-green-100">
                        <h3 class="text-sm font-bold text-green-800 flex items-center gap-2">🌱 Patrimoine Végétal
                            ({{ $vegetaux->count() }})</h3>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse($vegetaux as $veg)
                            <div class="p-3 flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-semibold">{{ $veg->type_vegetal }}</p>
                                    <p class="text-xs text-slate-500">Espèce : {{ $veg->espece_vegetal ?? 'Non précisée' }}</p>
                                </div>
                                <span class="text-xs font-bold text-green-700 bg-green-100 px-2 py-1 rounded-full">Qté:
                                    {{ $veg->quantite ?? 1 }}</span>
                            </div>
                        @empty
                            <p class="p-4 text-xs text-slate-400 italic">Aucun arbre ou massif végétal recensé individuellement.
                            </p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200">
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">📅 Plan d'Entretien Espaces
                            Verts</h3>
                    </div>
                    <div class="p-4">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="text-slate-400 font-bold border-b border-slate-100 pb-2">
                                    <th class="pb-2">Tâche d'entretien</th>
                                    <th class="pb-2 text-right">Fréquence Standard</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($plans_entretien as $plan)
                                    <tr>
                                        <td class="py-2.5 font-semibold text-slate-700">{{ $plan->libelle_tache }}</td>
                                        <td class="py-2.5 text-right text-slate-500">
                                            {{ $plan->frequence_standard ?? 'Variable' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="py-4 text-center text-slate-400 italic">Aucun plan d'entretien
                                            régulier configuré.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">📂 Plans &
                            Documents</h2>

                        <ul class="space-y-3 mb-6">
                            @forelse($documents as $doc)
                                <li
                                    class="flex items-center justify-between p-3 bg-slate-50 border border-slate-100 rounded-lg transition hover:bg-slate-100">
                                    <div class="flex items-center gap-3">
                                        <span class="text-2xl">
                                            {{ in_array(strtolower($doc->type_doc), ['pdf']) ? '📄' : (in_array(strtolower($doc->type_doc), ['jpg', 'png', 'jpeg']) ? '🖼️' : '📁') }}
                                        </span>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800 line-clamp-1">{{ $doc->nom_fichier }}
                                            </p>
                                            <p class="text-xs text-slate-500">
                                                {{ \Carbon\Carbon::parse($doc->date_upload)->format('d/m/Y') }} •
                                                {{ number_format($doc->taille_ko, 0, ',', ' ') }} Ko
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <a href="{{ asset('storage/' . $doc->chemin_stockage) }}" target="_blank"
                                            class="text-blue-600 hover:text-blue-800 text-xs font-bold bg-blue-50 px-2 py-1 rounded border border-blue-100">
                                            Voir
                                        </a>
                                        @can('check-permission', ['Patrimoine & Equipements', 'suppression'])
                                            <form action="{{ route('documents.global.destroy', $doc->id_document) }}" method="POST"
                                                class="inline" onsubmit="return confirm('Supprimer définitivement ce document ?');">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-800 text-xs font-bold bg-red-50 px-2 py-1 rounded border border-red-100">
                                                    🗑️
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </li>
                            @empty
                                <li class="text-sm text-slate-400 italic text-center py-4">Aucun document ou plan rattaché à cet
                                    espace public.</li>
                            @endforelse
                        </ul>
                    </div>

                    @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                        <form action="{{ route('lieux.documents.store', $lieu->id_lieu) }}" method="POST"
                            enctype="multipart/form-data"
                            class="bg-slate-50 p-4 rounded-lg border border-slate-200 border-dashed mt-auto">
                            @csrf

                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Ajouter une pièce
                                jointe</label>
                            <p class="text-[10px] text-slate-500 mb-3">Formats acceptés : PDF, JPG, PNG, DOC, DOCX. (Max : 5 Mo)
                            </p>

                            <div class="flex items-start gap-2">
                                <div class="w-full">
                                    <input type="file" name="fichier" required
                                        class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:font-semibold file:bg-slate-900 file:text-white hover:file:bg-slate-800 cursor-pointer focus:outline-none">

                                    @error('fichier')
                                        <p class="text-xs text-red-600 font-bold mt-2">⚠️ {{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit"
                                    class="px-3 py-1.5 bg-indigo-600 text-white font-bold rounded-md hover:bg-indigo-700 transition text-xs whitespace-nowrap">
                                    📤 Envoyer
                                </button>
                            </div>
                        </form>
                    @endcan
                </div>

            </div>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection

@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var mapElement = document.getElementById('map');
            if (!mapElement) return;

            var map = L.map('map').setView([45.928, 6.223], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            setTimeout(function () {
                map.invalidateSize();
            }, 200);

            // Récupération des données GeoJSON
            var geojsonParcellesStr = mapElement.getAttribute('data-parcelles');
            var geojsonLieuStr = mapElement.getAttribute('data-lieu');

            var bounds = L.latLngBounds(); // Utilisé pour regrouper toutes les emprises
            var hasValidBounds = false;
            var pointTarget = null;

            // 1. Dessiner TOUTES les Parcelles (Polygones verts)
            if (geojsonParcellesStr && geojsonParcellesStr.trim() !== '') {
                try {
                    var parcellesData = JSON.parse(geojsonParcellesStr);

                    // On boucle sur chaque GeoJSON de parcelle
                    parcellesData.forEach(function (pStr) {
                        if (pStr) {
                            var parsedData = JSON.parse(pStr);
                            var parcelleLayer = L.geoJSON(parsedData, {
                                style: {
                                    color: "#10b981",
                                    weight: 2,
                                    fillColor: "#10b981",
                                    fillOpacity: 0.1
                                }
                            }).addTo(map);

                            if (parcelleLayer.getBounds && parcelleLayer.getBounds().isValid()) {
                                bounds.extend(parcelleLayer.getBounds());
                                hasValidBounds = true;
                            }
                        }
                    });
                } catch (e) {
                    console.error("Erreur de rendu des parcelles :", e);
                }
            }

            // 2. Dessiner le Lieu Public (Marqueur GPS)
            if (geojsonLieuStr && geojsonLieuStr.trim() !== '') {
                try {
                    var lieuData = JSON.parse(geojsonLieuStr);
                    var markerLayer = L.geoJSON(lieuData, {
                        pointToLayer: function (feature, latlng) {
                            pointTarget = latlng;
                            return L.marker(latlng).bindPopup('<b>{{ addslashes($lieu->nom_lieu) }}</b>');
                        }
                    }).addTo(map);
                } catch (e) {
                    console.error("Erreur de rendu du marqueur :", e);
                }
            }

            // 3. Centrage dynamique de la caméra
            if (pointTarget) {
                // Priorité au marqueur s'il existe
                map.setView(pointTarget, 18);
            } else if (hasValidBounds) {
                // Sinon, on englobe toutes les parcelles
                map.fitBounds(bounds, {
                    padding: [30, 30]
                });
            }
        });
    </script>
@endsection