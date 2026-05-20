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