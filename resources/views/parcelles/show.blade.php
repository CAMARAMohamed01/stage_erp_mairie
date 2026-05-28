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
            <div class="flex items-center gap-2">
                <a href="{{ route('parcelles.edit', $parcelle->id_parcelle) }}"
                    class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 px-4 rounded-lg shadow-sm">✏️
                    Modifier</a>

                @can('check-permission', ['Patrimoine & Equipements', 'suppression'])
                    <form action="{{ route('parcelles.destroy', $parcelle->id_parcelle) }}" method="POST"
                        onsubmit="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer définitivement cette parcelle cadastrale ainsi que ses liaisons de propriété ? Cette action est irréversible.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow-sm">
                            🗑️ Supprimer
                        </button>
                    </form>
                @endcan
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="col-span-1 space-y-6">

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2">📋 Informations
                        Cadastrales</h3>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Parcelle :</dt>
                            <dd class="font-bold text-slate-900">{{ $parcelle->section_cadastrale }} -
                                {{ $parcelle->num_parcelle }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Lieu-dit :</dt>
                            <dd class="text-slate-900 font-medium">{{ $parcelle->lieuDit->nom_lieu_dit ?? 'Non défini' }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Surface :</dt>
                            <dd class="text-slate-900 font-medium">{{ $parcelle->surface_cadastrale ?? 'N/A' }} m²</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Type :</dt>
                            <dd class="text-slate-900 font-medium">{{ $parcelle->type_parcelle ?? 'Non spécifié' }}</dd>
                        </div>
                        <div class="flex justify-between border-t pt-2 mt-2">
                            <dt class="text-slate-500">N° Inventaire Immo :</dt>
                            <dd class="text-blue-600 font-mono font-bold text-xs">
                                {{ $parcelle->immobilisation->num_inventaire ?? 'Non inventorié' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Propriétaires (Table pivot proprio_parcelle) -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h3
                        class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-3 border-b pb-2 flex justify-between items-center">
                        <span>👥 Propriété Foncière</span>
                        <span
                            class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full text-xs">{{ $proprietaires->count() }}</span>
                    </h3>

                    <!-- Liste des propriétaires actuels -->
                    <div class="space-y-2 mb-4">
                        @forelse($proprietaires as $proprio)
                            <div class="p-2.5 bg-slate-50 border rounded-lg text-xs space-y-1 relative group">
                                <div class="flex justify-between items-start pr-6">
                                    <span class="font-bold text-slate-800">
                                        @if($proprio->type_tiers === 'Physique')
                                            👤 {{ $proprio->prenom_tiers }} {{ $proprio->nom_tiers }}
                                        @else
                                            🏢 {{ $proprio->raison_sociale }}
                                        @endif
                                    </span>

                                    @can('check-permission', ['Patrimoine & Equipements', 'suppression'])
                                        <form
                                            action="{{ route('parcelles.proprietaires.destroy', [$parcelle->id_parcelle, $proprio->id_tiers]) }}"
                                            method="POST" onsubmit="return confirm('Dissocier ce propriétaire de la parcelle ?');"
                                            class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-sm"
                                                title="Retirer">×</button>
                                        </form>
                                    @endcan
                                </div>
                                <div class="flex justify-between text-slate-500">
                                    <span>Part : <strong>{{ $proprio->pourcentage_part }}%</strong></span>
                                    <span>Acquis le :
                                        {{ \Carbon\Carbon::parse($proprio->date_acquisition)->format('d/m/Y') }}</span>
                                </div>
                                @if($proprio->prix_parcelle)
                                    <div class="text-[10px] text-slate-400">Montant transaction :
                                        {{ number_format($proprio->prix_parcelle, 2, ',', ' ') }} €
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 italic text-center py-2">Aucun propriétaire enregistré.</p>
                        @endforelse
                    </div>

                    <!-- Formulaire d'ajout rapide (visible si droit d'écriture) -->
                    @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                        <form action="{{ route('parcelles.proprietaires.store', $parcelle->id_parcelle) }}" method="POST"
                            class="bg-slate-50 p-3 rounded-lg border border-slate-200 space-y-2.5">
                            @csrf
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">➕ Associer un propriétaire
                            </p>

                            <div>
                                <select name="id_tiers" required
                                    class="w-full text-xs border border-slate-300 rounded p-1.5 bg-white">
                                    <option value="">-- Choisir un tiers --</option>
                                    @foreach($tousLesTiers as $tiers)
                                        <option value="{{ $tiers->id_tiers }}">
                                            [{{ $tiers->type_tiers }}]
                                            {{ $tiers->type_tiers === 'Physique' ? $tiers->nom_tiers . ' ' . $tiers->prenom_tiers : $tiers->raison_sociale }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Part (%)</label>
                                    <input type="number" name="pourcentage_part" min="0" max="100" step="0.01" value="100"
                                        required class="w-full text-xs border border-slate-300 rounded p-1">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Date d'achat</label>
                                    <input type="date" name="date_acquisition" value="{{ date('Y-m-d') }}" required
                                        class="w-full text-xs border border-slate-300 rounded p-1">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Prix d'acquisition (€)</label>
                                <input type="number" name="prix_parcelle" step="0.01" placeholder="Optionnel"
                                    class="w-full text-xs border border-slate-300 rounded p-1">
                            </div>

                            <button type="submit"
                                class="w-full bg-violet-600 hover:bg-violet-700 text-white font-bold text-xs py-1.5 rounded transition shadow-sm">
                                Enregistrer la liaison
                            </button>
                        </form>
                    @endcan
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-3 border-b pb-2">🏛️ Éléments
                        Rattachés</h3>
                    <div class="space-y-2 text-xs">
                        <div>
                            <span class="font-semibold text-slate-500 block mb-1">Bâtiments
                                ({{ $parcelle->batiments->count() }}) :</span>
                            @foreach($parcelle->batiments as $bat)
                                <a href="{{ route('batiments.show', $bat->id_batiment) }}"
                                    class="block p-1.5 hover:bg-blue-50 text-blue-600 rounded font-medium">🏢
                                    {{ $bat->nom_bat }}</a>
                            @endforeach
                        </div>
                        <div class="pt-2 border-t">
                            <span class="font-semibold text-slate-500 block mb-1">Espaces publics / Lieux :</span>
                            @foreach($parcelle->lieuxPublics as $lieu)
                                <span class="block p-1.5 bg-slate-50 text-slate-700 rounded mb-1">🌳
                                    {{ $lieu->nom_lieu }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-span-1 lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden" style="height: 450px;">
                    <div id="map" data-geojson="{{ $parcelle->geojson ?? '' }}" style="height: 100%; width: 100%;"
                        class="z-0 relative"></div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2">🏗️ Historique
                        des dossiers d'Urbanisme</h3>
                    @if(count($dossiersUrba) > 0)
                        <div class="overflow-x-auto text-xs">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="text-slate-400 font-bold border-b">
                                        <th class="pb-2">N° Dossier</th>
                                        <th class="pb-2">Type</th>
                                        <th class="pb-2">Date Décision</th>
                                        <th class="pb-2 text-right">Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dossiersUrba as $dossier)
                                        <tr class="border-b last:border-0 hover:bg-slate-50">
                                            <td class="py-2.5 font-mono font-bold text-slate-700">{{ $dossier->numero_dossier }}
                                            </td>
                                            <td class="py-2.5 text-slate-600">{{ $dossier->type_dossier_CU_DP_ }}</td>
                                            <td class="py-2.5 text-slate-500">
                                                {{ \Carbon\Carbon::parse($dossier->date_decision)->format('d/m/Y') }}
                                            </td>
                                            <td class="py-2.5 text-right font-semibold text-green-600">
                                                {{ $dossier->nature_decision }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-slate-400 italic text-center py-4">Aucun dossier d'urbanisme (permis de
                            construire, aménagement) lié à cette parcelle.</p>
                    @endif
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