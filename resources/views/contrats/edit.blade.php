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

        <form action="{{ route('contrats.update', $contrat->id_contrat) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT') <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">N° de contrat *</label>
                    <input type="text" name="numero_contrat" required
                        value="{{ old('numero_contrat', $contrat->numero_contrat) }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Type de contrat *</label>
                    <input type="text" name="type_contrat" required
                        value="{{ old('type_contrat', $contrat->type_contrat) }}" placeholder="Ex: Maintenance, Location..."
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tiers (Prestataire / Fournisseur) *</label>
                    <select name="id_tiers" required
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm bg-white outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Sélectionner le tiers --</option>
                        @foreach($tiers as $t)
                            <option value="{{ $t->id_tiers }}" {{ (old('id_tiers', $contrat->id_tiers) == $t->id_tiers) ? 'selected' : '' }}>
                                {{ $t->raison_sociale ?? ($t->nom_tiers . ' ' . $t->prenom_tiers) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Date de signature</label>
                    <input type="date" name="date_signature" value="{{ old('date_signature', $contrat->date_signature) }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Date d'effet</label>
                    <input type="date" name="date_effet" value="{{ old('date_effet', $contrat->date_effet) }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Date d'échéance</label>
                    <input type="date" name="date_echeance" value="{{ old('date_echeance', $contrat->date_echeance) }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Montant HT (€)</label>
                    <input type="number" step="0.01" name="montant_ht" value="{{ old('montant_ht', $contrat->montant_ht) }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Statut du contrat *</label>
                    <select name="statut_contrat" required
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm bg-white outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Actif" {{ old('statut_contrat', $contrat->statut_contrat) == 'Actif' ? 'selected' : '' }}>Actif
                        </option>
                        <option value="Échu" {{ old('statut_contrat', $contrat->statut_contrat) == 'Échu' ? 'selected' : '' }}>Échu / Terminé
                        </option>
                        <option value="Suspendu" {{ old('statut_contrat', $contrat->statut_contrat) == 'Suspendu' ? 'selected' : '' }}>Suspendu
                        </option>
                        <option value="Résilié" {{ old('statut_contrat', $contrat->statut_contrat) == 'Résilié' ? 'selected' : '' }}>Résilié
                        </option>
                    </select>
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