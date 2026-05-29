@extends('layouts.app')
@section('title', 'Arbitrages des Commissions')

@section('content')
<div class="max-w-6xl mx-auto space-y-5 pb-12">

    <div
        class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Décisions des Commissions d'Élus</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Registre des délibérations, validations de projets et
                arbitrages budgétaires municipaux.</p>
        </div>
        @can('check-permission', ['Conseil & Commissions', 'ecriture'])
        <a href="{{ route('decisions-commission.create') }}"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-xs shadow-sm transition flex items-center gap-1.5">
            Saisir une Décision
        </a>
        @endcan
    </div>

    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <form action="{{ route('decisions-commission.index') }}" method="GET"
            class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">

            <div class="sm:col-span-5 text-xs font-bold text-slate-600">
                <label for="search" class="block mb-1.5 pl-0.5">Rechercher un mot-clé, projet, écriture...</label>
                <div class="relative">
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                        placeholder="Ex: Rénovation, OP_2026..."
                        class="w-full text-xs border border-slate-300 rounded-lg pl-3 pr-8 py-2.5 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition text-slate-700 font-medium">
                    @if(request('search'))
                    <a href="{{ route('decisions-commission.index', request()->except('search')) }}"
                        class="absolute right-2.5 top-3 text-slate-400 hover:text-slate-600 font-bold"
                        title="Effacer la recherche">✕</a>
                    @endif
                </div>
            </div>

            <div class="sm:col-span-3 text-xs font-bold text-slate-600">
                <label for="statut" class="block mb-1.5 pl-0.5">Statut de décision</label>
                <select id="statut" name="statut"
                    class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 font-semibold">
                    <option value="">-- Tous les statuts --</option>
                    @foreach($statutsDisponibles as $st)
                    <option value="{{ $st }}" {{ request('statut') === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2 text-xs font-bold text-slate-600">
                <label for="annee" class="block mb-1.5 pl-0.5">Année vote</label>
                <select id="annee" name="annee"
                    class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 font-semibold">
                    <option value="">-- Toutes --</option>
                    @for($i = date('Y'); $i >= 2020; $i--)
                    <option value="{{ $i }}" {{ request('annee') == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <div class="sm:col-span-2 flex gap-2">
                <button type="submit"
                    class="flex-1 px-3 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-xs transition shadow-sm text-center">
                    🔍 Filtrer
                </button>
                @if(request()->hasAny(['search', 'statut', 'annee']))
                <a href="{{ route('decisions-commission.index') }}"
                    class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg text-xs transition border text-center shadow-sm"
                    title="Réinitialiser tous les filtres">
                    🔄
                </a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b font-bold text-slate-500 text-xs uppercase tracking-wider">
                        <th class="p-4 w-32">Date Vote</th>
                        <th class="p-4 w-36">Statut décision</th>
                        <th class="p-4 w-1/3">Objets & Liaisons Applicatives</th>
                        <th class="p-4">Observations Élus</th>
                        <th class="p-4 text-center w-28">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($decisions as $dec)
                    <tr class="hover:bg-slate-50/80 transition">

                        <td class="p-4 whitespace-nowrap">
                            <div class="font-mono font-bold text-slate-900">
                                📅 {{ \Carbon\Carbon::parse($dec->date_commission)->format('d/m/Y') }}
                            </div>
                            <span class="text-[10px] text-slate-400 font-mono block mt-0.5 font-normal">ID délib:
                                #{{ $dec->id_decision }}</span>
                        </td>

                        <td class="p-4 whitespace-nowrap">
                            @php
                            $badge = match ($dec->statut_decision) {
                            'Validé', 'Approuvé' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'Refusé', 'Rejeté' => 'bg-rose-50 text-rose-700 border-rose-200',
                            'Ajourné', 'En attente' => 'bg-amber-50 text-amber-700 border-amber-200',
                            default => 'bg-slate-50 text-slate-700 border-slate-200'
                            };
                            @endphp
                            <span class="px-2.5 py-1 text-[11px] font-bold rounded-full border {{ $badge }}">
                                {{ $dec->statut_decision }}
                            </span>
                        </td>

                        <td class="p-4 space-y-1">
                            @if($dec->projet)
                            <div class="text-xs text-slate-500">
                                Projet <span class="font-mono text-slate-400 font-bold">[ID:
                                    {{ $dec->id_projet }}]</span> :
                                <span class="font-bold text-slate-900">📂 {{ $dec->projet->nom_projet }}</span>
                            </div>
                            @endif
                            @if($dec->intervention)
                            <div class="text-xs text-slate-500">
                                Intervention <span class="font-mono text-slate-400 font-bold">[ID:
                                    {{ $dec->id_int }}]</span> :
                                <span class="font-bold text-blue-600">🛠️
                                    {{ $dec->intervention->type_intervention }}</span>
                            </div>
                            @endif
                            @if($dec->operationComptable)
                            <div class="text-xs text-slate-500">
                                Écriture compta <span class="font-mono text-slate-400 font-bold">[ID:
                                    {{ $dec->id_operation }}]</span> :
                                <span
                                    class="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-slate-700 text-[10px] font-bold border border-slate-200">💳
                                    {{ $dec->operationComptable->numero_operation }}</span>
                            </div>
                            @endif
                            @if(!$dec->id_projet && !$dec->id_int && !$dec->id_operation)
                            <span class="text-xs text-slate-400 italic font-normal">Délibération d'ordre général</span>
                            @endif
                        </td>

                        <td class="p-4 text-xs text-slate-600 italic font-normal max-w-xs truncate"
                            title="{{ $dec->commentaire_elus }}">
                            {{ $dec->commentaire_elus ?? '—' }}
                        </td>

                        <td class="p-4 text-center whitespace-nowrap">
                            <div class="flex justify-center items-center space-x-3.5">
                                <a href="{{ route('decisions-commission.edit', $dec->id_decision) }}"
                                    class="text-amber-500 hover:text-amber-600 text-sm transform hover:scale-110 transition"
                                    title="Modifier l'arbitrage">
                                    ✏️
                                </a>

                                @can('check-permission', ['Conseil & Commissions', 'suppression'])
                                <form action="{{ route('decisions-commission.destroy', $dec->id_decision) }}"
                                    method="POST" class="inline-flex"
                                    onsubmit="return confirm('⚠️ Supprimer définitivement ce PV d\'arbitrage ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-400 hover:text-red-600 text-sm transform hover:scale-110 transition"
                                        title="Supprimer de l'ERP">
                                        🗑️
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-sm text-slate-400 italic bg-slate-50/30">
                            🔍 Aucun arbitrage de commission ne correspond à vos critères de recherche ou filtres
                            actuels.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($decisions->hasPages())
        <div class="p-4 border-t bg-slate-50">
            {{ $decisions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection