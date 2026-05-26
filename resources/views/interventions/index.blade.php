@extends('layouts.app')

@section('title', 'Gestion des Interventions')

@section('content')
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex justify-between items-center">
            <h1 class="text-xl font-bold text-slate-800">🛠️ Registre des Interventions</h1>
            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">
                {{ $interventions->count() }} au total
            </span>
        </div>

        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4">

            <form action="{{ route('interventions.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="N° Réf ou mot-clé..."
                    class="text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2 bg-white w-full md:w-48 shadow-sm">

                <select name="statut" id="statut" onchange="this.form.submit()"
                    class="text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2 bg-white shadow-sm">
                    <option value="Tous" {{ request('statut') == 'Tous' ? 'selected' : '' }}>Tous les statuts</option>
                    <option value="En cours" {{ request('statut') == 'En cours' ? 'selected' : '' }}>🚧 En cours</option>
                    <option value="Terminé" {{ request('statut') == 'Terminé' ? 'selected' : '' }}>✅ Terminé</option>
                    <option value="En attente" {{ request('statut') == 'En attente' ? 'selected' : '' }}>⏳ En attente
                    </option>
                </select>

                <button type="submit"
                    class="px-3 py-2 bg-slate-800 text-white rounded-lg text-sm font-medium hover:bg-slate-700 transition">Chercher</button>

                @if((request('statut') && request('statut') !== 'Tous') || request('search'))
                    <a href="{{ route('interventions.index') }}"
                        class="text-xs text-red-600 hover:underline font-medium">Réinitialiser</a>
                @endif
            </form>

            <div class="flex gap-2">
                <a href="{{ route('interventions.create') }}"
                    class="px-3 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 transition shadow-sm flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nouvelle intervention
                </a>

                <a href="{{ route('interventions.excel') }}"
                    class="px-3 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-100 transition flex items-center shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M7 10l5 5m0 0l5-5m-5 5V3"></path>
                    </svg>
                    Exporter
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold">Réf</th>
                        <th class="px-6 py-4 font-semibold">Type / Description</th>
                        <th class="px-6 py-4 font-semibold">Date Ouverture</th>
                        <th class="px-6 py-4 font-semibold">Statut</th>
                        <th class="px-6 py-4 font-semibold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($interventions as $intervention)
                        <tr class="hover:bg-slate-50 transition-colors text-sm">
                            <td class="px-6 py-4 font-bold text-slate-700">#{{ $intervention->id_int }}</td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-900">{{ $intervention->type_intervention }}</div>
                                <div class="text-slate-500 text-xs italic">{{ Str::limit($intervention->description, 50) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ \Carbon\Carbon::parse($intervention->date_ouverture)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <x-badge type="statut" :value="$intervention->statut_global" />
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('interventions.show', $intervention->id_int) }}"
                                    class="text-blue-600 hover:text-blue-800 font-medium">Détails</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500 italic">Aucune intervention ne
                                correspond à votre recherche.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection