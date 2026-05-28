@extends('layouts.app')

@section('title', 'Registre des Dossiers Financiers')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6 pb-12">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div
                class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-xl font-bold text-slate-800">💳 Dossiers Financiers & Facturation</h1>
                    <p class="text-sm text-slate-500">Suivi des engagements, d'émissions de titres et ordonnancements.</p>
                </div>
                @can('check-permission', ['Finances & Achats', 'ecriture'])
                    <a href="{{ route('dossiers-financiers.create') }}"
                        class="flex items-center gap-2 bg-blue-600 px-4 py-2 rounded-lg text-sm font-bold text-white hover:bg-blue-700 shadow-md transition">
                        ➕ Nouveau Dossier
                    </a>
                @endcan
            </div>

            <div class="p-4 bg-white border-b border-slate-100">
                <form action="{{ route('dossiers-financiers.index') }}" method="GET"
                    class="flex flex-wrap gap-3 items-center">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par objet..."
                        class="border border-slate-300 rounded-lg text-sm px-3 py-1.5 focus:ring-2 focus:ring-blue-500 outline-none bg-slate-50">

                    <select name="statut"
                        class="border border-slate-300 rounded-lg text-sm px-3 py-1.5 focus:ring-2 focus:ring-blue-500 outline-none bg-white">
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
                        class="bg-slate-800 text-white px-4 py-1.5 rounded-lg text-sm font-semibold hover:bg-slate-700 transition">Filtrer</button>
                    <a href="{{ route('dossiers-financiers.index') }}"
                        class="text-slate-500 hover:text-slate-800 text-sm font-medium">Réinitialiser</a>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs uppercase tracking-wider font-bold">
                            <th class="px-6 py-4">Dossier / Objet</th>
                            <th class="px-6 py-4">Tiers & Contrat</th>
                            <th class="px-6 py-4">Pièces (Réf)</th>
                            <th class="px-6 py-4 text-center">Statut actuel</th>
                            <th class="px-6 py-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($dossiers as $d)
                            <tr class="hover:bg-slate-50 transition-colors text-sm font-medium text-slate-700">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900">
                                        DOS-{{ str_pad($d->id_dossier_f, 4, '0', STR_PAD_LEFT) }}</div>
                                    <div class="text-xs text-slate-400 font-normal mt-0.5">
                                        {{ Str::limit($d->objet_dossier ?? 'Sans objet', 45) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-normal">
                                    <div class="font-bold text-slate-800">
                                        @if($d->tiers)
                                            {{ $d->tiers->type_tiers === 'Physique' ? $d->tiers->physique?->prenom_tiers . ' ' . $d->tiers->physique?->nom_tiers : $d->tiers->morale?->raison_sociale }}
                                        @else
                                            <span class="text-slate-400 italic">Non spécifié</span>
                                        @endif
                                    </div>
                                    @if($d->contrat)
                                        <span
                                            class="text-[10px] text-blue-600 bg-blue-50 border px-2 py-0.5 rounded block w-max mt-1">
                                            Contrat: {{ $d->contrat->numero_contrat }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs font-mono text-slate-500 space-y-0.5">
                                    @if($d->numero_facture)
                                        <div><span class="text-slate-400">FAC:</span>
                                    {{ $d->numero_facture }}</div> @endif
                                    @if($d->numero_bon_commande)
                                        <div><span class="text-slate-400">BC:</span>
                                    {{ $d->numero_bon_commande }}</div> @endif
                                    @if(!$d->numero_facture && !$d->numero_bon_commande) <span
                                    class="text-slate-400 italic">Aucune référence</span> @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $color = match ($d->statut_actuel) {
                                            'Payé', 'Clôturé' => 'bg-green-50 text-green-700 border-green-100',
                                            'En attente de paiement', 'Transmis Trésorerie' => 'bg-amber-50 text-amber-700
                                                                border-amber-100',
                                            'Annulé' => 'bg-red-50 text-red-700 border-red-100',
                                            default => 'bg-blue-50 text-blue-700 border-blue-100'
                                        };
                                    @endphp
                                    <span class="px-2.5 py-0.5 text-xs font-bold rounded-full border {{ $color }}">
                                        {{ $d->statut_actuel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('dossiers-financiers.show', $d->id_dossier_f) }}"
                                        class="text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded transition">Ouvrir</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center text-slate-400 italic">Aucun dossier financier trouvé.
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