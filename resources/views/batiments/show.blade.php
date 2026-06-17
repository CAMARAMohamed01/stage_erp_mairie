@extends('layouts.app')

@section('header_title', 'Fiche Patrimoine - ' . $batiment->nom_bat)

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection

@section('content')
    <div class="max-w-7xl mx-auto space-y-6 pb-12">

        <div
            class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-wrap justify-between items-center gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <span class="text-3xl">🏢</span>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $batiment->nom_bat }}</h1>
                    <span
                        class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-100">
                        ERP : Categorie {{ $batiment->categorie_erp ?? 'N/A' }}
                    </span>
                </div>
                <p class="text-sm text-slate-500 mt-1.5 flex items-center gap-3">
                    <span>
                        📍 Adressage : <strong class="text-slate-700">
                            @if($batiment->nom_voie)
                                {{ $batiment->num_rue }} {{ $batiment->nom_voie }}, {{ $batiment->code_postal }}
                                {{ $batiment->ville }}
                            @else
                                <span class="italic text-slate-400">Adresse non renseignée</span>
                            @endif
                        </strong>
                    </span>
                    <span class="text-slate-300">|</span>
                    <span>
                        🗺️ Parcelle : <strong class="text-slate-700">
                            @if($batiment->num_parcelle)
                                Section {{ $batiment->section_cadastrale }} - N° {{ $batiment->num_parcelle }}
                            @else
                                <span class="italic text-slate-400">Non rattaché</span>
                            @endif
                        </strong>
                    </span>
                </p>
            </div>
            <div class="flex gap-2">
                <button onclick="history.back()"
                    class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 transition">
                    ← Retour
                </button>
                @can('check-permission', ['Patrimoine & Equipements', 'suppression'])
                    <form action="{{ route('batiments.destroy', $batiment->id_batiment) }}" method="POST"
                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce bâtiment ? Cette action est irréversible.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-50 border border-red-200 text-red-600 text-sm font-semibold rounded-lg hover:bg-red-100 transition">
                            🗑️ Supprimer
                        </button>
                    </form>
                @endcan
                @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                    <a href="{{ route('batiments.edit', $batiment->id_batiment) }}"
                        class="px-4 py-2 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition">
                        Modifier le bâtiment
                    </a>
                @endcan
            </div>
        </div>
        {{-- AFFICHE LES ALERTES DE SUCCÈS OU D'ERREUR ICI --}}
        @if(session('success'))
            <div class="p-4 mb-6 bg-green-50 text-green-700 rounded-lg border border-green-200 text-sm font-semibold">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 mb-6 bg-red-50 text-red-700 rounded-lg border border-red-200 text-sm font-semibold">
                {{ session('error') }}
            </div>
        @endif
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200">
                        <h3 class="text-sm font-bold text-slate-800 tracking-tight flex items-center gap-2">
                            🌍 Localisation & Cadastre
                        </h3>
                    </div>
                    <div id="map" data-batiment="{{ $batiment->geojson_batiment ?? '' }}"
                        data-parcelle="{{ $batiment->geojson_parcelle ?? '' }}"
                        style="height: 350px; width: 100%; z-index: 1;" class="relative">
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-slate-800 tracking-tight flex items-center gap-2">
                            ⚡ Réseaux & Compteurs Généraux ({{ $compteurs_generaux->count() }})
                        </h3>
                        @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                            <a href="{{ route('compteurs.create') }}"
                                class="text-xs text-blue-600 font-semibold hover:underline">➕ Ajouter</a>
                        @endcan
                    </div>
                    <div class="divide-y divide-slate-100 max-h-48 overflow-y-auto">
                        @forelse($compteurs_generaux as $compteur)
                            <div class="p-3.5 flex justify-between items-center hover:bg-slate-50 transition">
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
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-blue-100 text-blue-700">TGBT /
                                        Général</span>
                                    <a href="{{ route('compteurs.show', $compteur->id_compteur) }}"
                                        class="text-xs text-blue-600 font-semibold hover:underline">Voir →</a>
                                </div>
                            </div>
                        @empty
                            <p class="p-4 text-xs text-slate-400 italic text-center">Aucun compteur d'arrivée générale déclaré
                                pour ce bâtiment.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-slate-800 tracking-tight flex items-center gap-2">
                            🚪 Locaux & Salles Intérieures ({{ $locaux->count() }})
                        </h3>
                        @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                            <a href="{{ route('locaux.create', ['id_batiment' => $batiment->id_batiment]) }}"
                                class="text-xs text-blue-600 font-semibold hover:underline">
                                ➕ Ajouter une pièce
                            </a>
                        @endcan
                    </div>
                    <div class="divide-y divide-slate-100 max-h-60 overflow-y-auto">
                        @forelse($locaux as $loc)
                            <div class="p-3.5 flex justify-between items-center hover:bg-slate-50 transition">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">{{ $loc->nom_local }}</p>
                                    <p class="text-xs text-slate-400">
                                        Niveau : {{ $loc->niveau ?? 'RDC' }}
                                        @if($loc->surface_m2) | {{ $loc->surface_m2 }} m² @endif
                                    </p>
                                </div>
                                <a href="{{ route('locaux.show', $loc->id_local) }}"
                                    class="text-xs text-blue-600 font-semibold hover:underline">
                                    Consulter la pièce →
                                </a>
                            </div>
                        @empty
                            <p class="p-4 text-xs text-slate-400 italic text-center">Aucune pièce ou local enregistré pour ce
                                bâtiment.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-slate-800 tracking-tight flex items-center gap-2">
                            ⚙️ Équipements Globaux & Matériels ({{ $equipements->count() }})
                        </h3>
                        @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                            <a href="{{ route('equipements.create', ['id_batiment' => $batiment->id_batiment]) }}"
                                class="text-xs text-blue-600 font-semibold hover:underline">
                                ➕ Ajouter un équipement
                            </a>
                        @endcan
                    </div>
                    <div class="divide-y divide-slate-100 max-h-60 overflow-y-auto">
                        @forelse($equipements as $equip)
                            <div class="p-3.5 flex justify-between items-center hover:bg-slate-50 transition">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">{{ $equip->nom_equipement }}</p>
                                    <p class="text-xs text-slate-400">Réf : {{ $equip->reference_serie ?? 'N/A' }} | État :
                                        {{ $equip->etat_fonctionnement ?? 'Opérationnel' }}
                                    </p>
                                </div>
                                <a href="{{ route('equipements.show', $equip->id_equipement) }}"
                                    class="text-xs text-blue-600 font-semibold hover:underline">
                                    Voir la fiche →
                                </a>
                            </div>
                        @empty
                            <p class="p-4 text-xs text-slate-400 italic text-center">Aucun équipement global inventorié dans ce
                                bâtiment.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="space-y-6">

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200">
                        <h3 class="text-sm font-bold text-slate-800 tracking-tight flex items-center gap-2">
                            📜 Contrats d'Exploitation / Maintenance ({{ $contrats->count() }})
                        </h3>
                    </div>
                    <div class="divide-y divide-slate-100 max-h-48 overflow-y-auto">
                        @forelse($contrats as $contrat)
                            <div class="p-3.5 flex justify-between items-center hover:bg-slate-50 transition">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">N° {{ $contrat->numero_contrat }}</p>
                                    <p class="text-xs text-slate-500">{{ $contrat->type_contrat }} |
                                        {{ $contrat->raison_sociale ?? ($contrat->nom_tiers . ' ' . $contrat->prenom_tiers) }}
                                    </p>
                                </div>
                                <a href="{{ route('contrats.show', $contrat->id_contrat) }}"
                                    class="text-xs text-blue-600 font-semibold hover:underline">Consulter →</a>
                            </div>
                        @empty
                            <p class="p-4 text-xs text-slate-400 italic text-center">Aucun contrat lié à ce bâtiment.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200">
                        <h3 class="text-sm font-bold text-slate-800 tracking-tight flex items-center gap-2">
                            📋 Contrôles Réglementaires Obligatoires
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
                                            réglementaire requis ou enregistré.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">📂 Documents
                            Techniques & Photos</h2>
                        <ul class="space-y-3 mb-6 max-h-48 overflow-y-auto">
                            @forelse($documents as $doc)
                                <li
                                    class="flex items-center justify-between p-3 bg-slate-50 border border-slate-100 rounded-lg transition hover:bg-slate-100">
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="text-2xl">{{ in_array(strtolower($doc->type_doc), ['pdf']) ? '📄' : (in_array(strtolower($doc->type_doc), ['jpg', 'png', 'jpeg']) ? '🖼️' : '📁') }}</span>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800 line-clamp-1"
                                                title="{{ $doc->nom_fichier }}">{{ $doc->nom_fichier }}</p>
                                            <p class="text-xs text-slate-500">
                                                {{ \Carbon\Carbon::parse($doc->date_upload)->format('d/m/Y') }} •
                                                {{ number_format($doc->taille_ko, 0, ',', ' ') }} Ko
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ asset('storage/' . $doc->chemin_stockage) }}" target="_blank"
                                            class="text-blue-600 hover:text-blue-800 text-xs font-bold bg-blue-50 px-2 py-1 rounded border border-blue-100">Voir</a>
                                        @can('check-permission', ['Patrimoine & Equipements', 'suppression'])
                                            <form action="{{ route('documents.global.destroy', $doc->id_document) }}" method="POST"
                                                class="inline" onsubmit="return confirm('Supprimer ce document ?');">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-800 text-xs font-bold bg-red-50 px-2 py-1 rounded border border-red-100">❌</button>
                                            </form>
                                        @endcan
                                    </div>
                                </li>
                            @empty
                                <li class="text-sm text-slate-400 italic text-center py-4">Aucun document rattaché.</li>
                            @endforelse
                        </ul>
                    </div>

                    @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                        <form action="{{ route('batiments.documents', $batiment->id_batiment) }}" method="POST"
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
                                    @error('fichier')<p class="text-xs text-red-600 font-bold mt-2">⚠️ {{ $message }}</p>
                                    @enderror
                                </div>
                                <button type="submit"
                                    class="px-3 py-1.5 bg-indigo-600 text-white font-bold rounded-md hover:bg-indigo-700 transition text-xs whitespace-nowrap">📤
                                    Envoyer</button>
                            </div>
                        </form>
                    @endcan
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-red-50/50 border-b border-red-100">
                        <h3 class="text-sm font-bold text-red-800 tracking-tight flex items-center gap-2">🚨 Actions
                            Actives ({{ $actions->count() }})</h3>
                    </div>
                    <div class="p-4 space-y-3 max-h-52 overflow-y-auto">
                        @forelse($actions as $sig)
                            {{-- On transforme la div en lien cliquable --}}
                            <a href="{{ route('actions.show', $sig->id_action) }}"
                                class="block p-2.5 bg-slate-50 border border-slate-150 rounded-lg text-xs hover:bg-red-50 hover:border-red-200 transition group cursor-pointer shadow-sm">
                                <div
                                    class="flex justify-between font-semibold text-slate-800 group-hover:text-red-800 transition">
                                    <span>⚠️ {{ $sig->statut_action }}</span>
                                    <span class="text-red-600">{{ $sig->priorite ?? 'Normale' }}</span>
                                </div>
                                <p class="text-slate-500 mt-1 truncate group-hover:text-red-600 transition">
                                    {{ $sig->description }}
                                </p>
                            </a>
                        @empty
                            <p class="text-xs text-slate-400 italic text-center py-2">Parfait ! Aucune action en attente sur
                                ce lieu.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200">
                        <h3 class="text-sm font-bold text-slate-800 tracking-tight flex items-center gap-2">🛠️ Historique
                            des Interventions</h3>
                    </div>
                    <div class="divide-y divide-slate-100 text-xs">
                        @forelse($interventions as $int)
                            <div class="p-3 hover:bg-slate-50 transition">
                                <div class="flex justify-between items-center">
                                    <span
                                        class="font-semibold text-slate-800 truncate max-w-[150px]">{{ $int->type_intervention }}</span>
                                    <span
                                        class="px-2 py-0.5 rounded text-[10px] font-bold {{ $int->statut_global === 'Terminé' ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700' }}">
                                        {{ $int->statut_global }}
                                    </span>
                                </div>
                                <p class="text-slate-400 mt-0.5">
                                    {{ \Carbon\Carbon::parse($int->date_ouverture)->format('d/m/Y') }}
                                </p>
                            </div>
                        @empty
                            <p class="p-4 text-slate-400 italic text-center">Aucune intervention historique sur ce bâtiment.</p>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var mapElement = document.getElementById('map');
            if (!mapElement) return;

            var geojsonParcelleStr = mapElement.getAttribute('data-parcelle');
            var geojsonBatimentStr = mapElement.getAttribute('data-batiment');

            // Initialisation basique centrée sur Dingy-Saint-Clair par défaut
            var map = L.map('map').setView([45.928, 6.223], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(map);

            var layerBounds = [];

            // 1. Dessiner la parcelle (Polygone)
            if (geojsonParcelleStr && geojsonParcelleStr.trim() !== '') {
                try {
                    var parcelleData = JSON.parse(geojsonParcelleStr);
                    var parcelleLayer = L.geoJSON(parcelleData, {
                        style: {
                            color: "#8b5cf6",
                            weight: 2,
                            fillColor: "#8b5cf6",
                            fillOpacity: 0.15
                        } // Violet clair
                    }).addTo(map);

                    if (parcelleLayer.getBounds && parcelleLayer.getBounds().isValid()) {
                        layerBounds.push(parcelleLayer.getBounds());
                    }
                } catch (e) {
                    console.error("Erreur lecture parcelle", e);
                }
            }

            // 2. Dessiner le bâtiment (Marqueur)
            if (geojsonBatimentStr && geojsonBatimentStr.trim() !== '') {
                try {
                    var batimentData = JSON.parse(geojsonBatimentStr);
                    var batimentLayer = L.geoJSON(batimentData, {
                        pointToLayer: function (feature, latlng) {
                            // Utilisation d'un marqueur personnalisé (icône bleue)
                            return L.marker(latlng).bindPopup(
                                '<b>{{ addslashes($batiment->nom_bat) }}</b>');
                        }
                    }).addTo(map);

                    // Si on a un point de bâtiment, on centre dessus de façon prioritaire
                    if (batimentLayer.getBounds && batimentLayer.getBounds().isValid()) {
                        map.setView(batimentLayer.getBounds().getCenter(), 18);
                    } else if (layerBounds.length > 0) {
                        map.fitBounds(layerBounds[0], {
                            padding: [30, 30]
                        });
                    }
                } catch (e) {
                    console.error("Erreur lecture bâtiment", e);
                }
            } else if (layerBounds.length > 0) {
                // S'il n'y a pas de point de bâtiment mais qu'il y a une parcelle, on centre sur la parcelle
                map.fitBounds(layerBounds[0], {
                    padding: [30, 30]
                });
            }
        });
    </script>
@endsection