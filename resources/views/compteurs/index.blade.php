@extends('layouts.app')

@section('header_title', 'Registre des Compteurs')

@section('content')
    <div class="max-w-6xl mx-auto pb-12">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Réseaux & Compteurs</h1>
                <p class="text-sm text-slate-500 mt-1">Gestion des points de comptage d'eau, gaz, électricité.</p>
            </div>

            @if(auth()->user()->can('check-permission', ['Patrimoine', 'ecriture']))
                <a href="{{ route('compteurs.create') }}"
                    class="px-4 py-2 bg-slate-900 text-white font-bold rounded-lg shadow hover:bg-slate-800 transition flex items-center gap-2">
                    <span>➕ Ajouter un compteur</span>
                </a>
            @endif
        </div>
        <form action="{{ route('compteurs.index') }}" method="GET" class="flex flex-wrap gap-2 mb-6">
            <div class="relative flex-grow max-w-sm">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..."
                    class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-500 shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <select name="statut"
                class="border border-slate-300 rounded-lg py-2 px-3 shadow-sm focus:ring-2 focus:ring-slate-500">
                <option value="">Tous les statuts</option>
                <option value="en_service" {{ request('statut') == 'en_service' ? 'selected' : '' }}>En service</option>
                <option value="depose" {{ request('statut') == 'depose' ? 'selected' : '' }}>Déposé / Arrêté</option>
            </select>

            <select name="type_reseau"
                class="border border-slate-300 rounded-lg py-2 px-3 shadow-sm focus:ring-2 focus:ring-slate-500">
                <option value="">Tous les réseaux</option>
                <option value="Électricité" {{ request('type_reseau') == 'Électricité' ? 'selected' : '' }}>Électricité
                </option>
                <option value="Eau Potable" {{ request('type_reseau') == 'Eau Potable' ? 'selected' : '' }}>Eau Potable
                </option>
                <option value="Gaz" {{ request('type_reseau') == 'Gaz' ? 'selected' : '' }}>Gaz</option>
                <option value="Chauffage" {{ request('type_reseau') == 'Chauffage' ? 'selected' : '' }}>Chauffage</option>
            </select>

            <button type="submit"
                class="px-4 py-2 bg-slate-800 text-white font-semibold rounded-lg hover:bg-slate-700 transition">
                Filtrer
            </button>

            @if(request()->filled('search') || request()->filled('statut') || request()->filled('type_reseau'))
                <a href="{{ route('compteurs.index') }}"
                    class="px-4 py-2 bg-slate-100 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition">
                    Réinitialiser
                </a>
            @endif
        </form>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-bold">
                        <th class="p-4">Réseau & Point de comptage</th>
                        <th class="p-4">Localisation (Bâtiment/Local)</th>
                        <th class="p-4">Contrat lié</th>
                        <th class="p-4">Statut</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($compteurs as $compteur)
                        <tr class="hover:bg-slate-50 transition text-sm">

                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="text-xl">
                                        @if($compteur->type_reseau == 'Électricité') ⚡
                                        @elseif($compteur->type_reseau == 'Eau Potable') 💧
                                        @elseif($compteur->type_reseau == 'Gaz') 🔥
                                        @else ⚙️ @endif
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800">{{ $compteur->point_comptage }}</p>
                                        <p class="text-xs font-mono text-slate-500">N°
                                            {{ $compteur->numero_compteur ?? 'Non renseigné' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="p-4 text-slate-600">
                                <p class="font-medium">{{ $compteur->local->batiment->nom_bat ?? 'Bâtiment inconnu' }}</p>
                                <p class="text-xs text-slate-500">{{ $compteur->local->nom_local ?? 'Local inconnu' }}</p>
                                @if($compteur->dessert_tout_le_batiment)
                                    <span
                                        class="inline-block mt-1 px-2 py-0.5 bg-blue-50 text-blue-700 text-[10px] font-bold rounded uppercase">Général
                                        Bâtiment</span>
                                @endif
                            </td>

                            <td class="p-4">
                                @if($compteur->contrat)
                                    <p class="font-semibold text-slate-700">N° {{ $compteur->contrat->numero_contrat }}</p>
                                @else
                                    <span class="text-xs text-amber-600 bg-amber-50 px-2 py-1 rounded border border-amber-100">Aucun
                                        contrat</span>
                                @endif
                            </td>

                            <td class="p-4">
                                @if($compteur->date_arret && \Carbon\Carbon::parse($compteur->date_arret)->isPast())
                                    <span class="px-2 py-1 text-xs font-bold rounded-md bg-red-100 text-red-700">Déposé /
                                        Arrêté</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-bold rounded-md bg-green-100 text-green-700">En
                                        service</span>
                                @endif
                            </td>

                            <td class="p-4 text-right whitespace-nowrap">
                                <a href="{{ route('compteurs.show', $compteur->id_compteur) }}"
                                    class="text-blue-600 hover:text-blue-900 font-semibold text-xs border border-blue-200 bg-blue-50 px-3 py-1 rounded-md transition">
                                    Consulter
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400 italic">
                                Aucun compteur n'est enregistré dans le système.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection