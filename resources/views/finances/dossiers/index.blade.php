@extends('layouts.app')

@section('title', 'Registre des Dossiers Financiers')

@section('content')
<div class="max-w-6xl mx-auto space-y-6 pb-12">

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div
            class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-800">💳 Dossiers Financiers & Facturation</h1>
                <p class="text-sm text-slate-500">Suivi des engagements de dépenses, d'émissions de titres et
                    ordonnancements de la régie.</p>
            </div>
            @can('check-permission', ['Finances & Achats', 'ecriture'])
            <a href="{{ route('dossiers-financiers.create') }}"
                class="flex items-center gap-2 bg-blue-700 hover:bg-blue-600 px-4 py-2 rounded-lg text-xs font-bold text-white shadow-sm transition">
                ➕ Nouveau Dossier
            </a>
            @endcan
        </div>

        <div class="p-4 bg-white border-b border-slate-100">
            <form action="{{ route('dossiers-financiers.index') }}" method="GET"
                class="flex flex-wrap gap-3 items-center">
                <div class="relative flex-1 min-w-[260px]">
                    <span
                        class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400 text-sm">🔍</span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Rechercher par objet, libellé..."
                        class="w-full border border-slate-300 rounded-lg text-sm pl-9 pr-4 py-1.5 focus:ring-2 focus:ring-blue-500/20 outline-none bg-slate-50 transition focus:bg-white">
                </div>

                <select name="statut"
                    class="border border-slate-300 rounded-lg text-sm px-3 py-1.5 focus:ring-2 focus:ring-blue-500/20 outline-none bg-white font-medium text-slate-700">
                    <option value="">Tous les statuts</option>
                    @foreach($statuts as $statut)
                    @if($statut)
                    <option value="{{ $statut }}" {{ request('statut') == $statut ? 'selected' : '' }}>
                        {{ $statut }}
                    </option>
                    @endif
                    @endforeach
                </select>

                <button type="submit"
                    class="bg-blue-600 text-white px-4 py-1.5 rounded-lg text-sm font-semibold hover:bg-blue-700 shadow-sm transition">
                    Filtrer
                </button>

                @if(request('search') || request('statut'))
                <a href="{{ route('dossiers-financiers.index') }}"
                    class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-3 py-1.5 rounded-lg text-sm font-medium transition">
                    Réinitialiser
                </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs uppercase tracking-wider font-bold">
                        <th class="px-6 py-4 w-1/3">Dossier / Objet</th>
                        <th class="px-6 py-4">Flux comptable</th>
                        <th class="px-6 py-4">Tiers & Contrat</th>
                        <th class="px-6 py-4">Pièces (Réf)</th>
                        <th class="px-6 py-4 text-center">Statut actuel</th>
                        <th class="px-6 py-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($dossiers as $d)
                    <tr class="hover:bg-slate-50/80 transition-colors text-sm font-medium text-slate-700">
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-900">
                                DOS-{{ str_pad($d->id_dossier_f, 4, '0', STR_PAD_LEFT) }}
                            </div>
                            <div class="text-xs text-slate-400 font-normal mt-0.5" title="{{ $d->objet_dossier }}">
                                {{ Str::limit($d->objet_dossier ?? 'Sans objet', 45) }}
                            </div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($d->numero_titre_recette || $d->date_constatation_recette)
                            <span
                                class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold rounded bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase tracking-wide">
                                📈 Recette
                            </span>
                            @else
                            <span
                                class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold rounded bg-blue-50 text-blue-700 border border-blue-100 uppercase tracking-wide">
                                📉 Dépense
                            </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 font-normal">
                            <div class="font-bold text-slate-800">
                                @if($d->tiers)
                                {{ $d->tiers->type_tiers === 'Physique' ? $d->tiers->physique?->prenom_tiers . ' ' . $d->tiers->physique?->nom_tiers : $d->tiers->morale?->raison_sociale }}
                                @else
                                <span class="text-slate-400 italic font-normal">Non spécifié</span>
                                @endif
                            </div>
                            @if($d->contrat)
                            <span
                                class="text-[10px] text-blue-600 bg-blue-50 border border-blue-100 px-2 py-0.5 rounded block w-max mt-1 font-semibold">
                                Contrat: {{ $d->contrat->numero_contrat }}
                            </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-xs font-mono text-slate-500 space-y-0.5">
                            @if($d->numero_facture)
                            <div><span class="text-slate-400 font-sans font-semibold">FAC:</span>
                                {{ $d->numero_facture }}</div>
                            @endif
                            @if($d->numero_bon_commande)
                            <div><span class="text-slate-400 font-sans font-semibold">BC:</span>
                                {{ $d->numero_bon_commande }}</div>
                            @endif
                            @if($d->numero_titre_recette)
                            <div class="text-emerald-700"><span
                                    class="text-slate-400 font-sans font-semibold">TITRE:</span>
                                {{ $d->numero_titre_recette }}</div>
                            @endif
                            @if(!$d->numero_facture && !$d->numero_bon_commande && !$d->numero_titre_recette)
                            <span class="text-slate-400 italic font-sans font-normal">Aucune référence</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            @php
                            $color = match ($d->statut_actuel) {
                            'Payé', 'Encaissé / Soldé', 'Clôturé' => 'bg-green-50 text-green-700 border-green-100',
                            'En attente de paiement', 'Transmis Trésorerie', 'Titre émis' => 'bg-amber-50 text-amber-700
                            border-amber-100',
                            'Annulé' => 'bg-red-50 text-red-700 border-red-100',
                            default => 'bg-blue-50 text-blue-700 border-blue-100'
                            };
                            @endphp
                            <span class="px-2.5 py-0.5 text-xs font-bold rounded-full border {{ $color }}">
                                {{ $d->statut_actuel }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <a href="{{ route('dossiers-financiers.show', $d->id_dossier_f) }}"
                                class="text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded transition">
                                Ouvrir →
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400 italic">
                            Aucun dossier budgétaire ou comptable n'a été trouvé dans le registre.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
            {{ $dossiers->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection