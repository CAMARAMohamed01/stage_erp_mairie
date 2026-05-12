@extends('layouts.app')

@section('title', 'Tableau de bord - Technique')
@section('header_title', 'Vue d\'ensemble opérationnelle')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <div class="text-slate-500 text-sm font-medium mb-1">Signalements en attente</div>
            <div class="text-3xl font-bold text-slate-800">{{ count($nouveauxSignalements) }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <div class="text-slate-500 text-sm font-medium mb-1">Chantiers en cours</div>
            <div class="text-3xl font-bold text-blue-600">{{ count($interventionsEnCours) }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <div class="text-slate-500 text-sm font-medium mb-1">Équipements HS</div>
            <div class="text-3xl font-bold text-red-600">{{ count($equipementsEnPanne) }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <div class="text-slate-500 text-sm font-medium mb-1">Contrôles à venir</div>
            <div class="text-3xl font-bold text-orange-500">{{ count($controles) }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="bg-red-50 px-6 py-4 border-b border-red-100 flex justify-between items-center">
                <h2 class="font-semibold text-red-800 text-lg">⚠️ Équipements en panne</h2>
            </div>
            <div class="p-0">
                <table class="w-full text-left text-sm">
                    <tbody class="divide-y divide-slate-100">
                        @forelse($equipementsEnPanne as $equipement)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-3 font-medium text-slate-800">{{ $equipement->nom_equipement }}</td>
                                <td class="px-6 py-3 text-slate-500">{{ $equipement->marque ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-slate-500 text-center">Aucune panne signalée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="bg-orange-50 px-6 py-4 border-b border-orange-100 flex justify-between items-center">
                <h2 class="font-semibold text-orange-800 text-lg">📋 Échéances Réglementaires</h2>
            </div>
            <div class="p-0">
                <table class="w-full text-left text-sm">
                    <tbody class="divide-y divide-slate-100">
                        @forelse($controles as $controle)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-3 font-medium text-slate-800">{{ $controle->designation }}</td>
                                <td class="px-6 py-3 text-slate-500 text-right">{{ $controle->frequence_mois }} mois</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-slate-500 text-center">Tout est à jour.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex justify-between items-center">
                <h2 class="font-semibold text-slate-800 text-lg">🚨 Derniers Signalements</h2>

                <a href="{{ route('signalements.create') }}"
                    class="text-xs bg-blue-600 text-white px-3 py-1.5 rounded-lg font-bold hover:bg-blue-700 transition">
                    + Créer
                </a>
            </div>
            <div class="p-0">
                <ul class="divide-y divide-slate-100 text-sm">
                    @forelse($nouveauxSignalements as $signalement)
                        <li class="hover:bg-slate-50 transition-colors">
                            <a href="{{ route('signalement.show', $signalement->id_sig) }}" class="block px-6 py-3">
                                <div class="font-medium text-slate-800">{{ $signalement->description }}</div>
                                <div class="text-slate-500 mt-1 flex justify-between items-center">
                                    <span>{{ $signalement->categorie->libelle ?? 'Non catégorisé' }}</span>
                                    <x-badge type="priorite" :value="$signalement->priorite" />
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="px-6 py-4 text-slate-500 text-center">Aucun nouveau signalement.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="bg-blue-50 px-6 py-4 border-b border-blue-100 flex justify-between items-center">
                <h2 class="font-semibold text-blue-800 text-lg">🚧 Interventions en cours</h2>
            </div>
            <div class="p-0">
                <ul class="divide-y divide-slate-100 text-sm">
                    @foreach($interventionsEnCours as $int)
                        <li class="hover:bg-slate-50 transition-colors">
                            <a href="{{ route('interventions.show', $int->id_int) }}" class="block px-6 py-3">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-slate-800">{{ $int->type_intervention }}</span>
                                    <span
                                        class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($int->date_ouverture)->format('d/m') }}</span>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

    </div>
@endsection