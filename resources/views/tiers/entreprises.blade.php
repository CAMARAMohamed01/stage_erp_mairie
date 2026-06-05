@extends('layouts.app')

@section('title', 'Annuaire des Prestataires & Entreprises')

@section('content')
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">

        <div class="bg-slate-900 px-6 py-5 flex justify-between items-center">
            <div>
                <h1 class="text-xl font-bold text-white flex items-center">
                    <svg class="w-5 h-5 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                    Annuaire des Prestataires (Entreprises)
                </h1>
                <p class="text-slate-400 text-sm mt-1">Base de données des fournisseurs et Tiers Moraux</p>
            </div>
            <div>
                <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-bold">
                    {{ $entreprises->count() }} entreprise(s)
                </span>
                <a href="{{ route('tiers.create_entreprise') }}"
                    class="ml-4 bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded text-sm font-bold shadow-sm transition">
                    + Nouvel Entreprise
                </a>
            </div>
        </div>

        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
            <form action="{{ route('tiers.entreprises') }}" method="GET" class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Rechercher par raison sociale, SIRET, contact ou email..."
                    class="text-sm border-slate-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block p-2 bg-white w-full md:w-96 shadow-sm">

                <button type="submit"
                    class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-medium hover:bg-slate-700 transition">Rechercher</button>

                @if(request('search'))
                    <a href="{{ route('tiers.entreprises') }}"
                        class="text-xs text-red-600 hover:underline font-medium">Réinitialiser</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold">ID</th>
                        <th class="px-6 py-4 font-semibold">Entreprise & SIRET</th>
                        <th class="px-6 py-4 font-semibold">Contact Principal</th>
                        <th class="px-6 py-4 font-semibold">Infos Bancaires</th>
                        <th class="px-6 py-4 font-semibold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($entreprises as $entreprise)
                        <tr class="hover:bg-slate-50 transition-colors text-sm">
                            <td class="px-6 py-4 font-bold text-slate-400">#{{ $entreprise->id_tiers }}</td>

                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 text-base">
                                    {{ $entreprise->morale->raison_sociale ?? 'Nom manquant' }}
                                </div>
                                <div class="text-slate-500 text-xs mt-1 font-mono">SIRET:
                                    {{ $entreprise->morale->siret ?? 'Non renseigné' }}
                                </div>
                                @if($entreprise->morale->numero_tva_intra)
                                    <div class="text-slate-400 text-[10px]">TVA: {{ $entreprise->morale->numero_tva_intra }}</div>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                @if($entreprise->morale->nom_contact)
                                    <div class="font-semibold text-slate-700 mb-1">👤 {{ $entreprise->morale->nom_contact }}</div>
                                @endif

                                @if($entreprise->tel_tiers)
                                    <div class="text-slate-600 text-xs">📞 {{ $entreprise->tel_tiers }}</div>
                                @endif

                                @if($entreprise->email_tiers)
                                    <div class="text-slate-600 text-xs">✉️ {{ $entreprise->email_tiers }}</div>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                @forelse($entreprise->comptesBancaires as $compte)
                                    <div class="bg-white border border-slate-200 rounded p-2 mb-2 shadow-sm">
                                        <div class="flex items-center justify-between">
                                            <div class="font-mono text-xs font-bold text-slate-700">{{ $compte->iban }}</div>
                                            @if($compte->documents && $compte->documents->count() > 0)
                                                <span title="RIB/Document joint" class="text-blue-500 cursor-help">📎</span>
                                            @endif
                                        </div>
                                        <div class="text-[10px] text-slate-400 mt-1">BIC: {{ $compte->bic }}</div>
                                    </div>
                                @empty
                                    <span class="text-xs text-amber-600 bg-amber-50 px-2 py-1 rounded border border-amber-200">Aucun
                                        compte bancaire</span>
                                @endforelse
                            </td>

                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('tiers.show_entreprise', $entreprise->id_tiers) }}"
                                        class="text-blue-600 hover:text-emerald-800 font-medium bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded transition">
                                        Voir le dossier
                                    </a>
                                </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500 italic">
                                Aucune entreprise ne correspond à votre recherche.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection