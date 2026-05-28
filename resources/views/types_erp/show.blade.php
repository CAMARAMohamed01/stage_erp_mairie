@extends('layouts.app')

@section('header_title', 'Détail de la classification ERP')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6 pb-12">

        <div
            class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-wrap justify-between items-center gap-4">
            <div class="flex items-center gap-4">
                <div
                    class="w-16 h-16 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 flex flex-col items-center justify-center">
                    <span class="text-xs font-bold uppercase tracking-widest opacity-80">Cat.</span>
                    <span class="text-xl font-black">{{ $type_erp->categorie_erp ?? '?' }}</span>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Type {{ $type_erp->type_erp ?? 'Non défini' }}</h1>
                    <p class="text-sm text-slate-500 mt-1 font-medium">{{ $type_erp->public_cible ?? 'Public non précisé' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('types-erp.index') }}"
                    class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 transition">←
                    Retour</a>

                @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                    <a href="{{ route('types-erp.edit', $type_erp->id_type_erp) }}"
                        class="px-4 py-2 bg-amber-100 text-amber-800 border border-amber-200 text-sm font-semibold rounded-lg hover:bg-amber-200 transition">✏️
                        Modifier</a>
                @endcan

                @can('check-permission', ['Patrimoine & Equipements', 'suppression'])
                    <form action="{{ route('types-erp.destroy', $type_erp->id_type_erp) }}" method="POST"
                        onsubmit="return confirm('Attention ! Confirmer la suppression de cette catégorie ERP ?');">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-100 text-red-700 border border-red-200 text-sm font-semibold rounded-lg hover:bg-red-200 transition">🗑️
                            Supprimer</button>
                    </form>
                @endcan
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-2">⚖️ Réglementation Applicable</h3>
            <p class="text-slate-700 bg-slate-50 p-4 rounded-lg border border-slate-100">
                {{ $type_erp->reglementation_applicable }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h3
                    class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2 flex justify-between items-center">
                    <span>📋 Contrôles Réglementaires Associés</span>
                    <span
                        class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full text-xs">{{ $type_erp->controles->count() }}</span>
                </h3>

                @if($type_erp->controles->count() > 0)
                    <ul class="space-y-3">
                        @foreach($type_erp->controles as $controle)
                            <li class="flex items-start justify-between gap-3 p-3 bg-slate-50 border border-slate-100 rounded-lg">
                                <div>
                                    <a href="{{ route('controles.show', $controle->id_controle) }}"
                                        class="font-bold text-blue-600 hover:underline text-sm">{{ $controle->designation }}</a>
                                    <p class="text-xs text-slate-500 mt-1">{{ $controle->domaine_technique ?? 'Général' }} |
                                        {{ $controle->frequence_mois ? $controle->frequence_mois . ' mois' : 'Ponctuel' }}
                                    </p>
                                </div>
                                @if($controle->est_legalement_obligatoire)
                                    <span class="text-[10px] font-bold text-red-700 bg-red-100 px-2 py-1 rounded">Obligatoire</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-slate-500 italic py-4 text-center">Aucun contrôle n'est paramétré pour ce type d'ERP.
                    </p>
                @endif
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h3
                    class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2 flex justify-between items-center">
                    <span>🏛️ Patrimoine Communal Classé</span>
                    <span
                        class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full text-xs">{{ $batiments->count() + $lieux->count() }}</span>
                </h3>

                @if($batiments->count() > 0 || $lieux->count() > 0)
                    <ul class="space-y-2">
                        @foreach($batiments as $bat)
                            <li>
                                <a href="{{ route('batiments.show', $bat->id_batiment) }}"
                                    class="flex items-center gap-2 p-2 hover:bg-slate-50 rounded-lg text-sm border border-transparent hover:border-slate-100 transition">
                                    <span>🏢</span> <span class="font-semibold text-slate-700">{{ $bat->nom_bat }}</span>
                                </a>
                            </li>
                        @endforeach
                        @foreach($lieux as $lieu)
                            <li>
                                <a href="{{ route('lieux.show', $lieu->id_lieu) }}"
                                    class="flex items-center gap-2 p-2 hover:bg-slate-50 rounded-lg text-sm border border-transparent hover:border-slate-100 transition">
                                    <span>🌳</span> <span class="font-semibold text-slate-700">{{ $lieu->nom_lieu }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-slate-500 italic py-4 text-center">Aucun bâtiment ou lieu public n'est rattaché à cet
                        ERP.</p>
                @endif
            </div>
        </div>

    </div>
@endsection