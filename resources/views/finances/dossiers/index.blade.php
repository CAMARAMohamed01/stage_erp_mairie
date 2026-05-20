@extends('layouts.app')

@section('title', 'Registre des Dossiers Financiers')

@section('content')
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex justify-between items-center">
            <div>
                <h1 class="text-xl font-bold text-slate-800">💳 Dossiers Financiers & Facturation</h1>
                <p class="text-sm text-slate-500">Suivi des devis, bons de commande et paiements</p>
            </div>
            <a href="{{ route('dossiers-financiers.create') }}"
                class="flex items-center gap-2 bg-blue-600 px-4 py-2 rounded-lg text-sm font-bold text-white hover:bg-blue-700 shadow-md transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nouveau Dossier
            </a>
        </div>

        <div class="p-4 bg-white border-b border-slate-100">
            <form action="{{ route('dossiers-financiers.index') }}" method="GET" class="flex flex-wrap gap-4 items-center">
                <select name="statut"
                    class="border border-slate-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
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
                    class="bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-700 transition">Filtrer</button>
                <a href="{{ route('dossiers-financiers.index') }}"
                    class="text-slate-500 hover:text-slate-800 text-sm font-medium">Réinitialiser</a>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold">Dossier / Objet</th>
                        <th class="px-6 py-4 font-semibold">Tiers & Contrat</th>
                        <th class="px-6 py-4 font-semibold">Pièces (Réf)</th>
                        <th class="px-6 py-4 font-semibold">Statut actuel</th>
                        <th class="px-6 py-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($dossiers as $d)
                        <tr class="hover:bg-slate-50 transition-colors text-sm">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">DOS-{{ str_pad($d->id_dossier, 4, '0', STR_PAD_LEFT) }}
                                </div>
                                <div class="text-xs text-slate-500 mt-1">{{ Str::limit($d->objet_dossier ?? 'Sans objet', 40) }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-700">
                                    {{ $d->tiers->nom_affiche ?? 'Tiers non défini' }}
                                </div>
                                @if($d->contrat)
                                    <a href="{{ route('contrats.show', $d->id_contrat) }}"
                                        class="text-[10px] bg-blue-50 text-blue-600 border border-blue-100 px-2 py-0.5 rounded block w-max mt-1 hover:underline">
                                        Contrat: {{ $d->contrat->numero_contrat }}
                                    </a>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600 space-y-1 font-mono">
                                @if($d->numero_devis)
                                    <div><span class="text-slate-400">DEV:</span> {{ $d->numero_devis }}</div>
                                @endif
                                @if($d->numero_bon_commande)
                                    <div><span class="text-slate-400">BC:</span>
                                {{ $d->numero_bon_commande }}</div> @endif
                                @if($d->numero_facture)
                                    <div><span class="text-slate-400">FAC:</span> {{ $d->numero_facture }}
                                </div> @endif
                                @if(!$d->numero_devis && !$d->numero_bon_commande && !$d->numero_facture)
                                    <span class="text-slate-400 italic">Aucune pièce saisie</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $color = match ($d->statut_actuel) {
                                        'Payé', 'Clôturé' => 'bg-green-100 text-green-700',
                                        'En attente de paiement', 'Transmis Trésorerie' => 'bg-amber-100 text-amber-700',
                                        'Annulé' => 'bg-red-100 text-red-700',
                                        default => 'bg-blue-100 text-blue-700'
                                    };
                                @endphp
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $color }}">
                                    {{ $d->statut_actuel }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('dossiers-financiers.show', $d->id_dossier) }}"
                                    class="text-blue-600 hover:text-blue-800 font-medium">Ouvrir →</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400 italic">
                                Aucun dossier financier n'a été trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-100">
            {{ $dossiers->appends(request()->query())->links() }}
        </div>
    </div>
@endsection