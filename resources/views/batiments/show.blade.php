@extends('layouts.app')

@section('header_title', 'Fiche Patrimoine - ' . $batiment->nom_bat)

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

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
                <p class="text-sm text-slate-500 mt-1.5 flex items-center gap-1.5">
                    📍 Adressage : <span class="text-slate-700 font-medium">
                        @if($batiment->nom_voie)
                            {{ $batiment->num_rue }} {{ $batiment->nom_voie }}, {{ $batiment->code_postal }}
                            {{ $batiment->ville }}
                        @else
                            Adresse non renseignée
                        @endif
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">

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
                                        @if($loc->libelle_usage) | Usage : {{ $loc->libelle_usage }} @endif
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

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200">
                        <h3 class="text-sm font-bold text-slate-800 tracking-tight flex items-center gap-2">
                            📋 Calendrier des Contrôles Réglementaires Obligatoires
                        </h3>
                    </div>
                    <div class="p-4">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="text-slate-400 font-bold border-b border-slate-100 pb-2">
                                    <th class="pb-2">Désignation du contrôle</th>
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
            </div>

            <div class="space-y-6">

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-red-50/50 border-b border-red-100">
                        <h3 class="text-sm font-bold text-red-800 tracking-tight flex items-center gap-2">
                            🚨 Signalements Actifs ({{ $signalements->count() }})
                        </h3>
                    </div>
                    <div class="p-4 space-y-3 max-h-52 overflow-y-auto">
                        @forelse($signalements as $sig)
                            <div class="p-2.5 bg-slate-50 border border-slate-150 rounded-lg text-xs">
                                <div class="flex justify-between font-semibold text-slate-800">
                                    <span>⚠️ {{ $sig->statut_signalement }}</span>
                                    <span class="text-red-600">{{ $sig->priorite ?? 'Normale' }}</span>
                                </div>
                                <p class="text-slate-500 mt-1 truncate">{{ $sig->description }}</p>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 italic text-center py-2">Parfait ! Aucun signalement en attente sur
                                ce lieu.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200">
                        <h3 class="text-sm font-bold text-slate-800 tracking-tight flex items-center gap-2">
                            🛠️ Historique des Interventions
                        </h3>
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