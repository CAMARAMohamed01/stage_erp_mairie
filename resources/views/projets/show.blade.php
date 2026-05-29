@extends('layouts.app')

@section('title', 'Détails du Projet : ' . $projet->nom_projet)

@section('content')
<div class="max-w-6xl mx-auto space-y-6 pb-12">

    <div
        class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="text-3xl">🏗️</span>
                <h1 class="text-2xl font-bold text-slate-800">{{ $projet->nom_projet }}</h1>
                <span
                    class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-blue-100 text-blue-700 uppercase tracking-wider">
                    {{ $projet->type_projet ?? 'Mandat' }}
                </span>
            </div>
            <p class="text-slate-500 font-medium ml-11">Mandat — {{ $projet->annee_mandat }}</p>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('projets.index') }}"
                class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 transition text-center flex-1 sm:flex-initial">
                ← Retour
            </a>

            @if(auth()->user()->can('check-permission', ['Patrimoine & Travaux', 'ecriture']))
            <a href="{{ route('projets.edit', $projet->id_projet) }}"
                class="px-4 py-2 bg-amber-100 text-amber-700 border border-amber-200 text-sm font-semibold rounded-lg hover:bg-amber-200 transition text-center flex-1 sm:flex-initial">
                ✏️ Modifier
            </a>
            @endif

            @if(auth()->user()->can('check-permission', ['Patrimoine & Travaux', 'suppression']))
            <form action="{{ route('projets.destroy', $projet->id_projet) }}" method="POST"
                onsubmit="return confirm('Confirmer la suppression définitive de ce projet ?');"
                class="flex-1 sm:flex-initial">
                @csrf @method('DELETE')
                <button type="submit"
                    class="w-full px-4 py-2 bg-red-100 text-red-700 border border-red-200 text-sm font-semibold rounded-lg hover:bg-red-200 transition text-center">
                    🗑️ Supprimer
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2">📋 Informations
                    Générales</h3>
                <div class="text-sm text-slate-700 bg-slate-50 p-4 rounded-lg border border-slate-100">
                    <span class="font-bold text-slate-900 block mb-1">Avis / Note d'opportunité :</span>
                    <p class="italic text-slate-600 leading-relaxed">
                        {{ $projet->avis ?? 'Aucun avis ou note contextuelle renseigné.' }}
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">⚖️ Délibérations & Commissions
                        d'Élus</h3>
                    <span class="text-xs font-bold bg-slate-200 text-slate-700 px-2 py-0.5 rounded-md">
                        {{ $projet->decisions->count() }} {{ Str::plural('arbitrage', $projet->decisions->count()) }}
                    </span>
                </div>

                <div class="divide-y divide-slate-100 bg-white">
                    @forelse($projet->decisions as $decision)
                    <div
                        class="p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 text-sm hover:bg-slate-50/50 transition">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('decisions-commission.edit', $decision->id_decision) }}"
                                    class="font-mono font-bold text-slate-900 hover:text-blue-600 hover:underline"
                                    title="Consulter l'arbitrage complet">
                                    📅 {{ \Carbon\Carbon::parse($decision->date_commission)->format('d/m/Y') }}
                                </a>

                                @php
                                $badge = match ($decision->statut_decision) {
                                'Validé', 'Approuvé' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                'Refusé', 'Rejeté' => 'bg-rose-50 text-rose-700 border-rose-100',
                                'Ajourné', 'En attente' => 'bg-amber-50 text-amber-700 border-amber-100',
                                default => 'bg-slate-50 text-slate-700 border-slate-200'
                                };
                                @endphp
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full border {{ $badge }}">
                                    {{ $decision->statut_decision }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-600 font-normal italic">
                                « {{ $decision->commentaire_elus ?? 'Aucune consigne écrite rédigée' }} »
                            </p>
                        </div>

                        @if($decision->id_operation)
                        <div class="text-right flex-shrink-0">
                            <span
                                class="text-[10px] bg-slate-100 border border-slate-200 font-mono text-slate-600 px-2 py-0.5 rounded font-bold">
                                Lien Opération #{{ $decision->id_operation }}
                            </span>
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="p-6 text-center text-xs text-slate-400 italic">
                        Aucun arbitrage formel de commission n'est encore enregistré pour ce projet de mandat.
                    </div>
                    @endforelse
                </div>
            </div>

        </div>

        <div class="space-y-6">

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2">📊 Données Clés
                </h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Budget Global Alloué
                        </p>
                        <p class="text-xl font-black text-blue-900 mt-0.5">
                            {{ $projet->budget_global_alloue ? number_format($projet->budget_global_alloue, 2, ',', ' ') . ' €' : 'Non provisionné' }}
                        </p>
                    </div>
                    <div class="border-t pt-3">
                        <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Chef de projet /
                            Responsable</p>
                        <p class="text-sm font-bold text-slate-800 mt-0.5 flex items-center gap-1.5">
                            👤
                            {{ $projet->chefProjet ? $projet->chefProjet->prenom_user . ' ' . $projet->chefProjet->nom_user : 'Aucun agent assigné' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <div class="flex justify-between items-center mb-4 border-b pb-2 gap-2">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">🛠️ Suivi Chantiers</h3>
                    <a href="{{ route('interventions.create', ['projet_id' => $projet->id_projet]) }}"
                        class="px-2.5 py-1 bg-blue-600 text-white text-[10px] font-bold rounded-md hover:bg-blue-700 transition shadow-sm whitespace-nowrap">
                        ➕ Planifier
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th class="pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    Description</th>
                                <th
                                    class="pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-right">
                                    Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($projet->interventions as $intervention)
                            <tr class="text-xs group hover:bg-slate-50/80 transition">
                                <td class="py-2.5 pr-2">
                                    <a href="{{ route('interventions.show', $intervention->id_int) }}"
                                        class="font-bold text-slate-800 hover:text-blue-600 hover:underline block truncate max-w-[150px]"
                                        title="Consulter le bon d'intervention : {{ $intervention->description }}">
                                        {{ $intervention->description ?? 'Intervention sans titre' }}
                                    </a>
                                    <span class="text-[10px] text-slate-400 font-mono block mt-0.5">
                                        {{ $intervention->date_intervention ?? 'Date non fixée' }}
                                    </span>
                                </td>
                                <td class="py-2.5 text-right whitespace-nowrap">
                                    <span
                                        class="px-2 py-0.5 text-[9px] font-bold rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                                        {{ $intervention->statut ?? 'En cours' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="pt-4 text-center text-slate-400 italic text-xs">
                                    Aucun bon de travaux lié.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection