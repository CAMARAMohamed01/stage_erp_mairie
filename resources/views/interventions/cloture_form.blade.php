@extends('layouts.app')

@section('title', 'Clôture de l\'intervention #' . $intervention->id_int)

@section('content')
    <div class="max-w-5xl mx-auto">

        <div class="mb-6">
            <a href="{{ route('interventions.show', $intervention->id_int) }}"
                class="text-slate-500 hover:text-slate-800 text-sm flex items-center font-medium transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Retour à la fiche
            </a>
        </div>

        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Rapport d'intervention</h1>
            <p class="text-slate-500 mt-2 text-sm">Veuillez détailler les actions réalisées sur le terrain pour alimenter le
                suivi ou clôturer le dossier.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2">
                <form action="{{ route('interventions.cloturer.save', $intervention->id_int) }}" method="POST"
                    class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    @csrf
                    @method('PATCH')

                    <div class="p-8 space-y-8">
                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-2">Observations et travaux réalisés
                                <span class="text-red-500">*</span></label>
                            <textarea name="compte_rendu" rows="6" required
                                class="w-full border border-slate-300 rounded-lg shadow-sm px-4 py-3 focus:ring-blue-500 focus:border-blue-500 text-slate-700 bg-slate-50 focus:bg-white transition"
                                placeholder="Détaillez les actions menées, le matériel utilisé, les problèmes rencontrés..."></textarea>
                        </div>

                        {{-- ENCADRÉ COMPLEMENTAIRE : TEMPS, DATE ET COÛT --}}
                        <div
                            class="grid grid-cols-1 md:grid-cols-3 gap-4 p-6 bg-slate-50 rounded-lg border border-slate-100">
                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Temps passé (h)
                                </label>
                                <input type="number" name="temps_passe" step="0.25" placeholder="ex: 1.5" min="0"
                                    class="w-full text-xs border-slate-300 rounded-lg shadow-sm px-3 py-2.5 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                    Coût associé (€)
                                </label>
                                <input type="number" name="cout_associe" step="0.01" placeholder="ex: 150.00" min="0"
                                    value="0.00"
                                    class="w-full text-xs border-slate-300 rounded-lg shadow-sm px-3 py-2.5 focus:ring-blue-500 focus:border-blue-500 bg-white font-semibold text-slate-800">
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    Date d'achèvement <span class="text-red-500 ml-0.5">*</span>
                                </label>
                                <input type="date" name="date_cloture" value="{{ date('Y-m-d') }}" required
                                    class="w-full text-xs border-slate-300 rounded-lg shadow-sm px-3 py-2.5 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-800 mb-2">Bilan de l'intervention</label>
                                <select name="resultat"
                                    class="w-full border-slate-300 rounded-lg shadow-sm px-4 py-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-medium">
                                    <option value="Succès">✔️ Action résolue avec succès</option>
                                    <option value="Partiel">⚠️ Action résolue partiellement</option>
                                    <option value="Echec">❌ Non résolu / Autre corps de métier nécessaire</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-800 mb-2">Statut du dossier</label>
                                <select name="statut_final"
                                    class="w-full border border-green-500 rounded-lg shadow-sm px-4 py-2 focus:ring-green-500 focus:border-green-500 text-sm font-bold text-green-700 bg-green-50">
                                    <option value="Terminé">Clôturer le dossier</option>
                                    <option value="En cours">Maintenir "En cours"</option>
                                    <option value="En attente">Mettre "En attente"</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="px-8 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                        <p class="text-xs text-slate-400 italic">La saisie d'un coût impactera directement la comptabilité
                            analytique de l'opération.</p>
                        <button type="submit"
                            class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition shadow-sm flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Enregistrer le rapport
                        </button>
                    </div>
                </form>
            </div>

            <div class="lg:col-span-1 space-y-6">
                <div class="bg-slate-800 rounded-xl shadow-sm p-6 text-white">
                    <h3
                        class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4 border-b border-slate-700 pb-2">
                        Rappel du dossier</h3>

                    <div class="space-y-4">
                        <div>
                            <p class="text-xs text-slate-400 mb-1">Réf. Intervention</p>
                            <p class="font-bold text-lg">#{{ $intervention->id_int }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 mb-1">Type d'opération</p>
                            <p class="font-medium">{{ $intervention->type_intervention }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 mb-1">Date d'ouverture</p>
                            <p class="font-medium">
                                {{ \Carbon\Carbon::parse($intervention->date_ouverture)->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-slate-800 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Demande initiale
                    </h3>
                    <p class="text-sm text-slate-600 bg-slate-50 p-3 rounded border border-slate-100 italic">
                        "{{ $intervention->description }}"
                    </p>
                </div>
            </div>

        </div>
    </div>
@endsection