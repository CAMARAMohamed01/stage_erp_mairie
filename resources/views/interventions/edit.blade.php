@extends('layouts.app')

@section('header_title', 'Modifier l\'intervention #' . $intervention->id_int)

@section('content')
<div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-md border border-slate-100">

    <form action="{{ route('interventions.update', $intervention->id_int) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <h3 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4">Détails de l'intervention</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Équipement concerné</label>
                <select name="equipement_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-slate-50">
                    <option value="">-- Aucun équipement spécifique --</option>
                    @foreach($equipements as $equipement)
                    <option value="{{ $equipement->id_equipement }}"
                        {{ $equipement_preselectionne == $equipement->id_equipement ? 'selected' : '' }}>
                        {{ $equipement->nom_equipement }} (Ref: {{ $equipement->reference_serie ?? 'N/A' }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date d'ouverture *</label>
                <input type="date" name="date_ouverture" required
                    value="{{ old('date_ouverture', $intervention->date_ouverture) }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type d'intervention *</label>
                <select name="type_intervention" required class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    <option value="Maintenance"
                        {{ $intervention->type_intervention == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="Dépannage Curatif"
                        {{ $intervention->type_intervention == 'Dépannage Curatif' ? 'selected' : '' }}>Dépannage
                        Curatif</option>
                    <option value="Contrôle Réglementaire"
                        {{ $intervention->type_intervention == 'Contrôle Réglementaire' ? 'selected' : '' }}>Contrôle
                        Réglementaire</option>
                    <option value="Travaux Neufs"
                        {{ $intervention->type_intervention == 'Travaux Neufs' ? 'selected' : '' }}>Travaux
                        Neufs(Amélioration)
                    </option>
                    <option value="Gros Entretiens"
                        {{ $intervention->type_intervention == 'Gros Entretiens' ? 'selected' : '' }}>Gros Entretiens
                    </option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut *</label>
                <select name="statut_global" required class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    <option value="En cours" {{ $intervention->statut_global == 'En cours' ? 'selected' : '' }}>En cours
                    </option>
                    <option value="En attente de pièces"
                        {{ $intervention->statut_global == 'En attente de pièces' ? 'selected' : '' }}>En attente de
                        pièces</option>
                    <option value="Terminé" {{ $intervention->statut_global == 'Terminé' ? 'selected' : '' }}>Terminé
                    </option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Code Budget</label>
                <input type="text" name="code_budget" maxlength="2"
                    value="{{ old('code_budget', $intervention->code_budget) }}" placeholder="Ex: A, B1..."
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 uppercase focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                <span class="text-xs text-gray-500">1 ou 2 lettres max.</span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie *</label>
                <select name="id_cat" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm bg-white">
                    <option value="">-- Sélectionner une catégorie --</option>
                    @if(isset($categories))
                    @foreach($categories as $categorie)
                    <option value="{{ $categorie->id_cat }}"
                        {{ $intervention->id_cat == $categorie->id_cat ? 'selected' : '' }}>
                        {{ $categorie->libelle }}
                    </option>
                    @endforeach
                    @endif
                </select>
            </div>

        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Agents assignés à l'intervention</label>
            <div
                class="grid grid-cols-1 sm:grid-cols-2 gap-2 border border-gray-300 rounded-lg p-3 bg-white max-h-48 overflow-y-auto">
                @foreach($agents as $agent)
                <label class="flex items-center space-x-3 cursor-pointer p-2 hover:bg-slate-50 rounded transition">
                    <input type="checkbox" name="agents[]" value="{{ $agent->id_user }}"
                        class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-4 h-4"
                        {{ (isset($intervention) && $intervention->agents->contains('id_user', $agent->id_user)) ? 'checked' : '' }}>
                    <span class="text-sm font-medium text-slate-700">
                        {{ $agent->prenom_user }} {{ $agent->nom_user }}
                    </span>
                </label>
                @endforeach
            </div>
            <span class="text-xs text-gray-500 mt-1 block">Vous pouvez sélectionner plusieurs agents.</span>
        </div>
        <div>
            <label for="id_tiers" class="block text-sm font-medium text-gray-700 mb-1">
                🏢 Entreprise / Tiers externe (Sous-traitance)
            </label>
            <select name="id_tiers" id="id_tiers"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm text-sm">
                <option value="">-- Sélectionner un prestataire externe (Optionnel) --</option>
                @foreach($tiers as $t)
                <option value="{{ $t->id_tiers }}"
                    {{ (old('id_tiers', $intervention->id_tiers ?? '') == $t->id_tiers) ? 'selected' : '' }}>

                    {{-- Affichage dynamique selon le type de tiers --}}
                    {{ $t->raison_sociale ?? ($t->nom_tiers . ' ' . $t->prenom_tiers) }}
                    — [{{ $t->type_tiers }}]

                </option>
                @endforeach
            </select>
            <span class="text-xs text-gray-500 mt-1 block">À renseigner uniquement si l'intervention est
                externalisée.</span>
        </div>

        <h3 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4 mt-8">Cadre Administratif & Budgétaire</h3>
        <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-blue-900 mb-1">Contrat associé (Optionnel)</label>
                    <select name="id_contrat"
                        class="w-full border border-blue-200 rounded-lg px-4 py-2 bg-white focus:ring-blue-500">
                        <option value="">-- Aucun contrat --</option>
                        @foreach($contrats as $contrat)
                        <option value="{{ $contrat->id_contrat }}"
                            {{ (isset($intervention) && $intervention->id_contrat == $contrat->id_contrat) ? 'selected' : '' }}>
                            {{ $contrat->numero_contrat ?? 'Sans N°' }} - {{ $contrat->type_contrat }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-blue-900 mb-1">Projet Communal</label>
                    <select name="id_projet"
                        class="w-full border border-blue-200 rounded-lg px-4 py-2 bg-white focus:ring-blue-500">
                        <option value="">-- Aucun projet --</option>
                        @foreach($projets as $projet)
                        <option value="{{ $projet->id_projet }}"
                            {{ old('id_projet', $intervention->id_projet) == $projet->id_projet ? 'selected' : '' }}>
                            {{ $projet->nom_projet }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-blue-900 mb-1">Opération Comptable</label>
                    <select name="id_operation"
                        class="w-full border border-blue-200 rounded-lg px-4 py-2 bg-white focus:ring-blue-500">
                        <option value="">-- Aucune opération --</option>
                        @foreach($operations as $op)
                        <option value="{{ $op->id_operation }}"
                            {{ (isset($intervention) && $intervention->id_operation == $op->id_operation) ? 'selected' : '' }}>
                            {{ $op->numero_operation }} - {{ $op->libelle_operation }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Description du problème / de l'intervention
                *</label>
            <textarea name="description" required rows="4"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500">{{ old('description', $intervention->description) }}</textarea>
        </div>

        <div class="flex justify-end pt-6 border-t border-gray-200">
            <a href="{{ route('interventions.show', $intervention->id_int) }}"
                class="px-6 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg mr-4 font-medium transition">
                Annuler
            </a>
            <button type="submit"
                class="px-6 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-lg shadow-md transition">
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
@endsection