@extends('layouts.app')

@section('title', 'Inventaire des Équipements')

@section('content')
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex justify-between items-center">
            <div>
                <h1 class="text-xl font-bold text-slate-800">⚙️ Inventaire des Équipements</h1>
                <p class="text-sm text-slate-500">Parc matériel technique communal</p>
            </div>
            <a href="{{ route('equipements.create') }}"
                class="flex items-center gap-2 bg-blue-600 px-4 py-2 rounded-lg text-sm font-bold text-white hover:bg-blue-700 shadow-md transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nouvel Équipement
            </a>
        </div>

        <div class="p-4 bg-white border-b border-slate-100">
            <form action="{{ route('equipements.index') }}" method="GET" class="flex flex-wrap gap-4 items-center">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par nom..."
                    class="border border-slate-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none w-64">

                <select name="famille"
                    class="border border-slate-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">Toutes familles</option>
                    @foreach($familles as $f)
                        <option value="{{ $f->id_famille }}" {{ request('famille') == $f->id_famille ? 'selected' : '' }}>
                            {{ $f->libelle_famille }}
                        </option>
                    @endforeach
                </select>

                <select name="etat"
                    class="border border-slate-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">Tous états</option>
                    <option value="Opérationnel" {{ request('etat') == 'Opérationnel' ? 'selected' : '' }}>Opérationnel
                    </option>
                    <option value="En panne" {{ request('etat') == 'En panne' ? 'selected' : '' }}>En panne</option>
                </select>

                <button type="submit"
                    class="bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-700 transition">Filtrer</button>
                <a href="{{ route('equipements.index') }}"
                    class="text-slate-500 hover:text-slate-800 text-sm font-medium">Réinitialiser</a>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold">Désignation</th>
                        <th class="px-6 py-4 font-semibold">Famille</th>
                        <th class="px-6 py-4 font-semibold">Marque</th>
                        <th class="px-6 py-4 font-semibold">État</th>
                        <th class="px-6 py-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($equipements as $e)
                        <tr class="hover:bg-slate-50 transition-colors text-sm">
                            <td class="px-6 py-4 font-bold text-slate-700">{{ $e->nom_equipement }}</td>
                            <td class="px-6 py-4">
                                <span
                                    class="bg-blue-50 text-blue-700 text-xs font-medium px-2.5 py-1 rounded-md border border-blue-100">
                                    {{ $e->famille->libelle_famille ?? 'Non classé' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $e->marque ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                @if($e->etat_fonctionnement == 'Opérationnel')
                                    <span
                                        class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded-full border border-green-200">Opérationnel</span>
                                @elseif($e->etat_fonctionnement == 'En panne')
                                    <span
                                        class="bg-red-100 text-red-800 text-xs font-semibold px-2 py-1 rounded-full border border-red-200">En
                                        panne</span>
                                @else
                                    <span
                                        class="bg-gray-100 text-gray-800 text-xs font-semibold px-2 py-1 rounded-full border border-gray-200">{{ $e->etat_fonctionnement ?? 'Inconnu' }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('equipements.show', $e->id_equipement) }}"
                                    class="text-blue-600 hover:text-blue-800 font-medium">
                                    Voir la fiche →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400 italic">
                                Aucun équipement trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-100">
            {{ $equipements->appends(request()->query())->links() }}
        </div>
    </div>
@endsection