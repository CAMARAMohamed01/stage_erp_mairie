@extends('layouts.app')

@section('title', 'Modifier le contrat ' . $contrat->numero_contrat)

@section('content')
<div class="max-w-4xl mx-auto bg-white p-8 rounded-xl border border-slate-200 shadow-sm">
    <div class="mb-6 border-b border-slate-100 pb-4 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">📝 Modifier le contrat</h1>
            <p class="text-sm text-slate-500">Mise à jour des informations contractuelles</p>
        </div>
        <a href="{{ route('contrats.show', $contrat->id_contrat) }}"
            class="text-sm font-medium text-slate-500 hover:text-slate-700">← Retour à la fiche</a>
    </div>

    <form action="{{ route('contrats.update', $contrat->id_contrat) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

        {{-- SECTION 1 : Identification --}}
        <div>
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Identification</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">N° de contrat</label>
                    <input type="text" name="numero_contrat"
                        value="{{ old('numero_contrat', $contrat->numero_contrat) }}" maxlength="30"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Type de contrat *</label>
                    <input type="text" name="type_contrat" required
                        value="{{ old('type_contrat', $contrat->type_contrat) }}"
                        placeholder="Ex: Maintenance, Location..."
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Objet du contrat</label>
                    <textarea name="objet_contrat" rows="2" maxlength="255"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">{{ old('objet_contrat', $contrat->objet_contrat) }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tiers (Prestataire / Fournisseur)
                        *</label>
                    <select name="id_tiers" required
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm bg-white outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Sélectionner le tiers --</option>
                        @foreach($tiers as $t)
                        <option value="{{ $t->id_tiers }}"
                            {{ old('id_tiers', $contrat->id_tiers) == $t->id_tiers ? 'selected' : '' }}>
                            {{ $t->raison_sociale ?? ($t->nom_tiers . ' ' . $t->prenom_tiers) }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- SECTION 2 : Dates --}}
        <div>
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Dates</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Date de signature</label>
                    <input type="date" name="date_signature_contrat"
                        value="{{ old('date_signature_contrat', $contrat->date_signature_contrat?->format('Y-m-d')) }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Date de début *</label>
                    <input type="date" name="date_debut_contrat" required
                        value="{{ old('date_debut_contrat', $contrat->date_debut_contrat?->format('Y-m-d')) }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Date de fin</label>
                    <input type="date" name="date_fin_contrat"
                        value="{{ old('date_fin_contrat', $contrat->date_fin_contrat?->format('Y-m-d')) }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Date d'échéance</label>
                    <input type="date" name="date_echeance"
                        value="{{ old('date_echeance', $contrat->date_echeance?->format('Y-m-d')) }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        {{-- SECTION 3 : Conditions financières --}}
        <div>
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Conditions financières</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Prix mensuel (€)</label>
                    <input type="number" step="0.01" min="0" name="prix_mois"
                        value="{{ old('prix_mois', $contrat->prix_mois) }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Prix annuel (€)</label>
                    <input type="number" step="0.01" min="0" name="prix_annuel"
                        value="{{ old('prix_annuel', $contrat->prix_annuel) }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Fréquence de facturation</label>
                    <input type="text" name="frequence_facturation" maxlength="100"
                        value="{{ old('frequence_facturation', $contrat->frequence_facturation) }}"
                        placeholder="Ex: Mensuelle, Trimestrielle..."
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Mode de règlement</label>
                    <input type="text" name="mode_reglement" maxlength="50"
                        value="{{ old('mode_reglement', $contrat->mode_reglement) }}"
                        placeholder="Ex: Virement, Prélèvement..."
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2 flex items-center gap-3 p-3 bg-slate-50 rounded-lg border border-slate-200">
                    <input type="checkbox" name="revision_prix_prevue" id="revision_prix_prevue" value="1"
                        {{ old('revision_prix_prevue', $contrat->revision_prix_prevue) ? 'checked' : '' }}
                        class="w-4 h-4 accent-blue-600">
                    <label for="revision_prix_prevue" class="text-sm font-medium text-slate-700 cursor-pointer">
                        Révision des prix prévue au contrat
                    </label>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Statut du contrat *</label>
                    <select name="statut_contrat" required
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm bg-white outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Actif"
                            {{ old('statut_contrat', $contrat->statut_contrat) == 'Actif' ? 'selected' : '' }}>Actif
                        </option>
                        <option value="Échu"
                            {{ old('statut_contrat', $contrat->statut_contrat) == 'Échu' ? 'selected' : '' }}>Échu /
                            Terminé
                        </option>
                        <option value="Suspendu"
                            {{ old('statut_contrat', $contrat->statut_contrat) == 'Suspendu' ? 'selected' : '' }}>
                            Suspendu
                        </option>
                        <option value="Résilié"
                            {{ old('statut_contrat', $contrat->statut_contrat) == 'Résilié' ? 'selected' : '' }}>Résilié
                        </option>
                    </select>
                </div>
            </div>
        </div>

        {{-- SECTION 4 : Durée et résiliation --}}
        <div>
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Durée et résiliation</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Durée (en mois)</label>
                    <input type="number" min="0" name="duree_mois" value="{{ old('duree_mois', $contrat->duree_mois) }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Préavis de résiliation (mois)</label>
                    <input type="number" min="0" name="preavis_resiliation_mois"
                        value="{{ old('preavis_resiliation_mois', $contrat->preavis_resiliation_mois) }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Modalités de renouvellement</label>
                    <input type="text" name="modalite_renouvellement" maxlength="255"
                        value="{{ old('modalite_renouvellement', $contrat->modalite_renouvellement) }}"
                        placeholder="Ex: Tacite reconduction, Appel d'offres..."
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        {{-- SECTION 5 : Imputation --}}
        <div>
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Imputation analytique</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Code d'imputation</label>
                    <input type="text" name="code_imputation" maxlength="20"
                        value="{{ old('code_imputation', $contrat->code_imputation) }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Lot</label>
                    <input type="text" name="lot" maxlength="20" value="{{ old('lot', $contrat->lot) }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Code analytique</label>
                    <input type="text" name="code_analytique" maxlength="100"
                        value="{{ old('code_analytique', $contrat->code_analytique) }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-6 border-t border-slate-200 gap-3">
            <a href="{{ route('contrats.show', $contrat->id_contrat) }}"
                class="px-6 py-2 text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg font-medium transition text-sm">Annuler</a>
            <button type="submit"
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-md transition text-sm">Enregistrer
                les modifications</button>
        </div>
    </form>
</div>
@endsection