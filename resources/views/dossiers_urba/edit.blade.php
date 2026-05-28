@extends('layouts.app')

@section('header_title', 'Modifier le dossier d\'Urbanisme')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6 pb-12">
        <!-- À insérer au début de vos vues pour démasquer les bugs -->
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl font-semibold text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl font-semibold text-sm">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl text-sm space-y-1">
                <p class="font-bold">🛑 Problème de validation :</p>
                <ul class="list-disc pl-5 text-xs font-medium">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">✏️ Modifier l'instruction du dossier :
                {{ $dossier->numero_dossier }}
            </h1>
            <a href="{{ route('dossiers-urba.show', $dossier->id_dossier) }}"
                class="text-sm font-semibold text-slate-500 hover:text-slate-800 transition">← Retour</a>
        </div>


        <form action="{{ route('dossiers-urba.update', $dossier->id_dossier) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-6">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b pb-2">📋 Informations
                    d'Instruction</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Numéro unique du dossier *</label>
                        <input type="text" name="numero_dossier"
                            value="{{ old('numero_dossier', $dossier->numero_dossier) }}" required
                            class="w-full border border-slate-300 rounded-lg p-2.5 font-mono text-sm uppercase">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Type de dossier *</label>
                        <select name="type_dossier_CU_DP_" required
                            class="w-full border border-slate-300 rounded-lg p-2.5 bg-white">
                            <option value="PC" {{ $dossier->type_dossier_CU_DP_ == 'PC' ? 'selected' : '' }}>Permis de
                                Construire (PC)</option>
                            <option value="DP" {{ $dossier->type_dossier_CU_DP_ == 'DP' ? 'selected' : '' }}>Déclaration
                                Préalable (DP)</option>
                            <option value="CU" {{ $dossier->type_dossier_CU_DP_ == 'CU' ? 'selected' : '' }}>Certificat
                                d'Urbanisme (CU)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Décision de la commission *</label>
                        <select name="nature_decision" required
                            class="w-full border border-slate-300 rounded-lg p-2.5 bg-white">
                            <option value="En cours d'instruction" {{ $dossier->nature_decision == "En cours d'instruction" ? 'selected' : '' }}>En cours
                                d'instruction</option>
                            <option value="Accordé" {{ $dossier->nature_decision == 'Accordé' ? 'selected' : '' }}>Accordé /
                                Validé</option>
                            <option value="Refusé" {{ $dossier->nature_decision == 'Refusé' ? 'selected' : '' }}>Refusé
                            </option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Date de dépôt *</label>
                        <input type="date" name="date_depot"
                            value="{{ old('date_depot', $dossier->date_depot ? $dossier->date_depot->format('Y-m-d') : '') }}"
                            required class="w-full border border-slate-300 rounded-lg p-2.5">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Date de la décision (si
                            actée)</label>
                        <input type="date" name="date_decision"
                            value="{{ old('date_decision', $dossier->date_decision ? $dossier->date_decision->format('Y-m-d') : '') }}"
                            class="w-full border border-slate-300 rounded-lg p-2.5">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Agent Instructeur Référent</label>
                        <select name="id_user_instructeur" class="w-full border border-slate-300 rounded-lg p-2.5 bg-white">
                            <option value="">-- Assigner un agent --</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id_user }}" {{ $dossier->id_user_instructeur == $agent->id_user ? 'selected' : '' }}>
                                    {{ $agent->nom_user }} {{ $agent->prenom_user }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Pétitionnaire / Demandeur</label>
                        <select name="id_tiers" class="w-full border border-slate-300 rounded-lg p-2.5 bg-white">
                            @foreach($tiers as $t)
                                <option value="{{ $t->id_tiers }}" {{ $dossier->id_tiers == $t->id_tiers ? 'selected' : '' }}>
                                    [{{ $t->type_tiers }}]
                                    {{ $t->type_tiers === 'Physique' ? $t->nom_tiers . ' ' . $t->prenom_tiers : $t->raison_sociale }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Arrêté municipal d'autorisation
                            lié</label>
                        <select name="id_acte_decision" class="w-full border border-slate-300 rounded-lg p-2.5 bg-white">
                            <option value="">-- Aucun arrêté lié --</option>
                            @foreach($decisions as $dec)
                                <option value="{{ $dec->id_decision }}" {{ $dossier->id_acte_decision == $dec->id_decision ? 'selected' : '' }}>
                                    N°{{ $dec->numero_decision }} du
                                    {{ \Carbon\Carbon::parse($dec->date_decision)->format('d/m/Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-6">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b pb-2">🏗️ Caractéristiques
                    Techniques</h3>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Objet détaillé des travaux *</label>
                    <textarea name="objet_travaux" required rows="3"
                        class="w-full border border-slate-300 rounded-lg p-2.5">{{ old('objet_travaux', $dossier->objet_travaux) }}</textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Surface de Plancher créée
                            (m²)</label>
                        <input type="number" step="0.01" name="surface_plancher_m2"
                            value="{{ old('surface_plancher_m2', $dossier->surface_plancher_m2) }}"
                            class="w-full border border-slate-300 rounded-lg p-2.5">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Hauteur maximale de la construction
                            (m)</label>
                        <input type="number" step="0.01" name="hauteur_construction"
                            value="{{ old('hauteur_construction', $dossier->hauteur_construction) }}"
                            class="w-full border border-slate-300 rounded-lg p-2.5">
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-2 border-b pb-2">📍 Parcelles
                    cadastrales d'implantation *</h3>
                <select name="parcelles[]" multiple required
                    class="w-full border border-slate-300 rounded-lg p-3 h-44 bg-white">
                    @foreach($parcelles as $parcelle)
                        <option value="{{ $parcelle->id_parcelle }}" {{ in_array($parcelle->id_parcelle, $parcelles_liees) ? 'selected' : '' }}>
                            Section {{ $parcelle->section_cadastrale }} - N°{{ $parcelle->num_parcelle }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('dossiers-urba.show', $dossier->id_dossier) }}"
                    class="px-6 py-2.5 border border-slate-300 text-slate-700 font-semibold rounded-lg hover:bg-slate-50 transition">Annuler</a>
                <button type="submit"
                    class="px-6 py-2.5 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition shadow-sm">Enregistrer
                    les modifications</button>
            </div>
        </form>
    </div>
@endsection