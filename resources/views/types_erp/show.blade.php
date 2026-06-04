@extends('layouts.app')

@section('header_title', 'Détail de la classification ERP')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6 pb-12">

        {{-- BARRE DE TITRE & ACTIONS METIERS --}}
        <div
            class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-wrap justify-between items-center gap-4">
            <div class="flex items-center gap-4">
                <div
                    class="w-16 h-16 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 flex flex-col items-center justify-center shadow-sm select-none">
                    <span class="text-[10px] font-black uppercase tracking-widest opacity-70">Cat.</span>
                    <span class="text-2xl font-black leading-none mt-1">{{ $type_erp->categorie_erp ?? '?' }}</span>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Type
                        {{ $type_erp->type_erp ?? 'Non défini' }}
                    </h1>
                    <p class="text-sm text-slate-500 mt-1 font-medium">{{ $type_erp->public_cible ?? 'Public non précisé' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('types-erp.index') }}"
                    class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 transition shadow-sm">←
                    Retour</a>

                @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                    <a href="{{ route('types-erp.edit', $type_erp->id_type_erp) }}"
                        class="px-4 py-2 bg-amber-50 text-amber-700 border border-amber-200 text-sm font-bold rounded-lg hover:bg-amber-100 transition shadow-sm">✏️
                        Modifier</a>
                @endcan

                @can('check-permission', ['Patrimoine & Equipements', 'suppression'])
                    <form action="{{ route('types-erp.destroy', $type_erp->id_type_erp) }}" method="POST"
                        onsubmit="return confirm('Attention ! Confirmer la suppression de cette catégorie ERP ?');">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-50 text-red-700 border border-red-200 text-sm font-bold rounded-lg hover:bg-red-100 transition shadow-sm">🗑️
                            Supprimer</button>
                    </form>
                @endcan
            </div>
        </div>

        {{-- REGLEMENTATION APPLICABLE --}}
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">⚖️ Réglementation Applicable</h3>
            <p class="text-slate-700 bg-slate-50 p-4 rounded-lg border border-slate-100 font-medium leading-relaxed">
                {{ $type_erp->reglementation_applicable }}
            </p>
        </div>

        {{-- COEUR DES DONNÉES : GRILLE EN 2 COLONNES --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- BLOC GAUCHE : OBLIGATIONS ET DATES METIERS DE COMPORTEMENT --}}
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h3
                    class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2 flex justify-between items-center">
                    <span>📋 Contrôles Réglementaires Associés</span>
                    <span
                        class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full text-xs font-bold">{{ $type_erp->controles->count() }}</span>
                </h3>

                @if($type_erp->controles->count() > 0)
                    <ul class="space-y-3">
                        @foreach($type_erp->controles as $controle)
                            <li
                                class="flex flex-col gap-3 p-4 bg-slate-50 border border-slate-200 rounded-xl hover:bg-slate-100/50 transition">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <a href="{{ route('controles.show', $controle->id_controle) }}"
                                            class="font-extrabold text-blue-600 hover:text-blue-800 text-sm tracking-tight leading-tight block">
                                            {{ $controle->designation }}
                                        </a>
                                        <p class="text-[11px] text-slate-400 font-bold mt-1 uppercase tracking-wide">
                                            Domaine : {{ $controle->domaine_technique ?? 'Général' }} — Fréquence :
                                            {{ $controle->frequence_mois ? $controle->frequence_mois . ' mois' : 'Ponctuel' }}
                                        </p>
                                    </div>
                                    @if($controle->est_legalement_obligatoire)
                                        <span
                                            class="text-[9px] font-black text-red-700 bg-red-100 border border-red-200 px-2 py-0.5 rounded uppercase tracking-wider select-none">Obligatoire</span>
                                    @endif
                                </div>

                                {{-- 🚀 AFFICHAGE DE LA COLONNE NATIVE EXTENSION DU PIVOT --}}
                                <div class="pt-2 border-t border-slate-200/60 flex justify-between items-center text-xs">
                                    <span class="text-slate-400 font-semibold uppercase tracking-wide text-[11px]">Dernière
                                        inspection officielle :</span>
                                    <span
                                        class="font-bold text-slate-700 bg-white border px-2 py-0.5 rounded shadow-sm font-mono text-[11px]">
                                        @if($controle->pivot->date_controle)
                                            📅 {{ \Carbon\Carbon::parse($controle->pivot->date_controle)->format('d/m/Y') }}
                                        @else
                                            ⏳ Non planifié / À réaliser
                                        @endif
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-slate-400 italic py-8 text-center">Aucun contrôle n'est paramétré pour ce type d'ERP.
                    </p>
                @endif
            </div>

            {{-- BLOC DROIT : PATRIMOINE BÂTI DE LA COMMUNE --}}
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h3
                    class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2 flex justify-between items-center">
                    <span>🏛️ Patrimoine Communal Classé</span>
                    <span
                        class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full text-xs font-bold">{{ $batiments->count() + $lieux->count() }}</span>
                </h3>

                @if($batiments->count() > 0 || $lieux->count() > 0)
                    <ul class="space-y-2">
                        @foreach($batiments as $bat)
                            <li>
                                <a href="{{ route('batiments.show', $bat->id_batiment) }}"
                                    class="flex items-center gap-2.5 p-3 hover:bg-slate-50 rounded-xl text-sm border border-slate-100 bg-white shadow-sm hover:border-slate-200 transition">
                                    <span class="text-base">🏢</span> <span
                                        class="font-bold text-slate-700 hover:text-blue-600 transition">{{ $bat->nom_bat }}</span>
                                </a>
                                </td>
                        @endforeach
                            @foreach($lieux as $lieu)
                                <li>
                                    <a href="{{ route('lieux.show', $lieu->id_lieu) }}"
                                        class="flex items-center gap-2.5 p-3 hover:bg-slate-50 rounded-xl text-sm border border-slate-100 bg-white shadow-sm hover:border-slate-200 transition">
                                        <span class="text-base">🌳</span> <span
                                            class="font-bold text-slate-700 hover:text-blue-600 transition">{{ $lieu->nom_lieu }}</span>
                                    </a>
                                </li>
                            @endforeach
                    </ul>
                @else
                    <p class="text-sm text-slate-400 italic py-8 text-center">Aucun bâtiment ou lieu public n'est rattaché à cet
                        ERP.</p>
                @endif
            </div>
        </div>

    </div>
@endsection