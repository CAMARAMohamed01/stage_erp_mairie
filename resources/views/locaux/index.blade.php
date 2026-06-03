@extends('layouts.app')

@section('header_title', 'Gestion des Locaux et Pièces')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        <div class="flex flex-wrap justify-between items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Locaux de la Commune</h1>
                <p class="text-sm text-slate-500 mt-1">Inventaire des pièces, bureaux, salles de classe et locaux
                    techniques.</p>
            </div>
            <div>
                @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                    <a href="{{ route('locaux.create') }}"
                        class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition flex items-center gap-2">
                        ➕ Ajouter un local
                    </a>
                @endcan
            </div>
        </div>
        <form action="{{ route('locaux.index') }}" method="GET" class="flex gap-2 mb-6">
            <div class="relative flex-grow max-w-md">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Rechercher par local, bâtiment..."
                    class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
            <button type="submit"
                class="px-4 py-2 bg-slate-800 text-white font-semibold rounded-lg hover:bg-slate-700 transition">
                Rechercher
            </button>

            @if(request()->filled('search'))
                <a href="{{ route('locaux.index') }}"
                    class="px-4 py-2 bg-slate-100 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition">
                    Réinitialiser
                </a>
            @endif
        </form>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="p-4">Désignation</th>
                        <th class="p-4">Bâtiment Rattaché</th>
                        <th class="p-4 text-center">Niveau / Étage</th>
                        <th class="p-4 text-center">Surface (m²)</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($locaux as $local)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="p-4 font-semibold text-slate-800">
                                🚪 {{ $local->nom_local }}
                            </td>
                            <td class="p-4 text-slate-600 font-medium">
                                {{ $local->nom_bat ?? 'Aucun bâtiment' }}
                            </td>
                            <td class="p-4 text-center">
                                @if($local->niveau)
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $local->niveau }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="p-4 text-center text-slate-600 font-mono text-xs">
                                {{ $local->surface_m2 ? $local->surface_m2 . ' m²' : '—' }}
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('locaux.show', $local->id_local) }}"
                                    class="inline-flex items-center px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-md transition shadow-sm">
                                    Détails →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400 italic bg-slate-50/50">
                                Aucun local ou pièce n'est actuellement enregistré dans la base de données.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
@endsection