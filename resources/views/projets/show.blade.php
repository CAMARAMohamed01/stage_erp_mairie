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
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2">📋 Note
                        d'opportunité</h3>
                    <div class="text-sm text-slate-700 bg-slate-50 p-4 rounded-lg border border-slate-100">
                        <p class="italic text-slate-600 leading-relaxed">
                            {{ $projet->avis ?? 'Aucun avis ou note contextuelle renseigné.' }}
                        </p>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">⚖️ Délibérations & Arbitrages
                        </h3>
                        <span class="text-[10px] font-black bg-slate-200 text-slate-600 px-2 py-0.5 rounded uppercase">
                            {{ $projet->decisions->count() }} commissions
                        </span>
                    </div>

                    <div class="divide-y divide-slate-100 bg-white text-sm">
                        @forelse($projet->decisions as $decision)
                            <div
                                class="p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 hover:bg-slate-50/50 transition">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-mono font-bold text-slate-900">📅
                                            {{ \Carbon\Carbon::parse($decision->date_commission)->format('d/m/Y') }}</span>
                                        @php
                                            $badge = match ($decision->statut_decision) {
                                                'Validé', 'Approuvé' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                                'Refusé', 'Rejeté' => 'bg-rose-50 text-rose-700 border-rose-100',
                                                default => 'bg-amber-50 text-amber-700 border-amber-100'
                                            };
                                        @endphp
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full border {{ $badge }}">
                                            {{ $decision->statut_decision }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-600 italic">«
                                        {{ $decision->commentaire_elus ?? 'Sans commentaire' }} »
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-xs text-slate-400 italic">Aucun arbitrage de commission enregistré.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden text-sm">
                    <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">🛠️ Suivi des chantiers
                            (Interventions)</h3>
                        <a href="{{ route('interventions.create', ['projet_id' => $projet->id_projet]) }}"
                            class="px-2.5 py-1 bg-blue-600 text-white text-[10px] font-bold rounded-md hover:bg-blue-700 transition shadow-sm">
                            ➕ Planifier Travaux
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100">
                                    <th class="p-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                        Intervention</th>
                                    <th
                                        class="p-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-right">
                                        Statut</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($projet->interventions as $intervention)
                                    <tr class="group hover:bg-slate-50/80 transition">
                                        <td class="p-4">
                                            <a href="{{ route('interventions.show', $intervention->id_int) }}"
                                                class="font-bold text-slate-800 hover:text-blue-600 block">
                                                {{ $intervention->description ?? 'Intervention #' . $intervention->id_int }}
                                            </a>
                                            <span class="text-[10px] text-slate-400 font-mono mt-0.5 block">📅
                                                {{ $intervention->date_ouverture }}</span>
                                        </td>
                                        <td class="p-4 text-right">
                                            <span
                                                class="px-2 py-0.5 text-[9px] font-bold rounded-full bg-slate-100 text-slate-600 border border-slate-200 uppercase">
                                                {{ $intervention->statut_global }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="p-6 text-center text-slate-400 italic">Aucun bon de travaux lié à
                                            ce projet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="space-y-6">

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h3
                        class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2 text-center lg:text-left">
                        📊 Données Clés</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Budget Alloué</p>
                            <p class="text-2xl font-black text-blue-900 mt-0.5">
                                {{ $projet->budget_global_alloue ? number_format($projet->budget_global_alloue, 2, ',', ' ') . ' €' : '0,00 €' }}
                            </p>
                        </div>
                        <div class="border-t pt-3">
                            <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Responsable / Chef de
                                projet</p>
                            <p class="text-sm font-bold text-slate-800 mt-1 flex items-center gap-1.5">
                                👤
                                {{ $projet->chefProjet ? $projet->chefProjet->prenom_user . ' ' . $projet->chefProjet->nom_user : 'Agent non assigné' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h3
                        class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2 text-center lg:text-left">
                        📍 Périmètre d'impact</h3>

                    <div class="space-y-5">
                        <div>
                            <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-2">Bâtiments
                                concernés</p>
                            @if($projet->batiments->count() > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($projet->batiments as $bat)
                                        <span
                                            class="px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 text-xs font-bold rounded-md flex items-center gap-1">
                                            🏢 {{ $bat->nom_bat }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-slate-400 italic">Aucun bâtiment lié.</p>
                            @endif
                        </div>

                        <div class="border-t pt-4">
                            <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-2">Lieux publics
                                concernés</p>
                            @if($projet->lieuxPublics->count() > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($projet->lieuxPublics as $lieu)
                                        <span
                                            class="px-2.5 py-1 bg-teal-50 text-teal-700 border border-teal-100 text-xs font-bold rounded-md flex items-center gap-1">
                                            🌳 {{ $lieu->nom_lieu }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-slate-400 italic">Aucun lieu public lié.</p>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection