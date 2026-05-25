@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-slate-900">🗺️ Gestion des Zones</h1>
        <a href="{{ route('zones.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-sm">
            + Nouvelle Zone
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nom de la
                        Zone</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Secteur
                        Associé</th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Carto
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                @forelse($zones as $zone)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">#{{ $zone->id_zone }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900">{{ $zone->nom_zone }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $zone->code_zone ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                        @if($zone->secteur)
                        <span class="bg-slate-100 text-slate-700 px-2 py-1 rounded text-xs font-medium">
                            {{ $zone->secteur->nom_secteur }}
                        </span>
                        @else
                        <span class="text-red-500 text-xs">Aucun secteur</span>
                        @endif
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($zone->geom_zone)
                        <span title="Polygone tracé"
                            class="inline-flex items-center justify-center bg-emerald-50 text-emerald-600 border border-emerald-200 rounded-full px-2.5 py-1 text-xs font-bold">
                            🗺️ Oui
                        </span>
                        @else
                        <span title="Géométrie manquante"
                            class="inline-flex items-center justify-center bg-slate-50 text-slate-400 border border-slate-200 rounded-full px-2.5 py-1 text-xs font-bold">
                            À tracer
                        </span>
                        @endif
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('zones.show', $zone->id_zone) }}"
                            class="text-blue-600 hover:text-blue-900 mr-3">Voir</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                        Aucune zone n'a encore été créée.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection