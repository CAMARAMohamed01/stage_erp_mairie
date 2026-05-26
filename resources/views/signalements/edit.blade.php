@extends('layouts.app')

@section('title', 'Modifier le action #' . $action->id_action)

@section('content')
    <div class="max-w-3xl mx-auto">

        <div class="mb-6">
            <a href="{{ route('actions.show', $action->id_action) }}"
                class="text-slate-500 hover:text-slate-800 text-sm flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Retour à la fiche
            </a>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-lg overflow-hidden">
            <div class="bg-amber-500 px-8 py-6">
                <h1 class="text-2xl font-bold text-white">Modification du action #{{ $action->id_action }}</h1>
            </div>

            <form action="{{ route('actions.update', $action->id_action) }}" method="POST" class="p-8 space-y-8">
                @csrf
                @method('PUT')

                <div class="bg-slate-50 p-6 rounded-xl border border-slate-100 space-y-6">
                    <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider border-b pb-2">Identification de
                        l'émetteur</h2>

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="id_tiers" class="block text-xs font-bold text-slate-500 uppercase mb-1">Associer à
                                un citoyen existant</label>
                            <select name="id_tiers" id="id_tiers"
                                class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-amber-500">
                                <option value="">-- Aucun (Saisie manuelle) --</option>
                                @foreach($tiers as $t)
                                    <option value="{{ $t->id_tiers }}"
                                        {{ $action->id_tiers == $t->id_tiers ? 'selected' : '' }}>
                                        {{ $t->nom_tiers }} {{ $t->prenom_tiers }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nom / Prénom
                                    manuel</label>
                                <input type="text" name="emetteur_nom" id="emetteur_nom"
                                    value="{{ $action->emetteur_nom }}"
                                    class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-amber-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Coordonnées
                                    (Tél/Email)</label>
                                <input type="text" name="emetteur_contact" id="emetteur_contact"
                                    value="{{ $action->emetteur_contact }}"
                                    class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-amber-500">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b pb-2">Détails de
                        l'incident</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Mode de réception</label>
                            <select name="mode_reception" required
                                class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-amber-500">
                                <option value="Téléphone"
                                    {{ $action->mode_reception == 'Téléphone' ? 'selected' : '' }}>📞 Téléphone
                                </option>
                                <option value="Email" {{ $action->mode_reception == 'Email' ? 'selected' : '' }}>📧
                                    Email</option>
                                <option value="Accueil" {{ $action->mode_reception == 'Accueil' ? 'selected' : '' }}>🏢
                                    Accueil Mairie</option>
                                <option value="Application"
                                    {{ $action->mode_reception == 'Application' ? 'selected' : '' }}>📱 Application
                                    Mobile</option>
                                <option value="Courrier" {{ $action->mode_reception == 'Courrier' ? 'selected' : '' }}>
                                    ✉️ Courrier</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Catégorie technique</label>
                            <select name="id_cat" required
                                class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-amber-500">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id_cat }}"
                                        {{ $action->id_cat == $cat->id_cat ? 'selected' : '' }}>{{ $cat->libelle }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Priorité d'intervention</label>
                        <div class="flex gap-6 mt-2">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="priorite" value="Basse"
                                    {{ $action->priorite == 'Basse' ? 'checked' : '' }}
                                    class="text-green-500 focus:ring-green-500">
                                <span class="ml-2 text-sm text-slate-600">Basse</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="priorite" value="Normale"
                                    {{ $action->priorite == 'Normale' ? 'checked' : '' }}
                                    class="text-blue-500 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-slate-600">Normale</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="priorite" value="Haute"
                                    {{ $action->priorite == 'Haute' ? 'checked' : '' }}
                                    class="text-red-500 focus:ring-red-500">
                                <span class="ml-2 text-sm text-slate-600 font-bold text-red-600">Haute / Urgence</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Description précise</label>
                        <textarea name="description" rows="4" required
                            class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-amber-500">{{ $action->description }}</textarea>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 flex justify-end gap-4">
                    <button type="submit"
                        class="px-8 py-2.5 bg-amber-500 text-white font-bold rounded-lg hover:bg-amber-600 shadow-md transition-all">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Logique UI : Griser si Tiers existant sélectionné
    const selectTiers = document.getElementById('id_tiers');
    const inputNom = document.getElementById('emetteur_nom');

    function toggleFields() {
        if (selectTiers.value !== "") {
            inputNom.classList.add('bg-slate-100', 'text-slate-400');
            inputNom.readOnly = true;
            inputNom.value = "Le nom sera mis à jour avec celui du Tiers";
        } else {
            inputNom.classList.remove('bg-slate-100', 'text-slate-400');
            inputNom.readOnly = false;
            if (inputNom.value === "Le nom sera mis à jour avec celui du Tiers") inputNom.value = "";
        }
    }

    selectTiers.addEventListener('change', toggleFields);
    toggleFields(); // Exécuter au chargement pour gérer l'état initial
    </script>
@endsection