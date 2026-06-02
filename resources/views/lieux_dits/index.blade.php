@extends('layouts.app')

@section('title', 'Gestion des Lieux-dits')

@section('content')
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex justify-between items-center">
            <div>
                <h1 class="text-xl font-bold text-slate-800">📍 Référentiel des Lieux-dits</h1>
                <p class="text-sm text-slate-500">Découpage territorial et toponymie de Dingy-Saint-Clair</p>
            </div>
            @can('check-permission', ['Voirie', 'ecriture'])
                <a href="{{ route('lieux-dits.create') }}"
                    class="flex items-center gap-2 bg-blue-600 px-4 py-2 rounded-lg text-sm font-bold text-white hover:bg-blue-700 shadow-md transition">
                    ➕ Nouveau lieu-dit
                </a>
            @endcan
        </div>

        <div class="p-6 border-b border-slate-100">
            <form action="{{ route('lieux-dits.index') }}" method="GET" class="flex gap-2">
                <div class="relative flex-grow max-w-sm">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un lieu-dit..."
                        class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 shadow-sm text-sm transition">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                <button type="submit"
                    class="px-4 py-2 bg-slate-800 text-white text-sm font-bold rounded-lg hover:bg-slate-700 transition">
                    Rechercher
                </button>
                @if(request('search'))
                    <a href="{{ route('lieux-dits.index') }}"
                        class="px-4 py-2 bg-slate-100 text-slate-600 text-sm font-bold rounded-lg hover:bg-slate-200 transition">
                        Réinitialiser
                    </a>
                @endif
            </form>
        </div>

        @if(session('success'))
            <div class="m-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm font-medium">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="m-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm font-medium">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold">ID</th>
                        <th class="px-6 py-4 font-semibold">Nom du Lieu-dit</th>
                        <th class="px-6 py-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($lieuxDits as $ld)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-slate-400">#{{ $ld->id_lieu_dit }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-900">{{ $ld->nom_lieu_dit }}</td>
                            <td class="px-6 py-4 text-right space-x-3">
                                @can('check-permission', ['Voirie', 'ecriture'])
                                    <a href="{{ route('lieux-dits.edit', $ld->id_lieu_dit) }}"
                                        class="text-blue-600 hover:text-blue-900 font-medium">
                                        📝 Modifier
                                    </a>
                                @endcan

                                @can('check-permission', ['Voirie', 'suppression'])
                                    <form action="{{ route('lieux-dits.destroy', $ld->id_lieu_dit) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Supprimer ce lieu-dit ? S\'il est lié à des parcelles ou adresses, la base refusera par sécurité.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-medium ml-2">
                                            🗑️ Supprimer
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-slate-400 italic">Aucun lieu-dit répertorié.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection