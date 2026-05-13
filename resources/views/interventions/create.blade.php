@extends('layouts.app')

@section('header_title', 'Nouvelle Intervention')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-md border border-slate-100">

        <form action="{{ route('interventions.store') }}" method="POST" class="space-y-6">
            @csrf

            <h3 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4">Détails de l'intervention</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Équipement concerné</label>
                    <select name="equipement_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-slate-50">
                        <option value="">-- Aucun équipement spécifique --</option>
                        @foreach($equipements as $equipement)
                            <option value="{{ $equipement->id_equipement }}" {{ (isset($equipement_preselectionne) && $equipement_preselectionne == $equipement->id_equipement) ? 'selected' : '' }}>
                                {{ $equipement->nom_equipement }} (Ref: {{ $equipement->reference_serie ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date d'ouverture *</label>
                    <input type="date" name="date_ouverture" required value="{{ date('Y-m-d') }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type d'intervention *</label>
                    <select name="type_intervention" required class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        <option value="Maintenance Préventive">Maintenance Préventive</option>
                        <option value="Dépannage Curatif">Dépannage Curatif</option>
                        <option value="Contrôle Réglementaire">Contrôle Réglementaire</option>
                        <option value="Travaux Neufs">Travaux Neufs</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut *</label>
                    <select name="statut_global" required class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        <option value="En cours">En cours</option>
                        <option value="En attente de pièces">En attente de pièces</option>
                        <option value="Terminée">Terminée</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code Budget</label>
                    <input type="text" name="code_budget" maxlength="2" placeholder="Ex: T, C1..."
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
                                <option value="{{ $categorie->id_cat }}">{{ $categorie->libelle }}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('id_cat')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description du problème / de l'intervention
                    *</label>
                <textarea name="description" required rows="4"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500"></textarea>
            </div>

            <div class="flex justify-end pt-6 border-t border-gray-200">
                <button type="button" onclick="history.back()"
                    class="px-6 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg mr-4">Annuler</button>
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-md">
                    Enregistrer l'intervention
                </button>
            </div>
        </form>
    </div>
@endsection