@extends('layouts.app')

@section('header_title', 'Gestion du Patrimoine Communal')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        <div class="flex flex-wrap justify-between items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Bâtiments & Lieux Publics</h1>
                <p class="text-sm text-slate-500 mt-1">Inventaire complet du patrimoine de la commune et suivi de leur
                    classification ERP.</p>
            </div>
            <div>
                @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                    <a href="{{ route('batiments.create') }}"
                        class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition flex items-center gap-2">
                        ➕ Ajouter un bâtiment
                    </a>
                @endcan
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="p-4">Nom du bâtiment / Lieu</th>
                        <th class="p-4">Adresse</th>
                        <th class="p-4 text-center">Type ERP</th>
                        <th class="p-4 text-center">Catégorie ERP</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($batiments as $bat)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="p-4 font-semibold text-slate-800">
                                🏢 {{ $bat->nom_bat }}
                            </td>
                            <td class="p-4 text-slate-500">
                                {{ $bat->adresse_batiment ?? 'Non renseignée' }}
                            </td>

                            <td class="p-4 text-center">
                                @if($bat->categorie_erp)
                                    <span
                                        class="px-2.5 py-1 text-xs font-bold rounded-full bg-blue-50 text-blue-700 border border-blue-100">
                                        {{ $bat->categorie_erp }}e catégorie
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('batiments.show', $bat->id_batiment) }}"
                                    class="inline-flex items-center px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-md transition shadow-sm">
                                    Consulter la fiche →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400 italic bg-slate-50/50">
                                Aucun bâtiment ou lieu public n'est actuellement enregistré dans la base de données.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
@endsection