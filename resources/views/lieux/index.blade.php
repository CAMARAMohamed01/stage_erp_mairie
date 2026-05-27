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
        <form action="{{ route('lieux.index') }}" method="GET" class="flex gap-2 mb-6">
            <div class="relative flex-grow max-w-md">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Rechercher par lieu, adresse ou lieu-dit..."
                    class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm transition">
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
                <a href="{{ route('lieux.index') }}"
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