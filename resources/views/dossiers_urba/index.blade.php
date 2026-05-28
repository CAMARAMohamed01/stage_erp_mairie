@extends('layouts.app')

@section('header_title', 'Instruction des dossiers d\'Urbanisme')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        <div class="flex flex-wrap justify-between items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Dossiers d'Urbanisme</h1>
                <p class="text-sm text-slate-500 mt-1">Suivi et instruction des permis de construire (PC), déclarations
                    préalables (DP) et certificats d'urbanisme (CU).</p>
            </div>
            <div>
                @can('check-permission', ['Urbanisme', 'ecriture'])
                    <a href="{{ route('dossiers-urba.create') }}"
                        class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition flex items-center gap-2">
                        ➕ Ouvrir un dossier
                    </a>
                @endcan
            </div>
        </div>

        <form action="{{ route('dossiers-urba.index') }}" method="GET" class="flex flex-wrap gap-2 mb-6">

            <div class="relative flex-grow max-w-sm">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="N° de dossier, objet des travaux..."
                    class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 shadow-sm transition text-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <select name="type_dossier"
                class="border border-slate-300 rounded-lg py-2 px-3 text-sm focus:ring-2 focus:ring-indigo-500 shadow-sm bg-white">
                <option value="">Tous les types d'actes</option>
                <option value="PC" {{ request('type_dossier') == 'PC' ? 'selected' : '' }}>Permis de Construire (PC)
                </option>
                <option value="DP" {{ request('type_dossier') == 'DP' ? 'selected' : '' }}>Déclaration Préalable (DP)
                </option>
                <option value="CU" {{ request('type_dossier') == 'CU' ? 'selected' : '' }}>Certificat d'Urbanisme (CU)
                </option>
            </select>

            <select name="statut"
                class="border border-slate-300 rounded-lg py-2 px-3 text-sm focus:ring-2 focus:ring-indigo-500 shadow-sm bg-white">
                <option value="">Tous les statuts</option>
                <option value="En cours d'instruction" {{ request('statut') == "En cours d'instruction" ? 'selected' : '' }}>
                    En cours d'instruction</option>
                <option value="Accordé" {{ request('statut') == 'Accordé' ? 'selected' : '' }}>Accordé / Validé</option>
                <option value="Refusé" {{ request('statut') == 'Refusé' ? 'selected' : '' }}>Refusé</option>
            </select>

            <button type="submit"
                class="px-4 py-2 bg-slate-800 text-white text-sm font-bold rounded-lg hover:bg-slate-700 transition">
                Filtrer
            </button>

            @if(request()->anyFilled(['search', 'type_dossier', 'statut']))
                <a href="{{ route('dossiers-urba.index') }}"
                    class="px-4 py-2 bg-slate-100 text-slate-600 text-sm font-bold rounded-lg hover:bg-slate-200 transition">
                    Réinitialiser
                </a>
            @endif
        </form>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="p-4">N° Dossier / Objet</th>
                        <th class="p-4 text-center">Type d'acte</th>
                        <th class="p-4">Demandeur (Pétitionnaire)</th>
                        <th class="p-4 text-center">Déposé le</th>
                        <th class="p-4 text-center">Statut décision</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($dossiers as $dossier)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="p-4">
                                <p class="font-mono font-bold text-slate-800">{{ $dossier->numero_dossier }}</p>
                                <p class="text-xs text-slate-500 max-w-sm truncate mt-0.5">
                                    {{ $dossier->objet_travaux ?? 'Non spécifié' }}
                                </p>
                            </td>
                            <td class="p-4 text-center">
                                <span
                                    class="px-2.5 py-1 text-xs font-bold rounded-md bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    {{ $dossier->type_dossier_CU_DP_ }}
                                </span>
                            </td>
                            <td class="p-4 font-medium text-slate-700">
                                @if($dossier->demandeur)
                                    @if($dossier->demandeur->type_tiers === 'Physique')
                                        {{ $dossier->demandeur->physique?->prenom_tiers }}
                                        {{ $dossier->demandeur->physique?->nom_tiers ?? 'Citoyen inconnu' }}
                                    @else
                                        {{ $dossier->demandeur->morale?->raison_sociale ?? 'Entreprise/Structure inconnue' }}
                                    @endif
                                @else
                                    <span class="text-slate-400 italic text-xs">Non renseigné</span>
                                @endif
                            </td>
                            <td class="p-4 text-center text-slate-500">
                                {{ $dossier->date_depot ? $dossier->date_depot->format('d/m/Y') : '—' }}
                            </td>
                            <td class="p-4 text-center">
                                @if($dossier->nature_decision === 'Accordé')
                                    <span
                                        class="px-2.5 py-0.5 text-xs font-bold rounded bg-green-50 text-green-700 border border-green-100">Accordé</span>
                                @elseif($dossier->nature_decision === 'Refusé')
                                    <span
                                        class="px-2.5 py-0.5 text-xs font-bold rounded bg-red-50 text-red-700 border border-red-100">Refusé</span>
                                @else
                                    <span
                                        class="px-2.5 py-0.5 text-xs font-bold rounded bg-amber-50 text-amber-700 border border-amber-100">Instruction</span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('dossiers-urba.show', $dossier->id_dossier) }}"
                                    class="inline-flex items-center px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-md transition shadow-sm">
                                    Instruire →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400 italic bg-slate-50/50">
                                Aucun dossier d'urbanisme en cours ou enregistré.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection