@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-slate-900">📄 Référentiel Parcellaire</h1>
        <a href="{{ route('parcelles.create') }}"
            class="bg-violet-600 hover:bg-violet-700 text-white font-bold py-2 px-4 rounded-lg shadow-sm">
            + Nouvelle Parcelle
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Section</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">N° Parcelle</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Lieu-dit</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Surface</th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase">Carto</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                @forelse($parcelles as $parcelle)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-700">
                        {{ $parcelle->section_cadastrale }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900">
                        {{ $parcelle->num_parcelle }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                        {{ $parcelle->lieuDit->nom_lieu_dit ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                        {{ $parcelle->surface_cadastrale ?? '-' }} m²
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($parcelle->geom_parcelle)
                        <span
                            class="inline-flex items-center bg-violet-50 text-violet-600 border border-violet-200 rounded-full px-2.5 py-1 text-xs font-bold">🗺️
                            Oui</span>
                        @else
                        <span
                            class="inline-flex items-center bg-slate-50 text-slate-400 border border-slate-200 rounded-full px-2.5 py-1 text-xs font-bold">À
                            tracer</span>
                        @endif
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('parcelles.show', $parcelle->id_parcelle) }}"
                            class="text-blue-600 hover:text-blue-900 mr-3">Voir</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-slate-500">Aucune parcelle n'a été saisie.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection