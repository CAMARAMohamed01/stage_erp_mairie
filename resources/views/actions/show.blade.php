@extends('layouts.app')

@section('title', 'Détail du Action #' . $action->id_action)

@section('content')
    <div class="max-w-5xl mx-auto space-y-6 pb-16">

        {{-- FIL D'ARIANE / BREADCRUMB --}}
        <div class="flex items-center justify-between">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2 text-xs font-semibold uppercase tracking-wider">
                    <li class="inline-flex items-center">
                        <a href="{{ route('technique.dashboard') }}"
                            class="text-slate-400 hover:text-slate-700 transition">Mairie</a>
                    </li>
                    <div class="text-slate-300 px-1">/</div>
                    <li>
                        <a href="{{ route('actions.index') }}"
                            class="text-slate-400 hover:text-slate-700 transition">Signalements</a>
                    </li>
                    <div class="text-slate-300 px-1">/</div>
                    <li class="text-slate-800 font-bold">Fiche #{{ $action->id_action }}</li>
                </ol>
            </nav>
            <a href="{{ route('actions.index') }}"
                class="text-xs font-bold text-slate-500 hover:text-slate-800 transition flex items-center gap-1">
                ← RETOUR AU CATALOGUE
            </a>
        </div>

        {{-- GRILLE PRINCIPALE --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- COLONNE GAUCHE : INFOS MAJEURES DE L'INCIDENT --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- BLOC CENTRAL --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8 space-y-6">
                    <div class="flex justify-between items-start border-b border-slate-100 pb-4">
                        <div>
                            <!-- <span class="text-xs font-bold uppercase tracking-widest text-blue-600">Doléance
                                    Citoyenne</span> -->
                            <h1 class="text-3xl font-black text-slate-900 mt-1 tracking-tight">Signalement
                                #{{ $action->id_action }}</h1>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('actions.edit', $action->id_action) }}"
                                class="text-xs bg-slate-100 text-slate-700 hover:bg-slate-200 px-3 py-2 rounded-lg font-bold transition">
                                ✏️ Modifier
                            </a>
                            <form action="{{ route('actions.destroy', $action->id_action) }}" method="POST"
                                onsubmit="return confirm('⚠️ Confirmer la suppression définitive de ce signalement ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-xs bg-red-50 text-red-600 hover:bg-red-100 px-3 py-2 rounded-lg font-bold transition">
                                    🗑️ Supprimer
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- LA DESCRIPTION DE L'ANOMALIE --}}
                    <div class="space-y-2">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Description des faits
                            rapportés</h3>
                        <div
                            class="bg-slate-50 border border-slate-100 rounded-xl p-5 text-slate-700 text-base leading-relaxed font-medium italic">
                            "{!! nl2br(e($action->description)) !!}"
                        </div>
                    </div>

                    {{-- MATRICE DE STATUS / CARACTÉRISTIQUES --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4">
                        <div class="p-3 border border-slate-100 rounded-xl bg-slate-50/50">
                            <span class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Statut</span>
                            <span
                                class="inline-block mt-1 px-2.5 py-0.5 text-xs font-extrabold rounded-full
                                                                {{ $action->statut_action === 'Nouveau' ? 'bg-blue-100 text-blue-800' : ($action->statut_action === 'En cours' ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-800') }}">
                                {{ $action->statut_action }}
                            </span>
                        </div>

                        <div class="p-3 border border-slate-100 rounded-xl bg-slate-50/50">
                            <span
                                class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Priorité</span>
                            <span
                                class="inline-block mt-1 px-2.5 py-0.5 text-xs font-extrabold rounded-full
                                                                {{ $action->priorite === 'Haute' ? 'bg-red-100 text-red-700' : ($action->priorite === 'Normale' ? 'bg-slate-100 text-slate-700' : 'bg-green-100 text-green-700') }}">
                                {{ $action->priorite }}
                            </span>
                        </div>

                        <div class="p-3 border border-slate-100 rounded-xl bg-slate-50/50">
                            <span class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Canal</span>
                            <span class="text-sm font-bold text-slate-800 block mt-1">
                                {{ $action->mode_reception }}
                            </span>
                        </div>

                        <div class="p-3 border border-slate-100 rounded-xl bg-slate-50/50">
                            <span class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Date
                                d'alerte</span>
                            <span class="text-sm font-bold text-slate-800 block mt-1">
                                {{ \Carbon\Carbon::parse($action->date_creation)->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- 📍 BLOC LOCALISATION EXHAUSTIF --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <h3
                        class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5 border-b pb-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                            </path>
                        </svg>
                        Analyse de la Localisation
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- 1. Bâtiment --}}
                        <div
                            class="p-4 rounded-xl border {{ $action->id_batiment ? 'border-indigo-200 bg-indigo-50/30' : 'border-slate-100 bg-slate-50/30' }} flex gap-3 items-start">
                            <span class="text-2xl mt-0.5">🏛️</span>
                            <div>
                                <span
                                    class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Bâtiment</span>
                                @if($action->id_batiment && $action->batiment)
                                    <p class="font-bold text-slate-800 mt-1 text-sm">{{ $action->batiment->nom_bat }}</p>
                                @else
                                    <p class="text-slate-400 text-xs italic mt-1">Aucun bâtiment rattaché</p>
                                @endif
                            </div>
                        </div>

                        {{-- 2. Local Intérieur --}}
                        <div
                            class="p-4 rounded-xl border {{ $action->id_local ? 'border-blue-200 bg-blue-50/20' : 'border-slate-100 bg-slate-50/30' }} flex gap-3 items-start">
                            <span class="text-2xl mt-0.5">🚪</span>
                            <div>
                                <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Pièce /
                                    Local Intérieur</span>
                                @if($action->id_local && $action->local)
                                    <p class="font-bold text-slate-800 mt-1 text-sm">{{ $action->local->nom_local }}</p>
                                    <p class="text-xs text-slate-500 font-medium">Niveau : {{ $action->local->niveau ?? 'RDC' }}
                                    </p>
                                @else
                                    <p class="text-slate-400 text-xs italic mt-1">Aucun local spécifique</p>
                                @endif
                            </div>
                        </div>

                        {{-- 3. Espace Public --}}
                        <div
                            class="p-4 rounded-xl border {{ $action->id_lieu ? 'border-emerald-200 bg-emerald-50/20' : 'border-slate-100 bg-slate-50/30' }} flex gap-3 items-start">
                            <span class="text-2xl mt-0.5">🌳</span>
                            <div>
                                <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Espace
                                    Public</span>
                                @if($action->id_lieu && $action->lieu)
                                    <p class="font-bold text-slate-800 mt-1 text-sm">{{ $action->lieu->nom_lieu }}</p>
                                @else
                                    <p class="text-slate-400 text-xs italic mt-1">Aucun espace public lié</p>
                                @endif
                            </div>
                        </div>

                        {{-- 4. Adresse Voirie (BAN) --}}
                        @php
                            // Détermination de l'adresse "en cascade"
                            $adresseFinale = null;
                            $sourceAdresse = '';

                            if ($action->adresse) {
                                $adresseFinale = $action->adresse;
                            } elseif ($action->batiment && $action->batiment->adresse) {
                                $adresseFinale = $action->batiment->adresse;
                                $sourceAdresse = ' (Déduite du Bâtiment)';
                            } elseif ($action->lieu && $action->lieu->adresse) {
                                // Note: remplace '$action->lieu' par le nom exact de ta relation si c'est '$action->lieuPublic'
                                $adresseFinale = $action->lieu->adresse;
                                $sourceAdresse = ' (Déduite du Lieu)';
                            }
                        @endphp

                        <div
                            class="p-4 rounded-xl border {{ $adresseFinale ? 'border-amber-200 bg-amber-50/20' : 'border-slate-100 bg-slate-50/30' }} flex gap-3 items-start">
                            <span class="text-2xl mt-0.5">🛣️</span>
                            <div>
                                <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                    Adresse / Voirie <span
                                        class="text-amber-600 font-normal italic lowercase">{{ $sourceAdresse }}</span>
                                </span>

                                @if($adresseFinale)
                                    <p class="font-bold text-slate-800 mt-1 text-sm">
                                        {{ $adresseFinale->num_rue }} {{ $adresseFinale->nom_voie }}
                                    </p>
                                    <p class="text-xs text-slate-500 font-medium">
                                        {{ $adresseFinale->code_postal }} {{ $adresseFinale->ville }}
                                    </p>
                                @else
                                    <p class="text-slate-400 text-xs italic mt-1">Aucune adresse directe ou liée</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLONNE DROITE : EMETTEUR & CLASSIFICATION --}}
            <div class="space-y-6">

                {{-- BLOC EMETTEUR --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <h3
                        class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5 border-b pb-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Détails du Déclarant
                    </h3>

                    @php
                        $nomAffiche = $action->emetteur_nom;
                        if ($action->id_tiers) {
                            $citoyen = \App\Models\TiersPhysique::where('id_tiers', $action->id_tiers)->first();
                            if ($citoyen) {
                                $nomAffiche = $citoyen->prenom_tiers . ' ' . $citoyen->nom_tiers;
                            }
                        }
                    @endphp

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                            <span class="text-slate-400 font-medium">Identité :</span>
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-slate-800">{{ $nomAffiche }}</span>
                                @if($action->id_tiers)
                                    <span
                                        class="bg-blue-100 text-blue-700 font-black text-[9px] px-1.5 py-0.5 rounded uppercase tracking-wide">Fiche
                                        Tiers</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                            <span class="text-slate-400 font-medium">Contact :</span>
                            <span class="font-bold text-slate-700">{{ $action->emetteur_contact ?? 'Non renseigné' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400 font-medium">Créé par agent :</span>
                            <span
                                class="text-xs bg-slate-100 text-slate-700 px-2.5 py-1 font-extrabold rounded-md border border-slate-200 uppercase tracking-wider"
                                title="Enregistré par {{ $action->agent->prenom_user ?? 'Agent' }} {{ $action->agent->nom_user ?? '' }}">
                                👤 {{ $action->agent->initiales ?? 'AG' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- BLOC METIER INTERNE --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <h3
                        class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5 border-b pb-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                        Rattachement Service
                    </h3>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                            <span class="text-slate-400 font-medium">Corps d'état :</span>
                            <span
                                class="font-black text-slate-800">{{ $action->categorie->libelle ?? 'Non définie' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400 font-medium">Attribution :</span>
                            <span
                                class="text-xs font-bold px-2 py-1 bg-indigo-50 text-indigo-700 rounded-lg border border-indigo-100">👷
                                Services Techniques</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BARRE DE COMMANDE ET DE WORKFLOW CRUCIFORME (STICKY BANNER ECO-DESIGN) --}}
        <div class="bg-white border border-slate-200 shadow-lg rounded-xl p-4 flex items-center justify-between gap-4 mt-6">
            <div>
                @if(session('success'))
                    <span
                        class="text-green-700 font-bold text-xs flex items-center bg-green-50 px-3 py-2 rounded-xl border border-green-200">
                        {{ session('success') }}
                    </span>
                @else
                    <p class="text-xs text-slate-400 font-semibold italic pl-2">Prêt pour traitement technique municipal.</p>
                @endif
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('action.pdf', $action->id_action) }}" target="_blank"
                    class="px-4 py-2 border border-slate-300 text-slate-700 rounded-lg font-bold hover:bg-slate-50 transition shadow-sm text-xs">
                    🖨️ Récépissé
                </a>

                @if($action->statut_action === 'Nouveau')
                    <form action="{{ route('action.prendre-en-charge', $action->id_action) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="px-4 py-2 bg-slate-900 text-white rounded-lg font-bold hover:bg-slate-800 shadow-sm transition text-xs flex items-center gap-1">
                            🤝 Prendre en charge
                        </button>
                    </form>
                @else
                    <button disabled
                        class="px-4 py-2 bg-slate-100 text-slate-400 rounded-lg font-bold cursor-not-allowed italic text-xs">
                        ✓ Déjà pris en charge
                    </button>
                @endif

                @if($action->statut_action !== 'Transmis')
                    <form action="{{ route('action.creer-intervention', $action->id_action) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="px-5 py-2 bg-blue-600 text-white font-black rounded-lg hover:bg-blue-700 shadow transition text-xs flex items-center gap-1">
                            ⚡ Planifier Travaux (GMAO)
                        </button>
                    </form>
                @else
                    <button disabled
                        class="px-4 py-2 bg-green-50 border border-green-200 text-green-700 rounded-lg font-black text-xs cursor-not-allowed">
                        Envoyé aux services techniques
                    </button>
                @endif
            </div>
        </div>
    </div>
@endsection