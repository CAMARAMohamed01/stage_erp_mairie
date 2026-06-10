@extends('layouts.app')

@section('title', 'Référentiel des Tronçons')

@section('content')
    <div class="max-w-7xl mx-auto pb-12">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-3">
                    <span class="text-3xl">🛣️</span> Référentiel des Tronçons
                </h1>
                <p class="text-sm text-slate-500 mt-1 ml-11">Gestion centralisée des segments de voirie et chemins
                    autonomes.</p>
            </div>
            <a href="{{ route('troncons.create') }}"
                class="px-4 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition shadow-sm flex items-center gap-2">
                <span>➕</span> Nouveau Tronçon
            </a>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm mb-6">
            <form action="{{ route('troncons.index') }}" method="GET" class="flex gap-4">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Rechercher par numéro ou nom..."
                    class="flex-1 rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500 text-sm">
                <button type="submit"
                    class="px-6 py-2 bg-slate-800 text-white font-bold rounded-lg hover:bg-slate-900 transition">
                    Rechercher
                </button>
                @if(request('search'))
                    <a href="{{ route('troncons.index') }}"
                        class="px-4 py-2 bg-slate-100 text-slate-600 font-bold rounded-lg hover:bg-slate-200 transition">
                        Effacer
                    </a>
                @endif
            </form>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Identification</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Voie de rattachement
                            </th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Caractéristiques</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">État</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($troncons as $troncon)
                            <tr class="hover:bg-slate-50/80 transition group">

                                <td class="p-4">
                                    <div class="font-bold text-slate-800">
                                        {{ $troncon->numero_troncon }}
                                    </div>
                                    <div class="text-xs text-slate-500 mt-0.5">
                                        {{ $troncon->nom_portion ?? 'Portion sans nom' }}
                                    </div>
                                </td>

                                <td class="p-4">
                                    @if($troncon->id_voie && $troncon->voie)
                                        <a href="{{ route('voies.show', $troncon->id_voie) }}"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 text-xs font-bold rounded-md hover:bg-indigo-100 transition">
                                            <span>🛣️</span>
                                            {{ $troncon->voie->numero_voie ? $troncon->voie->numero_voie . ' - ' : '' }}{{ $troncon->voie->nom_voie }}
                                        </a>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-slate-600 border border-slate-200 text-xs font-bold rounded-md">
                                            <span>🍃</span> Chemin autonome
                                        </span>
                                    @endif
                                </td>

                                <td class="p-4">
                                    <div class="flex gap-2">
                                        @if($troncon->type_revetement)
                                            <span
                                                class="px-2 py-0.5 bg-slate-100 text-slate-700 text-[10px] font-bold rounded border border-slate-200 uppercase">
                                                {{ $troncon->type_revetement }}
                                            </span>
                                        @endif
                                        <span
                                            class="px-2 py-0.5 bg-slate-100 text-slate-700 text-[10px] font-bold rounded border border-slate-200 uppercase">
                                            PK {{ $troncon->pk_debut ?? '?' }} → {{ $troncon->pk_fin ?? '?' }}
                                        </span>
                                    </div>
                                </td>

                                <td class="p-4">
                                    @php
                                        $etatBadge = match (strtolower($troncon->etat_physique)) {
                                            'bon', 'neuf' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'moyen', 'dégradé' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'mauvais', 'à refaire' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            default => 'bg-slate-50 text-slate-600 border-slate-200'
                                        };
                                    @endphp
                                    <span
                                        class="px-2.5 py-1 text-[10px] font-bold rounded-full border {{ $etatBadge }} uppercase">
                                        {{ $troncon->etat_physique ?? 'Non évalué' }}
                                    </span>
                                </td>

                                <td class="p-4 text-right whitespace-nowrap">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('troncons.show', $troncon->id_troncon) }}"
                                            class="px-3 py-1.5 bg-blue-100 border border-blue-300 text-slate-700 text-xs font-bold rounded hover:bg-blue-200 transition shadow-sm">
                                            Consulter
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-500">
                                    <div class="text-4xl mb-3">🍃</div>
                                    <p class="font-bold text-slate-700">Aucun tronçon trouvé.</p>
                                    <p class="text-sm">Commencez par ajouter un nouveau segment de voirie ou chemin.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection