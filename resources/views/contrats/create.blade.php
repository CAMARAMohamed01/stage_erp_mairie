@extends('layouts.app')

@section('title', 'Nouveau Contrat')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-xl border border-slate-200 shadow-sm">
        <div class="mb-6 border-b border-slate-100 pb-4">
            <h1 class="text-2xl font-bold text-slate-800">📄 Enregistrer un nouveau contrat</h1>
            <p class="text-sm text-slate-500">Associez un engagement financier à un prestataire de la commune.</p>
        </div>

        <form action="{{ route('contrats.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <h3 class="text-sm font-bold text-blue-600 uppercase tracking-wider mb-3">1. Identification du Contrat</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Numéro de contrat (Unique)</label>
                        <input type="text" name="numero_contrat" value="{{ old('numero_contrat') }}"
                            placeholder="Ex: CT-2026-042"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                        @error('numero_contrat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Type de contrat *</label>
                        <select name="type_contrat" required
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm bg-white outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Choisir un type --</option>
                            <option value="Maintenance technique">Maintenance technique</option>
                            <option value="Assurance dommages">Assurance dommages</option>
                            <option value="Location de matériel">Location de matériel</option>
                            <option value="Prestation de services">Prestation de services</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Titulaire du contrat (Tiers) *</label>
                        <select name="id_tiers" required
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm bg-white outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Sélectionner le prestataire externe --</option>
                            @foreach($tiers as $t)
                                <option value="{{ $t->id_tiers }}" {{ old('id_tiers') == $t->id_tiers ? 'selected' : '' }}>
                                    {{ $t->raison_sociale ?? ($t->nom_tiers . ' ' . $t->prenom_tiers) }} —
                                    [{{ $t->type_tiers }}]
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Objet du contrat</label>
                        <input type="text" name="objet_contrat" value="{{ old('objet_contrat') }}"
                            placeholder="Ex: Maintenance des ascenseurs de l'hôtel de ville"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <h3 class="text-sm font-bold text-blue-600 uppercase tracking-wider mb-3">2. Période & Validité</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Date de signature</label>
                        <input type="date" name="date_signature_contrat" value="{{ old('date_signature_contrat') }}"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Date de début *</label>
                        <input type="date" name="date_debut_contrat" required value="{{ old('date_debut_contrat') }}"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Date de fin</label>
                        <input type="date" name="date_fin_contrat" value="{{ old('date_fin_contrat') }}"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Durée (en mois)</label>
                        <input type="number" name="duree_mois" value="{{ old('duree_mois') }}" placeholder="Ex: 24"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Préavis de résiliation (mois)</label>
                        <input type="number" name="preavis_resiliation_mois" value="{{ old('preavis_resiliation_mois') }}"
                            placeholder="Ex: 3"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex items-center h-full pt-6">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" name="revision_prix_prevue" value="1"
                                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
                            <span class="text-sm font-medium text-slate-700">Révision de prix prévue</span>
                        </label>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Modalités de renouvellement</label>
                        <input type="text" name="modalite_renouvellement" value="{{ old('modalite_renouvellement') }}"
                            placeholder="Ex: Reconduction tacite annuelle"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <h3 class="text-sm font-bold text-blue-600 uppercase tracking-wider mb-3">3. Paramètres Financiers &
                    Budgétaires</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Prix mensuel (€ HT)</label>
                        <input type="number" step="0.01" name="prix_mois" value="{{ old('prix_mois') }}" placeholder="0.00"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Prix annuel (€ HT)</label>
                        <input type="number" step="0.01" name="prix_annuel" value="{{ old('prix_annuel') }}"
                            placeholder="0.00"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Fréquence de facturation</label>
                        <input type="text" name="frequence_facturation" value="{{ old('frequence_facturation') }}"
                            placeholder="Ex: Trimestrielle, Annuelle"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Mode de règlement</label>
                        <input type="text" name="mode_reglement" value="{{ old('mode_reglement') }}"
                            placeholder="Ex: Virement administratif (Manda)"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <div class="md:col-span-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-2">Imputation
                            Analytique (Mairie)</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Code Imputation</label>
                        <input type="text" name="code_imputation" value="{{ old('code_imputation') }}"
                            placeholder="Ex: IMP-6156"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm bg-white outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Lot</label>
                        <input type="text" name="lot" value="{{ old('lot') }}" placeholder="Ex: Lot 2"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm bg-white outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Code Analytique</label>
                        <input type="text" name="code_analytique" value="{{ old('code_analytique') }}"
                            placeholder="Ex: ANA-CENTRAL"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm bg-white outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-6 border-t border-slate-200 gap-3">
                <a href="{{ route('contrats.index') }}"
                    class="px-6 py-2 text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg font-medium transition text-sm">
                    Annuler
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-md transition text-sm">
                    Enregistrer le contrat
                </button>
            </div>
        </form>
    </div>
@endsection