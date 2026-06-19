@extends('layouts.app')

@section('header_title', 'Nouvelle Intervention')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-md border border-slate-100">

    <form action="{{ route('interventions.store') }}" method="POST" class="space-y-6">
        @csrf

        @if(isset($equipement_preselectionne))
        <input type="hidden" name="from_equipement" value="1">
        @endif

        <h3 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4">Détails et Localisation de l'intervention</h3>

        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-4">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Élément du patrimoine
                concerné</span>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Équipement</label>
                    @if(isset($equipement_preselectionne))
                    <input type="hidden" name="equipement_id" value="{{ $equipement_preselectionne }}">
                    @endif
                    <select name="{{ isset($equipement_preselectionne) ? '' : 'equipement_id' }}"
                        {{ isset($equipement_preselectionne) ? 'disabled' : '' }}
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-white disabled:bg-slate-100">
                        <option value="">-- Aucun équipement spécifique --</option>
                        @foreach($equipements as $equipement)
                        <option value="{{ $equipement->id_equipement }}"
                            {{ (isset($equipement_preselectionne) && $equipement_preselectionne == $equipement->id_equipement) ? 'selected' : '' }}>
                            {{ $equipement->nom_equipement }} (Ref: {{ $equipement->reference_serie ?? 'N/A' }})
                        </option>
                        @endforeach
                    </select>
                </div>


                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Local / Bureau</label>
                    <select name="id_local" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-white">
                        <option value="">-- Aucun local spécifique --</option>
                        @foreach($locaux as $local)
                        <option value="{{ $local->id_local }}">{{ $local->nom_local }} (Niveau:
                            {{ $local->niveau ?? 'N/A' }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bâtiment Communal</label>
                    <select name="id_batiment" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-white">
                        <option value="">-- Aucun bâtiment spécifique --</option>
                        @foreach($batiments as $bat)
                        <option value="{{ $bat->id_batiment }}">{{ $bat->nom_bat }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lieu Public / Espace Extérieur</label>
                    <select name="id_lieu" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-white">
                        <option value="">-- Aucun espace public spécifique --</option>
                        @foreach($lieux_publics as $lieu)
                        <option value="{{ $lieu->id_lieu }}">{{ $lieu->nom_lieu }}
                            ({{ $lieu->typologie_lieu ?? 'Espace' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Projet Communal</label>
                    <select name="id_projet" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-white">
                        <option value="">-- Aucun projet spécifique --</option>
                        @foreach($projets as $p)
                        <option value="{{ $p->id_projet }}"
                            {{ old('id_projet', $projet_id ?? null) == $p->id_projet ? 'selected' : '' }}>
                            {{ $p->nom_projet }}
                        </option>
                        @endforeach
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
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date d'ouverture *</label>
                <input type="date" name="date_ouverture" required value="{{ date('Y-m-d') }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type d'intervention *</label>
                <select name="type_intervention" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500">
                    <option value="Maintenance">Maintenance</option>
                    <option value="Réparation">Réparation</option>
                    <option value="Aménagement">Aménagement</option>
                    <option value="Dépannage Curatif">Dépannage Curatif</option>
                    <option value="Contrôle Réglementaire">Contrôle Réglementaire</option>
                    <option value="Travaux Neufs">Travaux Neufs(Amélioration)</option>
                    <option value="Gros Entretiens">Gros Entretiens</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut Initial *</label>
                <select name="statut_global" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500">
                    <option value="En cours">En cours</option>
                    <option value="En attente de pièces">En attente de pièces</option>
                    <option value="Terminée">Terminée</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie Technique *</label>
                <select name="id_cat" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 bg-white">
                    <option value="">-- Sélectionner une catégorie --</option>
                    @foreach($categories as $categorie)
                    <option value="{{ $categorie->id_cat }}">{{ $categorie->libelle }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Code Budget</label>
                <input type="text" name="code_budget" maxlength="2" placeholder="Ex: T, C1..."
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 uppercase focus:ring-blue-500 shadow-sm">
                <span class="text-xs text-gray-500">2 caractères max (Code interne Mairie).</span>
            </div>
        </div>
        <h3 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4 mt-8">Cadre Administratif & Budgétaire</h3>
        <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-blue-900 mb-1">Contrat associé (Optionnel)</label>
                    <select name="id_contrat"
                        class="w-full border border-blue-200 rounded-lg px-4 py-2 bg-white focus:ring-blue-500">
                        <option value="">-- Aucun contrat spécifique --</option>
                        @foreach($contrats as $contrat)
                        <option value="{{ $contrat->id_contrat }}"
                            {{ (isset($intervention) && $intervention->id_contrat == $contrat->id_contrat) ? 'selected' : '' }}>
                            {{ $contrat->numero_contrat ?? 'Sans N°' }} - {{ $contrat->type_contrat }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-blue-900 mb-1">Imputation (Projet / Opération)</label>
                    <select name="id_operation"
                        class="w-full border border-blue-200 rounded-lg px-4 py-2 bg-white focus:ring-blue-500">
                        <option value="">-- Hors projet spécifique --</option>
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
            <label class="block text-sm font-medium text-gray-700 mb-1">Description du problème / Cahier des charges
                *</label>
            <textarea name="description" required rows="4"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500"
                placeholder="Saisissez ici les détails des travaux à réaliser sur la structure ou l'équipement..."></textarea>
        </div>

        <div class="flex justify-end pt-6 border-t border-gray-200">
            <button type="button" onclick="history.back()"
                class="px-6 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg mr-4">
                Annuler
            </button>
            <button type="submit"
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-md">
                Enregistrer l'intervention
            </button>
        </div>
    </form>
</div>
@endsection