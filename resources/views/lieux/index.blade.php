@extends('layouts.app')

@section('header_title', 'Gestion des Espaces et Lieux Publics')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        <div class="flex flex-wrap justify-between items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Espaces & Lieux Publics</h1>
                <p class="text-sm text-slate-500 mt-1">Inventaire des parcs, places, cimetières et installations
                    extérieures.</p>
            </div>
            <div>
                @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                    <a href="{{ route('lieux.create') }}"
                        class="px-4 py-2.5 bg-green-700 hover:bg-green-600 text-white text-sm font-semibold rounded-lg shadow-sm transition flex items-center gap-2">
                        🌳 Ajouter un lieu
                    </a>
                @endcan
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="p-4">Nom du Lieu</th>
                        <th class="p-4">Localisation (Adresse)</th>
                        <th class="p-4 text-center">Bâtiment Rattaché</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($lieux as $lieu)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="p-4 font-semibold text-slate-800">
                                {{ $lieu->nom_lieu }}
                            </td>
                            <td class="p-4 text-slate-600">
                                @if($lieu->nom_voie)
                                    {{ $lieu->num_rue }} {{ $lieu->nom_voie }}, {{ $lieu->ville }}
                                @else
                                    <span class="text-sm">Sect. {{ $lieu->section_cadastrale }} -
                                        N°{{ $lieu->num_parcelle }}</span><br>
                                    <span class="text-xs text-slate-400 italic">📍 {{ $lieu->nom_lieu_dit }}</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                @if($lieu->nom_bat)
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-md bg-blue-50 text-blue-700 border border-blue-100">
                                        {{ $lieu->nom_bat }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('lieux.show', $lieu->id_lieu) }}"
                                    class="inline-flex items-center px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-md transition shadow-sm">
                                    Détails →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-slate-400 italic bg-slate-50/50">
                                Aucun lieu public n'est actuellement enregistré dans le référentiel.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
@endsection