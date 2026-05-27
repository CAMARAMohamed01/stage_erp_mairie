@extends('layouts.app')

@section('header_title', 'Registre des concessions funéraires')

@section('content')
    <div class="max-w-6xl mx-auto pb-12">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Registre des Concessions</h1>
                <p class="text-sm text-slate-500 mt-1">Suivi des actes juridiques, des titulaires et des échéances.</p>
            </div>
            @if(auth()->user()->can('check-permission', ['État Civil & Cimetières', 'ecriture']))
                <a href="{{ route('concessions.create') }}"
                    class="px-4 py-2 bg-slate-900 text-white font-bold rounded-lg shadow hover:bg-slate-800 transition">
                    ✍️ Acter une concession
                </a>
            @endif


        </div>
        <form action="{{ route('concessions.index') }}" method="GET" class="flex flex-wrap gap-2 mb-6">

            <div class="relative flex-grow max-w-sm">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Rechercher par titulaire ou n° acte..."
                    class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-500 shadow-sm transition">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <select name="statut"
                class="border border-slate-300 rounded-lg py-2 px-3 text-sm focus:ring-2 focus:ring-slate-500 shadow-sm">
                <option value="">Tous les statuts</option>
                <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                <option value="perpétuelle" {{ request('statut') == 'perpétuelle' ? 'selected' : '' }}>Perpétuelle</option>
                <option value="echues" {{ request('statut') == 'echues' ? 'selected' : '' }}>Échues (à reprendre)</option>
            </select>

            <button type="submit"
                class="px-4 py-2 bg-slate-800 text-white text-sm font-bold rounded-lg hover:bg-slate-700 transition">
                Filtrer
            </button>

            @if(request()->filled('search') || request()->filled('statut'))
                <a href="{{ route('concessions.index') }}"
                    class="px-4 py-2 bg-slate-100 text-slate-600 text-sm font-bold rounded-lg hover:bg-slate-200 transition">
                    Réinitialiser
                </a>
            @endif
        </form>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-bold">
                        <th class="p-4">N° Acte / Titulaire</th>
                        <th class="p-4">Emplacement</th>
                        <th class="p-4">Défunts inhumés</th>
                        <th class="p-4">Échéance</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($concessions as $concession)
                        <tr class="hover:bg-slate-50 transition text-sm">
                            <td class="p-4">
                                <p class="font-semibold text-slate-800">Contrat
                                    n°{{ $concession->contrat->numero_contrat ?? 'Inconnu' }}</p>
                                <p class="text-xs text-slate-500">Titulaire :
                                    {{ $concession->contrat->tiers->raison_sociale ?? ($concession->contrat->tiers->nom_tiers . ' ' . $concession->contrat->tiers->prenom_tiers) }}
                                </p>
                            </td>

                            <td class="p-4 text-slate-600">
                                <p class="font-medium">{{ $concession->emplacement->lieu->nom_lieu ?? 'Cimetière' }}</p>
                                <p class="text-xs font-mono text-slate-500">
                                    {{ $concession->emplacement->reference_emplacement }}
                                </p>
                            </td>

                            <td class="p-4">
                                @forelse($concession->defunts as $defunt)
                                    <span class="inline-block bg-slate-100 text-slate-700 text-xs px-2 py-0.5 rounded-md mb-1">
                                        💀 {{ $defunt->nom_tiers }} {{ $defunt->prenom_tiers }}
                                    </span>
                                @empty
                                    <span class="text-xs text-slate-400 italic">Aucun corps inhumé</span>
                                @endforelse
                            </td>

                            <td class="p-4">
                                @if($concession->contrat->date_echeance)
                                    @php
                                        $isExpired = \Carbon\Carbon::parse($concession->contrat->date_echeance)->isPast();
                                        $isSoon = \Carbon\Carbon::parse($concession->contrat->date_echeance)->diffInMonths(now()) < 6;
                                    @endphp <span
                                        class="font-medium {{ $isExpired ? 'text-red-600 font-bold' : ($isSoon ? 'text-amber-600' : 'text-slate-700') }}">
                                        {{ \Carbon\Carbon::parse($concession->contrat->date_echeance)->format('d/m/Y') }}
                                    </span>

                                    @if($isExpired)
                                        <span class="block text-[10px] text-red-500 font-bold uppercase tracking-wide">Échue (À
                                            reprendre)</span>
                                    @endif
                                @else
                                    <span class="text-slate-400 italic">Perpétuelle</span>
                                @endif
                            </td>

                            <td class="p-4 text-right">
                                <a href="{{ route('concessions.show', $concession->id_concession) }}"
                                    class="text-slate-600 hover:text-slate-900 font-semibold text-xs">Consulter</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400 italic">
                                Aucune concession enregistrée dans le système.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection