@extends('layouts.app')

@section('header_title', 'Détail du contrôle')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">

        <div
            class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-wrap justify-between items-center gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-slate-900">{{ $controle->designation }}</h1>
                    @if($controle->est_legalement_obligatoire)
                        <span
                            class="px-2.5 py-1 text-xs font-bold rounded-full bg-red-100 text-red-800 border border-red-200">Obligation
                            Légale</span>
                    @endif
                </div>
                <p class="text-sm text-slate-500 mt-1 font-medium">Domaine :
                    {{ $controle->domaine_technique ?? 'Non spécifié' }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('controles.index') }}"
                    class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 transition">←
                    Retour</a>

                @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                    <a href="{{ route('controles.edit', $controle->id_controle) }}"
                        class="px-4 py-2 bg-amber-100 text-amber-800 border border-amber-200 text-sm font-semibold rounded-lg hover:bg-amber-200 transition">✏️
                        Modifier</a>
                @endcan

                @can('check-permission', ['Patrimoine & Equipements', 'suppression'])
                    <form action="{{ route('controles.destroy', $controle->id_controle) }}" method="POST"
                        onsubmit="return confirm('Confirmer la suppression ? Tous les liens avec les ERP seront perdus.');">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-100 text-red-700 border border-red-200 text-sm font-semibold rounded-lg hover:bg-red-200 transition">🗑️
                            Supprimer</button>
                    </form>
                @endcan
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2 space-y-6">
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2">📋 Détails
                        techniques</h3>

                    <div class="grid grid-cols-2 gap-6 text-sm">
                        <div>
                            <p class="text-slate-500 mb-1 font-semibold">Périodicité</p>
                            <p class="text-slate-800 font-medium bg-slate-50 p-2 rounded border border-slate-100">
                                {{ $controle->frequence_mois ? $controle->frequence_mois . ' mois' : 'Aucune fréquence fixe' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-slate-500 mb-1 font-semibold">Type de contrôle</p>
                            <p class="text-slate-800 font-medium bg-slate-50 p-2 rounded border border-slate-100">
                                {{ $controle->type_controle ?? 'Non spécifié' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-slate-500 mb-1 font-semibold">Intervenant exigé</p>
                            <p class="text-slate-800 font-medium bg-slate-50 p-2 rounded border border-slate-100">
                                {{ $controle->intervenant_prevu ?? 'Libre' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-slate-500 mb-1 font-semibold">Livrable attendu</p>
                            <p class="text-slate-800 font-medium bg-slate-50 p-2 rounded border border-slate-100">
                                📄 {{ $controle->type_document_attendu ?? 'Aucun document exigé' }}
                            </p>
                        </div>
                    </div>
                </div>
                <!-- BLOC DES ÉQUIPEMENTS SOUMIS AU CONTRÔLE -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm mt-6">
                    <h3
                        class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2 flex items-center justify-between">
                        <span>⚙️ Équipements soumis à ce contrôle</span>
                        <span
                            class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full text-xs">{{ $controle->equipements->count() }}</span>
                    </h3>

                    @if($controle->equipements->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr
                                        class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                        <th class="p-3">Équipement</th>
                                        <th class="p-3">Marque / Réf.</th>
                                        <th class="p-3">État actuel</th>
                                        <th class="p-3">Dernier contrôle</th>
                                        <th class="p-3 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-sm">
                                    @foreach($controle->equipements as $equipement)
                                        <tr class="hover:bg-slate-50/70 transition-colors">
                                            <td class="p-3">
                                                <span class="font-semibold text-slate-800">{{ $equipement->nom_equipement }}</span>
                                            </td>
                                            <td class="p-3 text-slate-600">
                                                {{ $equipement->marque ?? '-' }} <br>
                                                <span
                                                    class="text-xs text-slate-400 font-mono">{{ $equipement->reference_serie ?? 'Sans réf' }}</span>
                                            </td>
                                            <td class="p-3">
                                                @if($equipement->etat_fonctionnement == 'En service')
                                                    <span
                                                        class="px-2 py-1 text-[10px] font-bold rounded-md bg-green-50 text-green-700 border border-green-100">En
                                                        service</span>
                                                @elseif($equipement->etat_fonctionnement == 'En panne')
                                                    <span
                                                        class="px-2 py-1 text-[10px] font-bold rounded-md bg-red-50 text-red-700 border border-red-100">En
                                                        panne</span>
                                                @else
                                                    <span
                                                        class="px-2 py-1 text-[10px] font-bold rounded-md bg-slate-100 text-slate-700">{{ $equipement->etat_fonctionnement ?? 'Inconnu' }}</span>
                                                @endif
                                            </td>
                                            <td class="p-3 font-medium">
                                                @if($equipement->pivot->date_controle)
                                                    <span
                                                        class="{{ \Carbon\Carbon::parse($equipement->pivot->date_controle)->isPast() ? 'text-slate-800' : 'text-slate-800' }}">
                                                        {{ \Carbon\Carbon::parse($equipement->pivot->date_controle)->format('d/m/Y') }}
                                                    </span>
                                                @else
                                                    <span class="text-slate-400 italic text-xs">Aucune date renseignée</span>
                                                @endif
                                            </td>
                                            <td class="p-3 text-right">
                                                <a href="{{ route('equipements.show', $equipement->id_equipement) }}"
                                                    class="text-blue-600 hover:text-blue-800 font-medium text-xs">
                                                    Consulter →
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8 bg-slate-50/50 rounded-lg border border-dashed border-slate-200">
                            <p class="text-sm text-slate-500 italic">Aucun équipement de la commune n'est actuellement rattaché
                                à ce contrôle.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div>
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm h-full">
                    <h3
                        class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2 flex items-center justify-between">
                        <span>🏢 ERP Soumis</span>
                        <span
                            class="bg-blue-100 text-blue-700 py-0.5 px-2 rounded-full text-xs">{{ $controle->typesErp->count() }}</span>
                    </h3>

                    @if($controle->typesErp->count() > 0)
                        <ul class="space-y-3">
                            @foreach($controle->typesErp as $erp)
                                <li class="p-3 bg-slate-50 border border-slate-100 rounded-lg text-sm">
                                    <div class="font-bold text-slate-800">Catégorie {{ $erp->categorie_erp }} - Type
                                        {{ $erp->type_erp }}
                                    </div>
                                    <div class="text-xs text-slate-500 mt-1">{{ $erp->reglementation_applicable }}</div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-6">
                            <p class="text-sm text-slate-500 italic">Ce contrôle n'est actuellement rattaché à aucun type d'ERP.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection