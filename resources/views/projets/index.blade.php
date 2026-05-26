@extends('layouts.app')

@section('title', 'Gestion des Projets')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        <div class="flex justify-between items-center bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">🏗️ Projets Communaux</h1>
                <p class="text-slate-500 text-sm">Vue d'ensemble des projets, mandats et maintenances</p>
            </div>

            @if(auth()->user()->can('check-permission', ['Patrimoine & Travaux', 'ecriture']))
                <a href="{{ route('projets.create') }}"
                    class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                    + Nouveau Projet
                </a>
            @endif
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                <form action="{{ route('projets.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un projet..."
                        class="text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2 bg-white w-full md:w-64 shadow-sm">

                    <select name="type"
                        class="text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2 bg-white shadow-sm">
                        <option value="">Tous les types</option>
                        <option value="Mandat" {{ request('type') == 'Mandat' ? 'selected' : '' }}>Mandat</option>
                        <option value="Petit projet" {{ request('type') == 'Petit projet' ? 'selected' : '' }}>Petit projet
                        </option>
                        <option value="Maintenance" {{ request('type') == 'Maintenance' ? 'selected' : '' }}>Maintenance
                        </option>
                        <option value="Étude" {{ request('type') == 'Étude' ? 'selected' : '' }}>Étude</option>
                    </select>

                    <button type="submit"
                        class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-medium hover:bg-slate-700 transition">Filtrer</button>

                    @if(request('search') || request('type'))
                        <a href="{{ route('projets.index') }}"
                            class="text-xs text-red-600 hover:underline font-medium">Réinitialiser</a>
                    @endif
                </form>
            </div>

            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase">Projet</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase">Type</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase">Mandat</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase">Budget</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase">Responsable</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($projets as $projet)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="p-4 text-sm font-semibold text-slate-800">{{ $projet->nom_projet }}</td>
                                    <td class="p-4 text-sm">
                                        <span
                                            class="px-2 py-1 text-[10px] font-bold rounded-full 
                                                    {{ $projet->type_projet == 'Mandat' ? 'bg-purple-100 text-purple-700' :
                        ($projet->type_projet == 'Maintenance' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700') }}">
                                            {{ $projet->type_projet }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-sm text-slate-600 font-mono">{{ $projet->annee_mandat }}</td>
                                    <td class="p-4 text-sm font-medium text-slate-700">
                                        {{ $projet->budget_global_alloue ? number_format($projet->budget_global_alloue, 2, ',', ' ') . ' €' : '-' }}
                                    </td>
                                    <td class="p-4 text-sm text-slate-600">
                                        {{ $projet->chefProjet ? $projet->chefProjet->prenom_user . ' ' . $projet->chefProjet->nom_user : 'Non assigné' }}
                                    </td>
                                    <td class="p-4 text-right space-x-2">
                                        <a href="{{ route('projets.show', $projet->id_projet) }}"
                                            class="text-blue-600 hover:text-blue-800 font-bold text-xs">Détails</a>
                                    </td>
                                </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400 italic">Aucun projet trouvé pour cette
                                recherche.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection