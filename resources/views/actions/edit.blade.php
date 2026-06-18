@extends('layouts.app')

@section('title', 'Modifier l\'action #' . $action->id_action)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('actions.show', $action->id_action) }}"
            class="text-slate-500 hover:text-slate-800 text-sm flex items-center font-semibold transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Retour à la fiche
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="bg-white px-8 py-6 border-b border-slate-200">
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Modification du signalement
                #{{ $action->id_action }}</h1>
            <p class="text-sm text-slate-500 mt-1">Mise à jour des informations de l'anomalie ou du déclarant</p>
        </div>
        @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Il y a des erreurs dans le formulaire :</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif
        <form action="{{ route('actions.update', $action->id_action) }}" method="POST" class="p-8 space-y-8">
            @csrf
            @method('PUT')

            {{-- BLOC : CITOYEN EMETTEUR --}}
            <div class="bg-slate-50/50 p-6 rounded-xl border border-slate-200 space-y-6">
                <div class="flex items-center gap-2">
                    <span class="bg-slate-800 text-white p-1 rounded">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </span>
                    <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Identification de l'émetteur
                    </h2>
                </div>

                <div class="grid grid-cols-1 gap-5">
                    <div>
                        <label for="id_tiers" class="block text-xs font-bold text-slate-500 uppercase mb-1">Associer à
                            un citoyen existant</label>
                        <select name="id_tiers" id="id_tiers"
                            class="w-full border-slate-300 rounded-lg shadow-sm text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white py-2.5">
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
                            <input type="text" name="emetteur_nom" id="emetteur_nom" value="{{ $action->emetteur_nom }}"
                                class="w-full border-slate-300 rounded-lg shadow-sm text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 py-2.5">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Coordonnées
                                (Tél/Email)</label>
                            <input type="text" name="emetteur_contact" id="emetteur_contact"
                                value="{{ $action->emetteur_contact }}"
                                class="w-full border-slate-300 rounded-lg shadow-sm text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 py-2.5">
                        </div>
                    </div>
                </div>
            </div>

            {{-- BLOC : LOCALISATION DE L'INCIDENT --}}
            <div class="bg-amber-50/20 p-6 rounded-xl border border-amber-200/70 space-y-6">
                <div class="flex items-center gap-2">
                    <span class="bg-amber-500 text-white p-1 rounded">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </span>
                    <h2 class="text-sm font-bold text-amber-900 uppercase tracking-wider">📍 Localisation de l'incident
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Adresse (Voirie)</label>
                        <select name="id_adresse" id="id_adresse"
                            class="w-full border-slate-300 rounded-lg shadow-sm text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white py-2.5">
                            <option value="">-- Aucune adresse spécifique --</option>
                            @foreach($adresses as $adr)
                            <option value="{{ $adr->id_adresse }}"
                                {{ $action->id_adresse == $adr->id_adresse ? 'selected' : '' }}>
                                {{ $adr->num_rue }} {{ $adr->nom_voie }} ({{ $adr->code_postal }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Lieu public </label>
                        <select name="id_lieu" id="id_lieu"
                            class="w-full border-slate-300 rounded-lg shadow-sm text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white py-2.5">
                            <option value="">-- Aucun lieu public --</option>
                            @foreach($lieux_publics as $lieu)
                            <option value="{{ $lieu->id_lieu }}"
                                {{ $action->id_lieu == $lieu->id_lieu ? 'selected' : '' }}>
                                🌳 {{ $lieu->nom_lieu }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Bâtiment municipal</label>
                        <select name="id_batiment" id="id_batiment"
                            class="w-full border-slate-300 rounded-lg shadow-sm text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white py-2.5">
                            <option value="">-- Aucun bâtiment --</option>
                            @foreach($batiments as $bat)
                            <option value="{{ $bat->id_batiment }}"
                                {{ $action->id_batiment == $bat->id_batiment ? 'selected' : '' }}>
                                🏛️ {{ $bat->nom_bat }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Local technique
                            précis</label>
                        <select name="id_local" id="id_local"
                            class="w-full border-slate-300 rounded-lg shadow-sm text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white py-2.5">
                            <option value="">-- Aucun local technique --</option>
                            @foreach($locaux as $loc)
                            <option value="{{ $loc->id_local }}"
                                {{ $action->id_local == $loc->id_local ? 'selected' : '' }}>
                                🔑 {{ $loc->nom_local }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Lieu-dit</label>
                        <select name="id_lieu_dit" id="id_lieu_dit"
                            class="w-full border-slate-300 rounded-lg shadow-sm text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white py-2.5">
                            <option value="">-- Aucun lieu-dit --</option>
                            @foreach($lieux_dit as $lieu_dit)
                            <option value="{{ $lieu_dit->id_lieu_dit }}"
                                {{ $action->id_lieu_dit == $lieu_dit->id_lieu_dit ? 'selected' : '' }}>
                                🏞️ {{ $lieu_dit->nom_lieu_dit }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div> {{-- BLOC : DETAILS DE L'INCIDENT --}}
            <div class="space-y-6">
                <div class="flex items-center gap-2">
                    <span class="bg-slate-700 text-white p-1 rounded">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </span>
                    <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Détails techniques de
                        l'incident</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Mode de réception</label>
                        <select name="mode_reception" required
                            class="w-full border-slate-300 rounded-lg shadow-sm text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white py-2.5">
                            <option value="Téléphone" {{ $action->mode_reception == 'Téléphone' ? 'selected' : '' }}>📞
                                Téléphone</option>
                            <option value="Email" {{ $action->mode_reception == 'Email' ? 'selected' : '' }}>📧 Email
                            </option>
                            <option value="Accueil" {{ $action->mode_reception == 'Accueil' ? 'selected' : '' }}>🏢
                                Accueil Mairie</option>
                            <option value="Application"
                                {{ $action->mode_reception == 'Application' ? 'selected' : '' }}>📱 Application Mobile
                            </option>
                            <option value="Courrier" {{ $action->mode_reception == 'Courrier' ? 'selected' : '' }}>✉️
                                Courrier</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Catégorie technique</label>
                        <select name="id_cat" required
                            class="w-full border-slate-300 rounded-lg shadow-sm text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white py-2.5">
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id_cat }}" {{ $action->id_cat == $cat->id_cat ? 'selected' : '' }}>
                                {{ $cat->libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Statut de l'action</label>
                        <select name="statut_action" required
                            class="w-full border-slate-300 rounded-lg shadow-sm text-sm focus:ring-2 focus:ring-blue-500 py-2.5 bg-blue-50 font-bold text-blue-900 border-blue-300">
                            <option value="Nouveau"
                                {{ old('statut_action', $action->statut_action) == 'Nouveau' ? 'selected' : '' }}>
                                Nouveau</option>
                            <option value="En cours"
                                {{ old('statut_action', $action->statut_action) == 'En cours' ? 'selected' : '' }}>En
                                cours</option>
                            <option value="En attente"
                                {{ old('statut_action', $action->statut_action) == 'En attente' ? 'selected' : '' }}>En
                                attente (Devis/Matériel)</option>
                            <option value="Abandonné"
                                {{ old('statut_action', $action->statut_action) == 'Abandonné' ? 'selected' : '' }}>
                                Abandonné</option>
                            <option value="Terminé"
                                {{ old('statut_action', $action->statut_action) == 'Terminé' ? 'selected' : '' }}>
                                Clôturé / Terminé</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Priorité d'intervention
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-6 mt-2">
                        <label class="flex items-center cursor-pointer select-none">
                            <input type="radio" name="priorite" value="Basse"
                                {{ $action->priorite == 'Basse' ? 'checked' : '' }}
                                class="w-4 h-4 text-green-500 focus:ring-green-500">
                            <span class="ml-2 text-sm font-medium text-slate-600">Basse</span>
                        </label>
                        <label class="flex items-center cursor-pointer select-none">
                            <input type="radio" name="priorite" value="Normale"
                                {{ $action->priorite == 'Normale' ? 'checked' : '' }}
                                class="w-4 h-4 text-blue-500 focus:ring-blue-500">
                            <span class="ml-2 text-sm font-medium text-slate-600">Normale</span>
                        </label>
                        <label class="flex items-center cursor-pointer select-none">
                            <input type="radio" name="priorite" value="Haute"
                                {{ $action->priorite == 'Haute' ? 'checked' : '' }}
                                class="w-4 h-4 text-red-500 focus:ring-red-500">
                            <span class="ml-2 text-sm font-bold text-red-600">Haute / Urgence</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Description précise</label>
                    <textarea name="description" rows="5" required
                        class="w-full border-slate-300 rounded-lg shadow-sm text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 p-4 bg-white font-medium text-slate-800 leading-relaxed">{{ $action->description }}</textarea>
                </div>
            </div>

            {{-- PIED DE PAGE ET ACTIONNEUR --}}
            <div class="pt-6 border-t border-slate-200 flex justify-end gap-3">
                <a href="{{ route('actions.show', $action->id_action) }}"
                    class="px-5 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-800 transition">
                    Annuler
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5 text-sm">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>

<script>
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
toggleFields();
</script>
@endsection