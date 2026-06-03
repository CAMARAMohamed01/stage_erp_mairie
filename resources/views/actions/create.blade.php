@extends('layouts.app')

@section('title', 'Nouveau action')

@section('content')
    <div class="max-w-3xl mx-auto">
        <nav class="flex mb-5" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('technique.dashboard') }}"
                        class="text-sm text-slate-500 hover:text-blue-600 font-medium">Tableau de bord</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('actions.index') }}"
                            class="ml-1 text-sm text-slate-500 hover:text-blue-600 font-medium">Actions</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm text-slate-800 font-bold">Nouveau</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="bg-white rounded-xl border border-slate-200 shadow-lg overflow-hidden">
            <div class="bg-slate-900 px-8 py-6">
                <h1 class="text-2xl font-bold text-white">Saisie d'une action</h1>
                <p class="text-slate-400 text-sm mt-1">Enregistrement d'une doléance ou d'un incident technique citoyen.</p>
            </div>

            <form action="{{ route('actions.store') }}" method="POST" class="p-8 space-y-8">
                @csrf

                {{-- BLOC 1 : CITOYEN / EMETTEUR --}}
                <div class="bg-blue-50/50 p-6 rounded-xl border border-blue-100 space-y-6">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="bg-blue-600 text-white p-1 rounded">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </span>
                        <h2 class="text-sm font-bold text-blue-900 uppercase tracking-wider">Identification de l'émetteur
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Citoyen répertorié (Base
                                Tiers)</label>
                            <select name="id_tiers" id="id_tiers"
                                class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Rechercher un habitant --</option>
                                @foreach($tiers as $t)
                                    <option value="{{ $t->id_tiers }}">{{ $t->nom_tiers }} {{ $t->prenom_tiers }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="relative py-2">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-slate-200"></div>
                            </div>
                            <div class="relative flex justify-center text-xs uppercase">
                                <span class="bg-blue-50 px-3 text-slate-400 font-bold">Ou nouveau contact</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nom / Organisme</label>
                                <input type="text" name="emetteur_nom" id="emetteur_nom" placeholder="Ex: Martin"
                                    class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Prénom</label>
                                <input type="text" name="emetteur_prenom" id="emetteur_prenom" placeholder="Ex: Jean"
                                    class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Coordonnées
                                    (Tél/Email)</label>
                                <input type="text" name="emetteur_contact" id="emetteur_contact"
                                    placeholder="06 00 00 00 00"
                                    class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div class="mt-4 bg-white p-3 rounded-lg border border-blue-100 shadow-sm">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="creer_nouveau_tiers" id="creer_nouveau_tiers" value="1"
                                    class="w-5 h-5 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                <span class="ml-3 text-sm text-slate-700 font-bold">💾 Enregistrer ce contact comme citoyen
                                    (Base Tiers)</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- NOUVEAU BLOC : LOCALISATION DE L'INCIDENT --}}
                <div class="bg-amber-50/40 p-6 rounded-xl border border-amber-200/60 space-y-6">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="bg-amber-500 text-white p-1 rounded">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </span>
                        <h2 class="text-sm font-bold text-amber-900 uppercase tracking-wider">Localisation de l'incident
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Adresse officielle (Voie
                                issue de la BAN)</label>
                            <select name="id_adresse" id="id_adresse"
                                class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500 text-sm">
                                <option value="">-- Sélectionner une adresse de voirie --</option>
                                @foreach($adresses as $adr)
                                    <option value="{{ $adr->id_adresse }}">
                                        {{ $adr->num_rue }} {{ $adr->nom_voie }} ({{ $adr->code_postal }} {{ $adr->ville }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Ou Local technique
                                intérieur (Bâtiment / Pièce)</label>
                            <select name="id_local" id="id_local"
                                class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500 text-sm">
                                <option value="">-- Sélectionner un local technique --</option>
                                @foreach($locaux as $loc)
                                    <option value="{{ $loc->id_local }}">🏢 {{ $loc->nom_local }} (Niveau :
                                        {{ $loc->niveau ?? 'RDC' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 italic mt-2">💡 Conseil : Si l'anomalie se situe en pleine rue,
                        choisissez l'adresse. Si elle concerne un bâtiment municipal, privilégiez le local technique.</p>
                </div>

                {{-- BLOC DETRAILS DE L'INCIDENT --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="bg-slate-700 text-white p-1 rounded">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                        </span>
                        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Détails de l'incident</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Mode de réception</label>
                            <select name="mode_reception" required class="w-full border-slate-300 rounded-lg shadow-sm">
                                <option value="Téléphone">📞 Téléphone</option>
                                <option value="Email">📧 Email</option>
                                <option value="Accueil">🏢 Accueil Mairie</option>
                                <option value="Application">📱 Application Mobile</option>
                                <option value="Courrier">✉️ Courrier</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Catégorie technique</label>
                            <select name="id_cat" required class="w-full border-slate-300 rounded-lg shadow-sm">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id_cat }}">{{ $cat->libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Priorité d'intervention</label>
                        <div class="flex gap-6 mt-2">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="priorite" value="Basse"
                                    class="text-green-500 focus:ring-green-500">
                                <span class="ml-2 text-sm text-slate-600">Basse</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="priorite" value="Normale" checked
                                    class="text-blue-500 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-slate-600">Normale</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="priorite" value="Haute" class="text-red-500 focus:ring-red-500">
                                <span class="ml-2 text-sm text-slate-600 font-bold text-red-600">Haute / Urgence</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Description précise</label>
                        <textarea name="description" rows="4" required
                            placeholder="Détaillez l'anomalie, les observations du citoyen..."
                            class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 flex justify-end gap-4">
                    <a href="{{ route('actions.index') }}"
                        class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-800 transition">Annuler</a>
                    <button type="submit"
                        class="px-8 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 shadow-md transition-all transform hover:-translate-y-0.5">Enregistrer
                        l'action</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const selectTiers = document.getElementById('id_tiers');
        const inputNom = document.getElementById('emetteur_nom');
        const inputPrenom = document.getElementById('emetteur_prenom');
        const inputContact = document.getElementById('emetteur_contact');
        const checkCreerTiers = document.getElementById('creer_nouveau_tiers');

        selectTiers.addEventListener('change', function () {
            if (this.value !== "") {
                inputNom.disabled = true;
                inputPrenom.disabled = true;
                checkCreerTiers.disabled = true;
                checkCreerTiers.checked = false;
                inputNom.classList.add('bg-slate-100');
                inputPrenom.classList.add('bg-slate-100');
                inputNom.value = "";
                inputPrenom.value = "";
                inputContact.placeholder = "Sera récupéré automatiquement";
            } else {
                inputNom.disabled = false;
                inputPrenom.disabled = false;
                checkCreerTiers.disabled = false;
                inputNom.classList.remove('bg-slate-100');
                inputPrenom.classList.remove('bg-slate-100');
                inputContact.placeholder = "06 00 00 00 00";
            }
        });
    </script>
@endsection