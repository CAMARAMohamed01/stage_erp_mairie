@extends('layouts.app')

@section('header_title', 'Gestion des Supports d\'Accès')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        <div class="flex flex-wrap justify-between items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Registre des Clés & Badges</h1>
                <p class="text-sm text-slate-500 mt-1">Inventaire des moyens d'accès physiques, clés passe-partout et badges
                    RFID de la commune.</p>
            </div>
            <div>
                @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                    <a href="{{ route('supports-acces.create') }}"
                        class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold rounded-lg shadow-sm transition flex items-center gap-2">
                        🔑 Enregistrer un support
                    </a>
                @endcan
            </div>
        </div>

        <form action="{{ route('supports-acces.index') }}" method="GET" class="flex flex-wrap gap-2 mb-6">

            <div class="relative flex-grow max-w-sm">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par n° de série..."
                    class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-500 shadow-sm transition">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <select name="type_support"
                class="border border-slate-300 rounded-lg py-2 px-3 text-sm focus:ring-2 focus:ring-slate-500 shadow-sm">
                <option value="">Tous les types</option>
                <option value="Clé physique" {{ request('type_support') == 'Clé physique' ? 'selected' : '' }}>Clé physique
                </option>
                <option value="Badge RFID" {{ request('type_support') == 'Badge RFID' ? 'selected' : '' }}>Badge RFID
                </option>
                <option value="Vigik" {{ request('type_support') == 'Vigik' ? 'selected' : '' }}>Vigik</option>
                <option value="Télécommande" {{ request('type_support') == 'Télécommande' ? 'selected' : '' }}>Télécommande
                </option>
            </select>

            <select name="statut"
                class="border border-slate-300 rounded-lg py-2 px-3 text-sm focus:ring-2 focus:ring-slate-500 shadow-sm">
                <option value="">Tous les statuts</option>
                <option value="actif" {{ request('statut') == 'actif' ? 'selected' : '' }}>Actif</option>
                <option value="inactif" {{ request('statut') == 'inactif' ? 'selected' : '' }}>Inactif / Perdu</option>
            </select>

            <button type="submit"
                class="px-4 py-2 bg-slate-800 text-white text-sm font-bold rounded-lg hover:bg-slate-700 transition">
                Filtrer
            </button>

            @if(request()->anyFilled(['search', 'type_support', 'statut']))
                <a href="{{ route('supports-acces.index') }}"
                    class="px-4 py-2 bg-slate-100 text-slate-600 text-sm font-bold rounded-lg hover:bg-slate-200 transition">
                    Réinitialiser
                </a>
            @endif
        </form>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="p-4">N° de Série</th>
                        <th class="p-4">Type</th>
                        <th class="p-4">Statut</th>
                        <th class="p-4">Détenteur Actuel</th>
                        <th class="p-4">Observations</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($supports as $support)
                        @php $detenteur = $support->affectationActuelle(); @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="p-4 font-mono font-bold text-slate-700">
                                {{ $support->numero_serie }}
                            </td>
                            <td class="p-4">
                                <span
                                    class="px-2.5 py-1 text-xs font-semibold rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $support->type_support ?? 'Non spécifié' }}
                                </span>
                            </td>
                            <td class="p-4">
                                @if($support->est_actif)
                                    <span
                                        class="px-2 py-0.5 text-xs font-bold rounded bg-green-50 text-green-700 border border-green-100">Actif</span>
                                @else
                                    <span
                                        class="px-2 py-0.5 text-xs font-bold rounded bg-red-50 text-red-700 border border-red-100">Inactif
                                        / Révoqué</span>
                                @endif
                            </td>
                            <td class="p-4 font-medium">
                                @if($detenteur)
                                    <span class="text-slate-900">👤 {{ $detenteur->prenom_user }} {{ $detenteur->nom_user }}</span>
                                @else
                                    <span class="text-slate-400 italic text-xs">Au coffre / Disponible</span>
                                @endif
                            </td>
                            <td class="p-4 text-slate-500 max-w-xs truncate">
                                {{ $support->observations ?? '—' }}
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('supports-acces.show', $support->id_support) }}"
                                    class="inline-flex items-center px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-md transition shadow-sm border border-slate-200">
                                    Consulter
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400 italic bg-slate-50/50">
                                Aucun support d'accès ou clé enregistré dans le référentiel.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection