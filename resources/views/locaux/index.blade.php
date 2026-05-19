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

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="p-4">Désignation</th>
                        <th class="p-4">Bâtiment Rattaché</th>
                        <th class="p-4 text-center">Niveau / Étage</th>
                        <th class="p-4 text-center">Usage</th>
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
                            <td class="p-4 text-center text-slate-500">
                                {{ $local->libelle_usage ?? 'Non défini' }}
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