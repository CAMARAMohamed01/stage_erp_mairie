@extends('layouts.app')

@section('header_title', 'Gestion de la Voirie Communale')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-lg text-2xl">🛣️</div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-slate-400">Patrimoine Voirie</p>
                    <p class="text-xl font-bold text-slate-900">{{ $totalVoies }} voies recensées</p>
                </div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-lg text-2xl">📏</div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-slate-400">Longueur Totale</p>
                    <p class="text-xl font-bold text-slate-900">{{ number_format($longueurTotale / 1000, 2, ',', ' ') }} km
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex flex-wrap justify-between items-center bg-slate-50 gap-4">
                <h2 class="text-sm font-bold text-slate-800">Liste des Voies</h2>

                <div class="flex flex-wrap items-center gap-4">
                    <form action="{{ route('voies.index') }}" method="GET" class="flex gap-2">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Rechercher une voie..."
                            class="px-3 py-1 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <button type="submit"
                            class="px-3 py-1 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition">Rechercher</button>
                    </form>

                    <a href="{{ route('voies.create') }}"
                        class="px-4 py-1.5 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition flex items-center shadow-sm">
                        ➕ Ajouter une voie
                    </a>
                </div>
            </div>

            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 font-bold uppercase tracking-wider">N° Voie</th>
                        <th class="px-4 py-3 font-bold uppercase tracking-wider">Nom de la Voie</th>
                        <th class="px-4 py-3 font-bold uppercase tracking-wider">Catégorie</th>
                        <th class="px-4 py-3 font-bold uppercase tracking-wider text-center">Largeur</th>
                        <th class="px-4 py-3 font-bold uppercase tracking-wider text-center">PDIPR</th>
                        <th class="px-4 py-3 font-bold uppercase tracking-wider">Statut Juridique</th>
                        <th class="px-4 py-3 font-bold uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($voies as $voie)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 font-mono text-slate-700 font-bold">{{ $voie->numero_voie ?? '-' }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $voie->nom_voie }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $voie->categorie_voie ?? '-' }}</td>
                            <td class="px-4 py-3 text-center text-slate-700">
                                {{ $voie->largeur_moyenne_m ? $voie->largeur_moyenne_m . ' m' : '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($voie->est_pdipr)
                                    <span class="text-black-600 font-bold">OUI</span>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-medium">
                                    {{ $voie->statut_juridique ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('voies.show', $voie->id_voie) }}"
                                    class="text-blue-600 font-bold hover:underline">Consulter</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-400 italic">Aucune voie trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 border-t border-slate-100">
                {{ $voies->links() }}
            </div>
        </div>
    </div>
@endsection