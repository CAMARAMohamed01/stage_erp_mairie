@extends('layouts.app')

@section('header_title', 'Gestion des Ouvrages d\'Art')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">

            <div class="p-4 border-b border-slate-100 flex flex-wrap justify-between items-center bg-slate-50 gap-4">
                <h2 class="text-sm font-bold text-slate-800">Liste des Ouvrages (Ponts, Murs...)</h2>

                <div class="flex flex-wrap items-center gap-4">
                    <form action="{{ route('ouvrages.index') }}" method="GET" class="flex gap-2">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Rechercher un ouvrage..."
                            class="px-3 py-1 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <button type="submit"
                            class="px-3 py-1 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition">Rechercher</button>
                    </form>

                    @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                        <a href="{{ route('ouvrages.create') }}"
                            class="px-4 py-1.5 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition flex items-center shadow-sm">
                            ➕ Ajouter un ouvrage
                        </a>
                    @endcan
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 border-b border-slate-200 uppercase text-xs font-bold text-slate-500">
                        <tr>
                            <th class="px-6 py-4">Nom de l'ouvrage</th>
                            <th class="px-6 py-4">Type</th>
                            <th class="px-6 py-4">Voie Rattachée</th>
                            <th class="px-6 py-4 text-center">Spécificités</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($ouvrages as $o)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4 font-bold text-slate-900">{{ $o->nom_ouvrage }}</td>
                                <td class="px-6 py-4 text-slate-600">
                                    <span
                                        class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-md text-xs font-semibold">{{ $o->type_ouvrage ?? '-' }}</span>
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-700">{{ $o->nom_voie ?? 'Non affecté' }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if($o->sous_loi_didier) <span
                                        class="text-xs bg-amber-100 text-amber-800 px-2 py-0.5 rounded font-bold"
                                    title="Loi Didier">LD</span> @endif
                                    @if($o->dimension_sup_2m) <span
                                        class="text-xs bg-slate-200 text-slate-700 px-2 py-0.5 rounded font-bold"
                                    title="Ouverture > 2m">>2m</span> @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('ouvrages.show', $o->id_ouvrage) }}"
                                        class="text-blue-600 font-bold hover:underline bg-blue-50 px-3 py-1.5 rounded-lg">Détails</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-400 italic">Aucun ouvrage trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $ouvrages->links() }}
            </div>
        </div>
    </div>
@endsection