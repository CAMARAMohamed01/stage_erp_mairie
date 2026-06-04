@extends('layouts.app')

@section('title', 'Registre des actions')

@section('content')
    {{-- Passage à max-w-6xl pour donner plus d'espace et d'envergure à la page --}}
    <div class="max-w-6xl mx-auto bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- EN-TÊTE DE LA PAGE --}}
        <div class="bg-white px-8 py-6 border-b border-slate-200 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Registre des actions</h1>
                <p class="text-sm text-slate-500 mt-1">Suivi exhaustif des doléances et incidents techniques de la commune
                </p>
            </div>
            <div class="flex items-center gap-3">
                @can('check-permission', ['actions', 'lecture'])
                    <a href="{{ route('actions.excel') }}"
                        class="flex items-center gap-2 bg-white border border-slate-300 px-4 py-2.5 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50 transition shadow-sm">
                        Exporter (.CSV)
                    </a>
                @endcan

                @can('check-permission', ['actions', 'ecriture'])
                    {{-- Remis en bleu vif pour une action majeure évidente --}}
                    <a href="{{ route('actions.create') }}"
                        class="flex items-center gap-2 bg-blue-600 px-5 py-2.5 rounded-lg text-sm font-bold text-white hover:bg-blue-700 shadow-md transition">
                        ➕ Nouvelle action
                    </a>
                @endcan
            </div>
        </div>

        {{-- BARRE DE RECHERCHE ET FILTRES --}}
        <div class="p-6 border-b border-slate-200 bg-slate-50/50">
            <form action="{{ route('actions.index') }}" method="GET" class="flex flex-wrap gap-3">
                <div class="relative flex-grow max-w-md">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Rechercher par émetteur ou description..."
                        class="w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm bg-white transition">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <select name="statut"
                    class="border border-slate-300 rounded-lg py-2 px-3 text-sm focus:ring-2 focus:ring-blue-500 text-slate-700 bg-white shadow-sm">
                    <option value="">Tous les statuts</option>
                    <option value="Nouveau" {{ request('statut') == 'Nouveau' ? 'selected' : '' }}>Nouveau</option>
                    <option value="En cours" {{ request('statut') == 'En cours' ? 'selected' : '' }}>En cours</option>
                    <option value="Transmis" {{ request('statut') == 'Transmis' ? 'selected' : '' }}>Transmis</option>
                    <option value="Abandonné" {{ request('statut') == 'Abandonné' ? 'selected' : '' }}>Abandonné</option>
                    <option value="Terminé" {{ request('statut') == 'Terminé' ? 'selected' : '' }}>Terminé</option>
                </select>

                <select name="categorie_id"
                    class="border border-slate-300 rounded-lg py-2 px-3 text-sm focus:ring-2 focus:ring-blue-500 text-slate-700 bg-white shadow-sm">
                    <option value="">Toutes catégories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id_cat }}" {{ request('categorie_id') == $cat->id_cat ? 'selected' : '' }}>
                            {{ $cat->libelle }}
                        </option>
                    @endforeach
                </select>

                <button type="submit"
                    class="px-5 py-2 bg-slate-800 text-white text-sm font-bold rounded-lg hover:bg-slate-700 transition shadow-sm">
                    Filtrer
                </button>

                @if(request()->anyFilled(['search', 'statut', 'categorie_id']))
                    <a href="{{ route('actions.index') }}"
                        class="px-4 py-2 bg-slate-200 text-slate-700 text-sm font-bold rounded-lg hover:bg-slate-300 transition">
                        Réinitialiser
                    </a>
                @endif
            </form>
        </div>

        {{-- TABLEAU GRAND FORMAT ET LISIBLE --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="border-b border-slate-200 text-slate-400 text-xs uppercase tracking-wider font-bold bg-slate-50/50">
                        <th class="px-6 py-4 w-20">Réf</th>
                        <th class="px-6 py-4 w-32">Date</th>
                        <th class="px-6 py-4 w-52">Citoyen / Demandeur</th>
                        <th class="px-6 py-4">Description & Localisation</th>
                        <th class="px-6 py-4 w-28 text-center">Urgence</th>
                        <th class="px-6 py-4 w-24 text-center">Agent</th>
                        <th class="px-6 py-4 w-28 text-center">Statut</th>
                        <th class="px-6 py-4 text-right w-28">Action</th>
                    </tr>
                </thead>
                {{-- Augmentation globale de la taille de police (text-sm et text-base) --}}
                <tbody class="divide-y divide-slate-100 bg-white text-sm text-slate-700">
                    @forelse($actions as $sig)
                        <tr class="hover:bg-slate-50/40 transition-colors">
                            {{-- Référence --}}
                            <td class="px-6 py-5 font-mono font-bold text-slate-400 text-sm">#{{ $sig->id_action }}</td>

                            {{-- Date --}}
                            <td class="px-6 py-5 text-slate-600 font-semibold">
                                {{ \Carbon\Carbon::parse($sig->date_creation)->format('d/m/Y') }}
                            </td>

                            {{-- Émetteur --}}
                            <td class="px-6 py-5">
                                <div class="font-bold text-slate-900 text-base truncate max-w-[180px]">{{ $sig->emetteur_nom }}
                                </div>
                                <div class="text-slate-400 text-xs mt-0.5 font-medium">Via {{ $sig->mode_reception }}</div>
                            </td>

                            {{-- Description & Géo-localisation --}}
                            <td class="px-6 py-5">
                                <div class="text-slate-800 font-medium text-sm line-clamp-1" title="{{ $sig->description }}">
                                    {{ Str::limit($sig->description, 80) }}
                                </div>
                                @if($sig->id_local && $sig->local)
                                    <div class="text-xs text-slate-400 mt-1 font-semibold flex items-center gap-1">
                                        🏢 Intérieur : {{ $sig->local->nom_local }}
                                    </div>
                                @elseif($sig->id_adresse && $sig->adresse)
                                    <div class="text-xs text-slate-400 mt-1 font-semibold flex items-center gap-1">
                                        📍 Extérieur : {{ $sig->adresse->num_rue }} {{ $sig->adresse->nom_voie }}
                                    </div>
                                @endif
                            </td>

                            {{-- Priorité --}}
                            <td class="px-6 py-5 text-center font-black text-xs uppercase tracking-wide">
                                <span
                                    class="{{ $sig->priorite === 'Haute' ? 'text-red-600' : ($sig->priorite === 'Normale' ? 'text-slate-500' : 'text-green-600') }}">
                                    {{ $sig->priorite }}
                                </span>
                            </td>

                            {{-- Initiales Agent --}}
                            <td class="px-6 py-5 text-center">
                                <span class="text-slate-600 font-mono font-extrabold text-sm"
                                    title="Enregistré par l'Agent ID #{{ $sig->id_user }}">
                                    {{ $sig->createur->initiales ?? 'AG' }}
                                </span>
                            </td>

                            {{-- Statut --}}
                            <td class="px-6 py-5 text-center">
                                <span class="px-3 py-0.5 rounded text-xs font-bold border 
                                                                                    {{ $sig->statut_action === 'Nouveau' ? 'border-slate-300 text-slate-700 bg-slate-50' : '' }}
                                                                                    {{ $sig->statut_action === 'En cours' ? 'border-amber-300 text-amber-800 bg-amber-50/20' : '' }}
                                                                                    {{ $sig->statut_action === 'Transmis' ? 'border-blue-300 text-blue-800 bg-blue-50/20' : '' }}
                                                                                    {{ $sig->statut_action === 'Terminé' ? 'border-green-300 text-green-800 bg-green-50/20' : '' }}
                                                                                    {{ $sig->statut_action === 'Abandonné' ? 'border-slate-200 text-slate-400 bg-white' : '' }}
                                                                                ">
                                    {{ $sig->statut_action }}
                                </span>
                            </td>

                            {{-- Lien vers la fiche : Remis en bouton bleu épuré --}}
                            <td class="px-6 py-5 text-right">
                                <a href="{{ route('actions.show', $sig->id_action) }}"
                                    class="inline-block text-blue-600 hover:text-blue-800 font-bold text-xs bg-blue-50 hover:bg-blue-100/80 px-3 py-2 rounded-lg border border-blue-100/60 tracking-wider transition">
                                    Consulter
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-10 text-center text-slate-400 italic bg-slate-50/10 text-sm">
                                Aucun signalement ou action recensé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection