@extends('layouts.app')

@section('title', 'Fiche Intervention #' . $intervention->id_int)

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('interventions.index') }}"
            class="text-slate-500 hover:text-slate-800 text-sm flex items-center transition">
            ← Retour à la liste
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- COLONNE GAUCHE (Principale) --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- EN-TÊTE ET DESCRIPTION --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <span class="text-blue-600 font-bold text-sm uppercase tracking-wider">Bon de travaux</span>
                        <h1 class="text-3xl font-extrabold text-slate-900 mt-1">{{ $intervention->type_intervention }}
                        </h1>
                        @if($intervention->Autre)
                        <span
                            class="inline-block bg-slate-100 text-slate-600 text-xs px-2 py-1 rounded mt-2 border border-slate-200">
                            Détail / Autre : {{ $intervention->Autre }}
                        </span>
                        @endif
                    </div>

                    @if(Auth::user()->role_appli === 'Administrateur' || Auth::user()->role_appli === 'Responsable
                    technique')
                    <div class="flex items-center gap-2">
                        <a href="{{ route('interventions.edit', $intervention->id_int) }}"
                            class="text-xs bg-amber-100 text-amber-700 hover:bg-amber-200 px-3 py-1.5 rounded-lg font-bold transition">
                            ✏️ Modifier
                        </a>

                        <form action="{{ route('interventions.destroy', $intervention->id_int) }}" method="POST"
                            onsubmit="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer cette intervention ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-xs bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1.5 rounded-lg font-bold transition">
                                🗑️ Supprimer
                            </button>
                        </form>
                    </div>
                    @endif
                </div>

                <div class="prose max-w-none text-slate-600 mb-8 p-4 bg-slate-50/50 rounded-lg border border-slate-100">
                    <h3 class="text-slate-900 font-semibold mb-2">Description du travail à effectuer :</h3>
                    <p class="leading-relaxed whitespace-pre-line">{{ $intervention->description }}</p>
                </div>

                {{-- BLOC FINANCIER GLOBAL CONSOLIDÉ --}}
                <div
                    class="mb-6 p-4 bg-slate-50 border border-slate-200 rounded-xl grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                    <div class="border-r border-slate-200 md:last:border-0 pb-4 md:pb-0 border-b md:border-b-0">
                        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Fournitures</span>
                        <p class="text-lg font-extrabold text-slate-700 mt-0.5">
                            {{ number_format($coutMateriels ?? 0, 2, ',', ' ') }} €
                        </p>
                    </div>
                    <div class="border-r border-slate-200 md:last:border-0 pb-4 md:pb-0 border-b md:border-b-0">
                        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Temps / Suivis</span>
                        <p class="text-lg font-extrabold text-slate-700 mt-0.5">
                            {{ number_format($coutSuivi ?? 0, 2, ',', ' ') }} €
                        </p>
                    </div>
                    <div class="bg-blue-50/50 rounded-lg p-2 border border-blue-100/50 shadow-sm">
                        <span class="text-xs text-blue-500 font-bold uppercase tracking-wider">Coût Total</span>
                        <p class="text-lg font-black text-blue-700 mt-0.5">
                            {{ number_format($coutTotalIntervention ?? 0, 2, ',', ' ') }} € <span
                                class="text-xs font-bold text-blue-500">HT</span>
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 pt-6 border-t border-slate-100">
                    <div>
                        <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Ouverture</p>
                        <p class="text-slate-800 font-medium mt-1">
                            {{ \Carbon\Carbon::parse($intervention->date_ouverture)->format('d/m/Y') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Catégorie</p>
                        <p class="text-slate-800 font-medium mt-1">{{ $intervention->categorie->libelle ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Code budget</p>
                        @if($intervention->code_budget)
                        <span
                            class="inline-block bg-slate-100 text-slate-800 font-bold px-2 py-0.5 rounded border border-slate-200 mt-1">
                            {{ strtoupper($intervention->code_budget) }}
                        </span>
                        @else
                        <p class="text-slate-400 italic text-sm mt-1">N/A</p>
                        @endif
                    </div>

                    <div>
                        <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Statut</p>
                        <span
                            class="inline-block mt-1 px-2.5 py-1 text-xs font-bold rounded-full 
                                {{ $intervention->statut_global === 'Terminée' ? 'bg-green-100 text-green-800' : ($intervention->statut_global === 'En cours' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                            {{ $intervention->statut_global }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- TABLEAU DU SUIVI ET DES CR --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-slate-800 mb-6 border-b pb-2">Historique des interventions sur le
                    terrain</h2>
                @if($intervention->suiviActions && $intervention->suiviActions->count() > 0)
                <div class="space-y-6">
                    @foreach($intervention->suiviActions as $action)
                    <div class="flex gap-4 pb-6 border-l-2 border-slate-100 ml-3 pl-6 relative">
                        <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-blue-500 border-2 border-white">
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-bold text-slate-900">Passage du
                                        {{ \Carbon\Carbon::parse($action->date_action_suivi)->format('d/m/Y') }}</p>
                                    <p class="text-xs text-slate-500 mt-0.5">Par :
                                        {{ $action->utilisateur->prenom_user ?? 'Agent' }}
                                        {{ $action->utilisateur->nom_user ?? '' }}</p>
                                    @if($action->cout_associe > 0)
                                    <span
                                        class="text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded mt-2 inline-block">
                                        💰 Coût engagé : {{ number_format($action->cout_associe, 2, ',', ' ') }} €
                                    </span>
                                    @endif
                                </div>
                                <span class="text-xs bg-slate-100 px-2 py-1 rounded text-slate-500 font-medium">
                                    ⏳ {{ $action->temps_passe_heures }}h passées
                                </span>
                            </div>
                            <div class="mt-3 bg-slate-50 p-3 rounded-lg border border-slate-100">
                                <p class="text-slate-700 text-sm leading-relaxed">{{ $action->description_etape }}</p>
                            </div>
                            <p class="text-xs mt-2 font-semibold text-blue-600 flex items-center gap-1">
                                <span>Statut après action :</span>
                                <span class="bg-blue-50 px-1.5 py-0.5 rounded">{{ $action->statut_apres_action }}</span>
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-slate-400 italic">Aucun compte-rendu de terrain pour le moment.</p>
                @endif
            </div>

            {{-- MATÉRIELS --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mt-6">
                <div
                    class="p-4 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">📦 Fournitures & Matériels
                        consommés</h3>
                    <span class="text-xs font-bold bg-blue-100 text-blue-800 px-2.5 py-1 rounded-full shadow-sm">
                        Total matériel : {{ number_format($coutMateriels ?? 0, 2, ',', ' ') }} € HT
                    </span>
                </div>

                <div class="divide-y divide-slate-100 bg-white">
                    @forelse($intervention->achatsMateriels ?? [] as $mat)
                    <div class="p-4 flex justify-between items-center text-sm hover:bg-slate-50/50 transition">
                        <div>
                            <p class="font-semibold text-slate-800">{{ $mat->nom_materiel }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Qté : <span class="font-medium text-slate-700">{{ $mat->quantite }}
                                    {{ $mat->unite_mesure }}</span>
                                | PU : <span
                                    class="font-medium text-slate-700">{{ number_format($mat->prix_unitaire_ht, 2, ',', ' ') }}
                                    €</span>
                            </p>
                        </div>
                        <div class="text-right flex items-center gap-4">
                            <div>
                                <span
                                    class="font-bold text-slate-900">{{ number_format($mat->quantite * $mat->prix_unitaire_ht, 2, ',', ' ') }}
                                    €</span>
                                <p class="text-[10px] text-slate-400 uppercase tracking-wider mt-0.5">
                                    {{ \Carbon\Carbon::parse($mat->date_achat)->format('d/m/Y') }}</p>
                            </div>
                            @can('check-permission', ['Interventions', 'ecriture'])
                            <form action="{{ route('interventions.materiel.destroy', $mat->id_achat) }}" method="POST"
                                onsubmit="return confirm('Supprimer cette ligne ?');">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="text-red-400 hover:text-red-600 bg-red-50 hover:bg-red-100 p-1.5 rounded transition"
                                    title="Supprimer">
                                    ❌
                                </button>
                            </form>
                            @endcan
                        </div>
                    </div>
                    @empty
                    <p class="p-6 text-center text-sm text-slate-400 italic">Aucune fourniture enregistrée sur cette
                        intervention.</p>
                    @endforelse
                </div>

                @if(auth()->user()->can('check-permission', ['Interventions', 'ecriture']))
                <div class="p-4 bg-slate-50/80 border-t border-slate-100">
                    <span class="text-xs font-bold uppercase text-slate-500 tracking-wider block mb-3">➕ Ajouter une
                        fourniture</span>
                    <form action="{{ route('interventions.materiel.store', $intervention->id_int) }}" method="POST"
                        class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        @csrf
                        <div class="sm:col-span-2">
                            <input type="text" name="nom_materiel" required placeholder="Désignation (Ex: Vanne PVC...)"
                                class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500 transition shadow-sm">
                        </div>
                        <div>
                            <div class="flex gap-1 shadow-sm rounded-lg overflow-hidden border border-slate-300">
                                <input type="number" step="0.01" name="quantite" required placeholder="Qté"
                                    class="w-2/3 text-sm px-2 py-2 outline-none focus:ring-2 focus:ring-blue-500 border-none">
                                <input type="text" name="unite_mesure" placeholder="Unité" value="U"
                                    class="w-1/3 text-sm px-1 py-2 text-center bg-slate-100 border-l border-slate-300 outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        <div>
                            <input type="number" step="0.01" name="prix_unitaire_ht" required
                                placeholder="Prix U. HT (€)"
                                class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition">
                        </div>
                        <div class="sm:col-span-4 flex justify-between items-center mt-2">
                            <div class="flex items-center gap-2">
                                <label class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">Date
                                    d'achat :</label>
                                <input type="date" name="date_achat" value="{{ now()->format('Y-m-d') }}" required
                                    class="text-sm border border-slate-300 rounded-lg px-2 py-1 bg-white text-slate-600 shadow-sm outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm px-4 py-2 rounded-lg shadow-sm transition">
                                Ajouter
                            </button>
                        </div>
                    </form>
                </div>
                @endif
            </div>
        </div>

        {{-- PANNEAU LATÉRAL DROIT --}}
        <div class="space-y-6">

            {{-- ACTIONS RAPIDES --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h3 class="font-bold text-slate-900 mb-4 border-b border-slate-100 pb-2">Actions disponibles</h3>
                @if($intervention->statut_global !== 'Terminée')
                @if(in_array(Auth::user()->role_appli, ['Responsable technique', 'Technicien', 'Administrateur']))
                <a href="{{ route('interventions.cloturer.form', $intervention->id_int) }}"
                    class="w-full block text-center bg-green-600 text-white font-bold py-2.5 rounded-lg hover:bg-green-700 transition shadow-sm mb-3 text-sm">
                    ✓ Saisir un CR / Clôturer
                </a>
                @endif
                @endif
                <a href="{{ route('interventions.pdf', $intervention->id_int) }}"
                    class="w-full bg-white border border-slate-300 text-slate-700 py-2.5 rounded-lg hover:bg-slate-50 transition text-sm text-center block font-medium shadow-sm">
                    🖨️ Imprimer le bon (PDF)
                </a>
            </div>

            {{-- NOUVEAU BLOC : PROJET LIÉ --}}
            @if($intervention->projet)
            <div
                class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl border border-indigo-200 shadow-sm p-6 relative overflow-hidden">
                <div class="absolute -right-4 -top-4 text-indigo-200 opacity-50 text-6xl">📁</div>
                <h3
                    class="text-sm font-bold text-indigo-900 uppercase tracking-wider mb-3 flex items-center relative z-10">
                    <span class="mr-2">📁</span> Rattaché au Projet
                </h3>
                <div class="relative z-10">
                    <p class="font-extrabold text-indigo-950 text-base leading-tight">
                        {{ $intervention->projet->nom_projet }}</p>
                    @if($intervention->projet->budget_global_alloue)
                    <p class="text-xs font-medium text-indigo-700 mt-2 bg-indigo-200/50 inline-block px-2 py-1 rounded">
                        Budget Alloué : {{ number_format($intervention->projet->budget_global_alloue, 2, ',', ' ') }} €
                    </p>
                    @endif
                    <a href="{{ route('projets.show', $intervention->projet->id_projet) }}"
                        class="mt-4 w-full block text-center bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-2 rounded-lg transition shadow-sm">
                        Voir le détail du Projet →
                    </a>
                </div>
            </div>
            @endif

            {{-- LOCALISATION UNIFIÉE --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h3 class="font-bold text-slate-900 mb-4 border-b border-slate-100 pb-2">Localisation & Périmètre</h3>
                <div class="space-y-4">
                    {{-- Bâtiment --}}
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">🏛️ Bâtiment
                            communal</span>
                        @if($intervention->batiment)
                        <p class="text-sm font-bold text-slate-800 mt-1">{{ $intervention->batiment->nom_bat }}</p>
                        @elseif($intervention->local && $intervention->local->batiment)
                        <p class="text-sm font-bold text-slate-800 mt-1">{{ $intervention->local->batiment->nom_bat }}
                        </p>
                        <span class="text-[10px] text-slate-400 italic">(Déduit via le local)</span>
                        @else
                        <p class="text-xs text-slate-400 italic mt-1 border-l-2 border-slate-200 pl-2">Aucun bâtiment
                            spécifique lié.</p>
                        @endif
                    </div>

                    {{-- Local --}}
                    <div class="pt-3 border-t border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">🚪 Local /
                            Pièce</span>
                        @if($intervention->local)
                        <p class="text-sm font-bold text-slate-800 mt-1">{{ $intervention->local->nom_local }}</p>
                        <span class="text-xs text-slate-500 bg-slate-100 px-2 py-0.5 rounded mt-1 inline-block">Niveau :
                            {{ $intervention->local->niveau ?? 'RDC' }}</span>
                        @else
                        <p class="text-xs text-slate-400 italic mt-1 border-l-2 border-slate-200 pl-2">Aucun local
                            intérieur rattaché.</p>
                        @endif
                    </div>

                    {{-- Espace Public --}}
                    <div class="pt-3 border-t border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">🌳 Espace
                            Public</span>
                        @if($intervention->lieuPublic)
                        <div class="bg-slate-50 p-2 rounded-lg border border-slate-200 text-sm mt-1.5">
                            <p class="font-bold text-slate-800">{{ $intervention->lieuPublic->nom_lieu }}</p>
                            @if($intervention->lieuPublic->typologie_lieu)
                            <span
                                class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">{{ $intervention->lieuPublic->typologie_lieu }}</span>
                            @endif
                        </div>
                        @else
                        <p class="text-xs text-slate-400 italic mt-1 border-l-2 border-slate-200 pl-2">Aucun espace
                            public extérieur lié.</p>
                        @endif
                    </div>

                    {{-- Tronçon routier / Voie --}}
                    <div class="pt-3 border-t border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">🛣️ Voirie /
                            Tronçon</span>
                        @if($intervention->troncon)
                        <p class="text-sm font-bold text-slate-800 mt-1">
                            {{ $intervention->troncon->numero_troncon ?? 'Tronçon' }}</p>
                        <span class="text-xs text-slate-500">{{ $intervention->troncon->nom_portion ?? '' }}</span>
                        @else
                        <p class="text-xs text-slate-400 italic mt-1 border-l-2 border-slate-200 pl-2">Aucune voie liée.
                        </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ÉLÉMENTS TECHNIQUES (Equipements, Compteurs, Contrats) --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h3 class="font-bold text-slate-900 mb-4 border-b border-slate-100 pb-2">Réseaux & Équipements</h3>

                {{-- Equipements --}}
                @if($intervention->equipements && $intervention->equipements->count() > 0)
                <ul class="space-y-2 mb-4">
                    @foreach($intervention->equipements as $equip)
                    <li class="bg-slate-50 p-2.5 rounded-lg border border-slate-200">
                        <a href="{{ route('equipements.show', $equip->id_equipement) }}"
                            class="text-blue-600 font-bold hover:underline block text-sm">⚙️
                            {{ $equip->nom_equipement }}</a>
                        <span class="text-[10px] text-slate-500 uppercase tracking-wider">Réf:
                            {{ $equip->reference_serie ?? 'N/A' }}</span>
                    </li>
                    @endforeach
                </ul>
                @endif

                {{-- Compteur lié --}}
                @if($intervention->compteur)
                <div class="bg-amber-50 border border-amber-100 p-2.5 rounded-lg mb-4">
                    <p class="text-xs font-bold text-amber-900 flex items-center gap-1"><span
                            class="text-base">⏱️</span> Compteur : {{ $intervention->compteur->numero_compteur }}</p>
                    <p class="text-[10px] text-amber-700 mt-1">Réseau : {{ $intervention->compteur->type_reseau }}</p>
                </div>
                @endif

                {{-- Contrat lié --}}
                @if($intervention->contrat)
                <div class="bg-purple-50 border border-purple-100 p-2.5 rounded-lg">
                    <p class="text-xs font-bold text-purple-900 flex items-center gap-1"><span
                            class="text-base">📄</span> Contrat n°{{ $intervention->contrat->numero_contrat }}</p>
                    <p class="text-[10px] text-purple-700 mt-1">Type : {{ $intervention->contrat->type_contrat }}</p>
                </div>
                @endif

                @if(!($intervention->equipements && $intervention->equipements->count() > 0) && !$intervention->compteur
                && !$intervention->contrat)
                <p class="text-xs text-slate-400 italic">Aucun équipement, réseau ou contrat lié.</p>
                @endif
            </div>

            {{-- ÉQUIPE ASSIGNÉE ET PRESTATAIRE --}}
            <div class="bg-white rounded-xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between">
                <div>
                    <h3
                        class="text-sm font-bold text-slate-800 uppercase mb-4 flex items-center border-b border-slate-100 pb-2">
                        <span class="mr-2">👷</span> Équipe / Intervenants
                    </h3>

                    @if($intervention->tiers)
                    <div class="p-3 bg-indigo-50 border border-indigo-200 shadow-sm rounded-lg mb-4">
                        <span
                            class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider block mb-1">Prestataire
                            Externe</span>
                        <p class="text-sm font-bold text-indigo-900">{{ $intervention->tiers->nom_tiers }}</p>
                        @if($intervention->tiers->telephone || $intervention->tiers->email)
                        <div class="mt-2 text-xs text-indigo-700 space-y-1 border-t border-indigo-200/60 pt-2">
                            @if($intervention->tiers->telephone)<p>📞 {{ $intervention->tiers->telephone }}</p>@endif
                            @if($intervention->tiers->email)<p>✉️ {{ $intervention->tiers->email }}</p>@endif
                        </div>
                        @endif
                    </div>
                    @else
                    <div
                        class="p-2 mb-4 bg-emerald-50 border border-emerald-200 rounded-lg flex items-center gap-2 shadow-sm">
                        <span class="text-emerald-600 text-xs">✔️</span>
                        <p class="text-xs font-bold text-emerald-800">Intervention en Régie Interne</p>
                    </div>
                    @endif

                    @if($intervention->agents && $intervention->agents->count() > 0)
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Agents
                        communaux assignés</span>
                    <ul class="space-y-2">
                        @foreach($intervention->agents as $agent)
                        <li
                            class="flex items-center gap-3 p-2 bg-slate-50 rounded-lg border border-slate-200 shadow-sm">
                            <div
                                class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 border border-blue-200 flex items-center justify-center font-bold text-xs">
                                {{ substr($agent->prenom_user, 0, 1) }}{{ substr($agent->nom_user, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">{{ $agent->prenom_user }}
                                    {{ $agent->nom_user }}</p>
                                <p class="text-[10px] font-medium text-slate-500 uppercase">
                                    {{ $agent->role_appli ?? 'Agent technique' }}</p>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>

            {{-- PIÈCES JOINTES --}}
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h2
                    class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2 flex items-center gap-2">
                    <span>📸</span> Photos & Documents
                </h2>
                <ul class="space-y-2">
                    @forelse($documents ?? [] as $doc)
                    <li
                        class="flex items-center justify-between p-2 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 transition shadow-sm">
                        <div class="flex items-center gap-2 overflow-hidden">
                            <span class="text-lg">
                                {{ in_array(strtolower($doc->type_doc), ['pdf']) ? '📄' : (in_array(strtolower($doc->type_doc), ['jpg', 'png', 'jpeg']) ? '🖼️' : '📁') }}
                            </span>
                            <div class="truncate">
                                <p class="text-xs font-bold text-slate-800 truncate">{{ $doc->nom_fichier }}</p>
                                <p class="text-[10px] text-slate-500">{{ number_format($doc->taille_ko, 0, ',', ' ') }}
                                    Ko</p>
                            </div>
                        </div>
                        <a href="{{ asset('storage/' . $doc->chemin_stockage) }}" target="_blank"
                            class="text-blue-600 hover:text-blue-800 text-[10px] font-bold uppercase tracking-wider bg-blue-50 px-2 py-1 rounded border border-blue-100 ml-2 shrink-0">Voir</a>
                    </li>
                    @empty
                    <li class="text-xs text-slate-400 italic text-center py-2">Aucun document joint.</li>
                    @endforelse
                </ul>
            </div>

            {{-- TRAÇABILITÉ & DEMANDEUR --}}
            <div class="bg-slate-100 rounded-xl p-6 border border-slate-200">
                <h3
                    class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4 border-b border-slate-200 pb-2">
                    Traçabilité & Origine</h3>

                @if($intervention->demandeur)
                <div class="mb-4">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">👤 Demandeur
                        initial</span>
                    <p class="text-sm font-bold text-slate-800 mt-0.5">{{ $intervention->demandeur->prenom_user }}
                        {{ $intervention->demandeur->nom_user }}</p>
                    @if($intervention->demandeur->service)
                    <p class="text-[10px] text-slate-500 uppercase">{{ $intervention->demandeur->service->nom_service }}
                    </p>
                    @endif
                </div>
                @endif

                @if($intervention->id_action)
                <div class="pt-2 border-t border-slate-200">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">⚠️ Signalement
                        d'origine</span>
                    <p class="text-sm text-slate-600 mt-1 font-medium">Action #{{ $intervention->id_action }}</p>
                    <a href="{{ route('actions.show', $intervention->id_action) }}"
                        class="inline-block mt-1 text-blue-600 text-xs font-bold hover:underline">Voir le signalement
                        initial →</a>
                </div>
                @elseif(!$intervention->demandeur)
                <p class="text-xs text-slate-400 italic">Créé manuellement, aucun demandeur ni action spécifiés.</p>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection