@extends('layouts.app')

@section('header_title', 'Registre des Actes & Décisions')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6 pb-12">

        <!-- Bandeau Haut & Action -->
        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Actes Administratifs</h1>
                <p class="text-xs text-slate-500 font-medium mt-1">Saisie, suivi légal et impact budgétaire des arrêtés
                    municipaux.</p>
            </div>

            @can('check-permission', ['Administration', 'ecriture'])
                <a href="{{ route('decisions-admin.create') }}"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition shadow-sm flex items-center gap-2">
                    ➕ Enregistrer un acte
                </a>
            @endcan
        </div>

        <!-- Barre de Recherche -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <form method="GET" action="{{ route('decisions-admin.index') }}" class="flex gap-2">
                <div class="relative flex-1">
                    <span
                        class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400 text-sm">🔍</span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Rechercher par numéro de décision, mot-clé ou intitulé..."
                        class="w-full pl-9 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:bg-white transition">
                </div>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                    Filtrer
                </button>
                @if(request('search'))
                    <a href="{{ route('decisions-admin.index') }}"
                        class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium rounded-lg transition flex items-center">
                        Effacer
                    </a>
                @endif
            </form>
        </div>

        <!-- Tableau du Registre -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr
                            class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <th class="p-4 w-32">Numéro</th>
                            <th class="p-4">Intitulé de l'acte</th>
                            <th class="p-4 w-40">Type</th>
                            <th class="p-4 w-36 text-center">Date</th>
                            <th class="p-4 w-32 text-center">Préfecture</th>
                            <th class="p-4 w-24 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($decisions as $dec)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-4 font-mono font-bold text-slate-900">{{ $dec->numero_decision }}</td>
                                <td class="p-4">
                                    <p class="text-slate-900 line-clamp-1">{{ $dec->intitule_decision }}</p>
                                    <p class="text-[11px] text-slate-400 font-normal mt-0.5">Rédigé par :
                                        {{ $dec->redacteur->nom_user ?? 'Non renseigné' }}
                                    </p>
                                </td>
                                <td class="p-4">
                                    <span class="px-2 py-0.5 text-xs bg-slate-100 border text-slate-600 rounded">
                                        {{ $dec->type_decision ?? 'Général' }}
                                    </span>
                                </td>
                                <td class="p-4 text-center text-slate-500 text-xs">
                                    {{ $dec->date_decision ? $dec->date_decision->format('d/m/Y') : '—' }}
                                </td>
                                <td class="p-4 text-center">
                                    <span
                                        class="px-2 py-0.5 text-[10px] font-bold rounded {{ $dec->teletransmission_prefecture ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-amber-50 text-amber-700 border border-amber-100' }}">
                                        {{ $dec->teletransmission_prefecture ? 'Transmis' : 'En attente' }}
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <a href="{{ route('decisions-admin.show', $dec->id_decision) }}"
                                        class="text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded transition">
                                        Instruire
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400 italic">
                                    Aucun acte ou arrêté trouvé dans le registre.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection