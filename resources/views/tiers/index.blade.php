@extends('layouts.app')

@section('title', 'Annuaire des Citoyens')

@section('content')
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">

        <div class="bg-slate-900 px-6 py-5 flex justify-between items-center">
            <div>
                <h1 class="text-xl font-bold text-white flex items-center">
                    <svg class="w-5 h-5 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    Annuaire des Citoyens
                </h1>
                <p class="text-slate-400 text-sm mt-1">Base de données des contacts (Tiers Physiques)</p>
            </div>
            <div>
                <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-bold">
                    {{ $citoyens->count() }} contact(s)
                </span>
                <a href="{{ route('tiers.create') }}"
                    class="ml-4 bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded text-sm font-bold shadow-sm transition">
                    + Nouveau Citoyen
                </a>
            </div>
        </div>

        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
            <form action="{{ route('tiers.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Rechercher par nom, prénom, email ou téléphone..."
                    class="text-sm border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2 bg-white w-full md:w-96 shadow-sm">

                <button type="submit"
                    class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-medium hover:bg-slate-700 transition">Rechercher</button>

                @if(request('search'))
                    <a href="{{ route('tiers.index') }}"
                        class="text-xs text-red-600 hover:underline font-medium">Réinitialiser</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold">ID</th>
                        <th class="px-6 py-4 font-semibold">Nom & Prénom</th>
                        <th class="px-6 py-4 font-semibold">Contact</th>
                        <th class="px-6 py-4 font-semibold text-center">Historique actions</th>
                        <th class="px-6 py-4 font-semibold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($citoyens as $citoyen)
                        <tr class="hover:bg-slate-50 transition-colors text-sm">
                            <td class="px-6 py-4 font-bold text-slate-400">#{{ $citoyen->id_tiers }}</td>

                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $citoyen->physique->nom_tiers ?? 'Inconnu' }}</div>
                                <div class="text-slate-500">{{ $citoyen->physique->prenom_tiers ?? '' }}</div>
                            </td>

                            <td class="px-6 py-4">
                                @if($citoyen->tel_tiers)
                                    <div class="flex items-center text-slate-700 mb-1">
                                        <svg class="w-3.5 h-3.5 mr-2 text-slate-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                            </path>
                                        </svg>
                                        {{ $citoyen->tel_tiers }}
                                    </div>
                                @endif
                                @if($citoyen->email_tiers)
                                    <div class="flex items-center text-slate-600 text-xs">
                                        <svg class="w-3.5 h-3.5 mr-2 text-slate-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        {{ $citoyen->email_tiers }}
                                    </div>
                                @endif
                                @if(!$citoyen->tel_tiers && !$citoyen->email_tiers)
                                    <span class="text-xs text-slate-400 italic">Aucun contact</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if($citoyen->actions_count > 0)
                                    <span
                                        class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full border border-blue-200">
                                        {{ $citoyen->actions_count }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs italic">Aucun</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('tiers.show', $citoyen->id_tiers) }}"
                                    class="text-blue-600 hover:text-blue-800 font-medium bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded transition">
                                    Voir le dossier
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500 italic">
                                Aucun citoyen ne correspond à votre recherche.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection