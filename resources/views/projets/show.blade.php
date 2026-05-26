@extends('layouts.app')

@section('title', 'Détails du Projet : ' . $projet->nom_projet)

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">

        <div
            class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-wrap justify-between items-center gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <span class="text-3xl">🏗️</span>
                    <h1 class="text-2xl font-bold text-slate-800">{{ $projet->nom_projet }}</h1>
                    <span
                        class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-blue-100 text-blue-700 uppercase tracking-wider">
                        {{ $projet->type_projet }}
                    </span>
                </div>
                <p class="text-slate-500 font-medium ml-11">Mandat {{ $projet->annee_mandat }}</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('projets.index') }}"
                    class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 transition">←
                    Retour</a>

                @if(auth()->user()->can('check-permission', ['Patrimoine & Travaux', 'ecriture']))
                    <a href="{{ route('projets.edit', $projet->id_projet) }}"
                        class="px-4 py-2 bg-amber-100 text-amber-700 border border-amber-200 text-sm font-semibold rounded-lg hover:bg-amber-200 transition">✏️
                        Modifier</a>
                @endif

                @if(auth()->user()->can('check-permission', ['Patrimoine & Travaux', 'suppression']))
                    <form action="{{ route('projets.destroy', $projet->id_projet) }}" method="POST"
                        onsubmit="return confirm('Confirmer la suppression ?');">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-100 text-red-700 border border-red-200 text-sm font-semibold rounded-lg hover:bg-red-200 transition">🗑️
                            Supprimer</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2 space-y-6">
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2">📋 Informations
                        du Projet</h3>

                    <div class="text-sm text-slate-700 bg-slate-50 p-4 rounded-lg border border-slate-100">
                        <span class="font-bold text-slate-900 block mb-1">Avis / Note :</span>
                        {{ $projet->avis ?? 'Aucun avis renseigné.' }}
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm mt-6">
                <div class="flex justify-between items-center mb-4 border-b pb-2">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">🛠️ Interventions liées</h3>

                    <a href="{{ route('interventions.create', ['projet_id' => $projet->id_projet]) }}"
                        class="px-3 py-1 bg-blue-600 text-white text-xs font-bold rounded-lg hover:bg-blue-700 transition">
                        + Ajouter une intervention
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="p-2 text-[10px] font-bold text-slate-500 uppercase">Description</th>
                                <th class="p-2 text-[10px] font-bold text-slate-500 uppercase">Date</th>
                                <th class="p-2 text-[10px] font-bold text-slate-500 uppercase">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($projet->interventions as $intervention)
                                <tr class="text-sm">
                                    <td class="p-2 text-slate-700">{{ $intervention->description ?? 'Intervention sans titre' }}
                                    </td>
                                    <td class="p-2 text-slate-500">{{ $intervention->date_intervention ?? '-' }}</td>
                                    <td class="p-2">
                                        <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-slate-100 text-slate-600">
                                            {{ $intervention->statut ?? 'En cours' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="p-4 text-center text-slate-400 italic text-xs">Aucune intervention
                                        enregistrée pour ce projet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2">📊 Données Clés
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <p class="text-xs text-slate-500 uppercase font-bold">Budget Alloué</p>
                            <p class="text-lg font-extrabold text-blue-900">
                                {{ $projet->budget_global_alloue ? number_format($projet->budget_global_alloue, 2, ',', ' ') . ' €' : 'Non défini' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase font-bold">Responsable</p>
                            <p class="text-sm font-semibold text-slate-800">
                                {{ $projet->chefProjet ? $projet->chefProjet->prenom_user . ' ' . $projet->chefProjet->nom_user : 'Non assigné' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection