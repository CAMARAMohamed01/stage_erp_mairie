@extends('layouts.app')

@section('header_title', 'Ouvrir un Dossier d\'Urbanisme')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6 pb-12">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">📄 Enregistrer un nouveau dossier d'urbanisme</h1>
            <a href="{{ route('dossiers-urba.index') }}"
                class="text-sm font-semibold text-slate-500 hover:text-slate-800 transition">← Retour au registre</a>
        </div>

        <form action="{{ route('dossiers-urba.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-6">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b pb-2">📋 Références
                    Administratives</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Numéro unique du dossier *</label>
                        <input type="text" name="numero_dossier" value="{{ old('numero_dossier') }}" required
                            placeholder="Ex: PC 074012 26 00015"
                            class="w-full border border-slate-300 rounded-lg p-2.5 font-mono text-sm uppercase focus:ring-2 focus:ring-indigo-500">
                        @error('numero_dossier') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Type de dossier *</label>
                        <select name="type_dossier_CU_DP_" required
                            class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500 bg-white">
                            <option value="PC">Permis de Construire (PC)</option>
                            <option value="DP">Déclaration Préalable (DP)</option>
                            <option value="CU">Certificat d'Urbanisme (CU)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Statut initial de l'instruction
                            *</label>
                        <select name="nature_decision" required
                            class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500 bg-white">
                            <option value="En cours d'instruction">En cours d'instruction</option>
                            <option value="Accordé">Accordé / Validé</option>
                            <option value="Refusé">Refusé</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Date de dépôt en mairie *</label>
                        <input type="date" name="date_depot" value="{{ old('date_depot', date('Y-m-d')) }}" required
                            class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Date limite d'instruction</label>
                        <input type="date" name="date_limite_instruction" value="{{ old('date_limite_instruction') }}"
                            class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Agent Instructeur Référent</label>
                        <select name="id_user_instructeur"
                            class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500 bg-white">
                            <option value="">-- Assigner un agent --</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id_user }}">{{ $agent->nom_user }} {{ $agent->prenom_user }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Pétitionnaire / Demandeur
                            (Tiers)</label>
                        <select name="id_tiers"
                            class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500 bg-white">
                            <option value="">-- Choisir le demandeur --</option>
                            @foreach($tiers as $t)
                                <option value="{{ $t->id_tiers }}">
                                    [{{ $t->type_tiers }}]
                                    {{ $t->type_tiers === 'Physique' ? $t->nom_tiers . ' ' . $t->prenom_tiers : $t->raison_sociale }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Arrêté municipal lié (si déjà
                            statué)</label>
                        <select name="id_acte_decision"
                            class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500 bg-white">
                            <option value="">-- Aucun arrêté lié --</option>
                            @foreach($decisions as $dec)
                                <option value="{{ $dec->id_decision }}">N°{{ $dec->numero_decision }} du
                                    {{ \Carbon\Carbon::parse($dec->date_decision)->format('d/m/Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-6">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b pb-2">🏗️ Caractéristiques
                    Techniques du Projet</h3>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Objet détaillé des travaux *</label>
                    <textarea name="objet_travaux" required rows="3"
                        placeholder="Ex: Construction d'une maison d'habitation individuelle avec garage attenant et création d'un accès de voirie..."
                        class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500">{{ old('objet_travaux') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Surface de Plancher créée
                            (m²)</label>
                        <input type="number" step="0.01" name="surface_plancher_m2" value="{{ old('surface_plancher_m2') }}"
                            placeholder="Ex: 145.50"
                            class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Hauteur maximale de la construction
                            (m)</label>
                        <input type="number" step="0.01" name="hauteur_construction"
                            value="{{ old('hauteur_construction') }}" placeholder="Ex: 7.20"
                            class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <label>📍 Parcelles cadastrales d'implantation </label>
                <p class="text-xs text-slate-400 mb-4">Sélectionnez la ou les parcelles concernées par le projet de
                    construction (maintenez Ctrl enfoncé pour en sélectionner plusieurs) :</p>

                <select name="parcelles[]" multi-select multiple required
                    class="w-full border border-slate-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 h-44 bg-white">
                    @foreach($parcelles as $parcelle)
                        <option value="{{ $parcelle->id_parcelle }}">Section {{ $parcelle->section_cadastrale }} -
                            N°{{ $parcelle->num_parcelle }} (Lieu-dit : {{ $parcelle->lieuDit->nom_lieu_dit ?? 'Inconnu' }})
                        </option>
                    @endforeach
                </select>
                @error('parcelles') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('dossiers-urba.index') }}"
                    class="px-6 py-2.5 border border-slate-300 text-slate-700 font-semibold rounded-lg hover:bg-slate-50 transition">Annuler</a>
                <button type="submit"
                    class="px-6 py-2.5 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition shadow-sm">💾
                    Ouvrir l'instruction du dossier</button>
            </div>
        </form>
    </div>
@endsection