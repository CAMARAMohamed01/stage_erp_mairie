@extends('layouts.app')

@section('header_title', 'Référentiel des Contrôles Réglementaires')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        <div class="flex flex-wrap justify-between items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Contrôles Réglementaires</h1>
                <p class="text-sm text-slate-500 mt-1">Catalogue des obligations légales applicables au patrimoine.</p>
            </div>
            <div>
                @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                    <a href="{{ route('controles.create') }}"
                        class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition flex items-center gap-2">
                        ➕ Ajouter un contrôle
                    </a>
                @endcan
            </div>
        </div>

        <form action="{{ route('controles.index') }}" method="GET" class="flex flex-wrap gap-2 mb-6">
            <div class="relative flex-grow max-w-sm">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Rechercher une désignation, un domaine..."
                    class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 shadow-sm transition">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
            <button type="submit"
                class="px-4 py-2 bg-slate-800 text-white text-sm font-bold rounded-lg hover:bg-slate-700 transition">
                Rechercher
            </button>
            @if(request()->filled('search'))
                <a href="{{ route('controles.index') }}"
                    class="px-4 py-2 bg-slate-100 text-slate-600 text-sm font-bold rounded-lg hover:bg-slate-200 transition">
                    Réinitialiser
                </a>
            @endif
        </form>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="p-4">Désignation</th>
                        <th class="p-4 text-center">Périodicité</th>
                        <th class="p-4 text-center">Obligatoire</th>
                        <th class="p-4">ERP Ciblés</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($controles as $controle)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="p-4">
                                <p class="font-semibold text-slate-800">{{ $controle->designation }}</p>
                                <p class="text-xs text-slate-500">{{ $controle->domaine_technique ?? 'Domaine non précisé' }}
                                </p>
                            </td>
                            <td class="p-4 text-center text-slate-600 font-medium">
                                {{ $controle->frequence_mois ? $controle->frequence_mois . ' mois' : 'Ponctuel' }}
                            </td>
                            <td class="p-4 text-center">
                                @if($controle->est_legalement_obligatoire)
                                    <span
                                        class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-red-50 text-red-700 border border-red-100">OUI</span>
                                @else
                                    <span
                                        class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-slate-100 text-slate-600 border border-slate-200">NON</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($controle->typesErp as $erp)
                                        <span
                                            class="px-2 py-0.5 text-[10px] font-semibold bg-blue-50 text-blue-700 rounded border border-blue-100"
                                            title="{{ $erp->reglementation_applicable }}">
                                            Cat. {{ $erp->categorie_erp }} - {{ $erp->type_erp }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-slate-400 italic">Aucun ERP lié</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('controles.show', $controle->id_controle) }}"
                                    class="inline-flex items-center px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-md transition shadow-sm">
                                    Consulter →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400 italic bg-slate-50/50">
                                Aucun contrôle réglementaire enregistré dans le catalogue.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection