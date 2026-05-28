@extends('layouts.app')

@section('header_title', 'Référentiel des Types ERP')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        <div class="flex flex-wrap justify-between items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Catégories et Types d'ERP</h1>
                <p class="text-sm text-slate-500 mt-1">Classification des Établissements Recevant du Public (ERP).</p>
            </div>
            <div>
                @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                    <a href="{{ route('types-erp.create') }}"
                        class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition flex items-center gap-2">
                        ➕ Ajouter une catégorie
                    </a>
                @endcan
            </div>
        </div>

        <form action="{{ route('types-erp.index') }}" method="GET" class="flex flex-wrap gap-2 mb-6">
            <div class="relative flex-grow max-w-sm">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Rechercher une réglementation, un type..."
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
                <a href="{{ route('types-erp.index') }}"
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
                        <th class="p-4 text-center">Catégorie</th>
                        <th class="p-4 text-center">Type</th>
                        <th class="p-4">Public Cible</th>
                        <th class="p-4">Réglementation</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($types_erp as $erp)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="p-4 text-center">
                                <span
                                    class="px-3 py-1 font-bold text-slate-700 bg-slate-100 rounded-md border border-slate-200">
                                    {{ $erp->categorie_erp ?? '-' }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <span class="px-3 py-1 font-bold text-blue-700 bg-blue-50 rounded-md border border-blue-100">
                                    {{ $erp->type_erp ?? '-' }}
                                </span>
                            </td>
                            <td class="p-4 font-medium text-slate-800">
                                {{ $erp->public_cible ?? 'Non spécifié' }}
                            </td>
                            <td class="p-4 text-slate-600">
                                {{ Str::limit($erp->reglementation_applicable, 50) }}
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('types-erp.show', $erp->id_type_erp) }}"
                                    class="inline-flex items-center px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-md transition shadow-sm">
                                    Détails →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400 italic bg-slate-50/50">
                                Aucun type d'ERP n'est enregistré.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection