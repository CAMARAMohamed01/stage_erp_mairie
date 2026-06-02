@extends('layouts.app')

@section('title', 'Registre des actions')

@section('content')
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex justify-between items-center">
            <div>
                <h1 class="text-xl font-bold text-slate-800">📋 Registre des actions</h1>
                <p class="text-sm text-slate-500">Liste exhaustive des doléances citoyennes</p>
            </div>
            <div class="flex gap-3">
                {{-- 🔒 Sécurisé : Seuls ceux qui ont le droit de lecture peuvent exporter --}}
                @can('check-permission', ['actions', 'lecture'])
                    <a href="{{ route('actions.excel') }}"
                        class="flex items-center gap-2 bg-white border border-slate-300 px-4 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Exporter
                    </a>
                @endcan

                {{-- 🔒 Sécurisé : Seuls ceux qui ont le droit d'écriture peuvent créer une action --}}
                @can('check-permission', ['actions', 'ecriture'])
                    <a href="{{ route('actions.create') }}"
                        class="flex items-center gap-2 bg-blue-600 px-4 py-2 rounded-lg text-sm font-bold text-white hover:bg-blue-700 shadow-md transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Nouveau action
                    </a>
                @endcan
            </div>
        </div>

        <div class="p-6 border-b border-slate-100">
            <form action="{{ route('actions.index') }}" method="GET" class="flex flex-wrap gap-2">
                <div class="relative flex-grow max-w-sm">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Rechercher par émetteur ou description..."
                        class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 shadow-sm transition">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <select name="statut"
                    class="border border-slate-300 rounded-lg py-2 px-3 text-sm focus:ring-2 focus:ring-blue-500 shadow-sm">
                    <option value="">Tous les statuts</option>
                    <option value="Ouvert" {{ request('statut') == 'Ouvert' ? 'selected' : '' }}>Ouvert</option>
                    <option value="En cours" {{ request('statut') == 'En cours' ? 'selected' : '' }}>En cours</option>
                    <option value="Transmis" {{ request('statut') == 'Transmis' ? 'selected' : '' }}>Transmis</option>
                    <option value="Abandonné" {{ request('statut') == 'Abandonné' ? 'selected' : '' }}>Abandonné</option>
                    <option value="Terminé" {{ request('statut') == 'Terminé' ? 'selected' : '' }}>Terminé</option>
                </select>

                <select name="categorie_id"
                    class="border border-slate-300 rounded-lg py-2 px-3 text-sm focus:ring-2 focus:ring-blue-500 shadow-sm">
                    <option value="">Toutes catégories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id_cat }}" {{ request('categorie_id') == $cat->id_cat ? 'selected' : '' }}>
                            {{ $cat->libelle }}
                        </option>
                    @endforeach
                </select>

                <button type="submit"
                    class="px-4 py-2 bg-slate-800 text-white text-sm font-bold rounded-lg hover:bg-slate-700 transition">
                    Filtrer
                </button>

                @if(request()->anyFilled(['search', 'statut', 'categorie_id']))
                    <a href="{{ route('actions.index') }}"
                        class="px-4 py-2 bg-slate-100 text-slate-600 text-sm font-bold rounded-lg hover:bg-slate-200 transition">
                        Réinitialiser
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold">Réf</th>
                        <th class="px-6 py-4 font-semibold">Date</th>
                        <th class="px-6 py-4 font-semibold">Émetteur</th>
                        <th class="px-6 py-4 font-semibold">Description</th>
                        <th class="px-6 py-4 font-semibold">Statut</th>
                        <th class="px-6 py-4 font-semibold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($actions as $sig)
                        <tr class="hover:bg-slate-50 transition-colors text-sm">
                            <td class="px-6 py-4 font-bold text-slate-700">#{{ $sig->id_action }}</td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ \Carbon\Carbon::parse($sig->date_creation)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-900">{{ $sig->emetteur_nom }}</div>
                                <div class="text-slate-500 text-xs">{{ $sig->mode_reception }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ Str::limit($sig->description, 60) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                                    {{ $sig->statut_action === 'Ouvert' ? 'bg-red-50 text-red-700 border border-red-100' : '' }}
                                                    {{ $sig->statut_action === 'En cours' ? 'bg-amber-50 text-amber-700 border border-amber-100' : '' }}
                                                    {{ $sig->statut_action === 'Terminé' ? 'bg-green-50 text-green-700 border border-green-100' : '' }}
                                                ">
                                    {{ $sig->statut_action }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('actions.show', $sig->id_action) }}"
                                    class="text-blue-600 hover:text-blue-800 font-medium">
                                    Consulter →
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection