@extends('layouts.app')

@section('title', 'Registre des Contrats')

@section('content')
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex justify-between items-center">
            <div>
                <h1 class="text-xl font-bold text-slate-800">📄 Registre des Contrats & Engagements</h1>
                <p class="text-sm text-slate-500">Suivi des prestataires, assurances et maintenances</p>
            </div>
            <a href="{{ route('contrats.create') }}"
                class="flex items-center gap-2 bg-blue-600 px-4 py-2 rounded-lg text-sm font-bold text-white hover:bg-blue-700 shadow-md transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nouveau Contrat
            </a>
        </div>

        <div class="p-4 bg-white border-b border-slate-100">
            <form action="{{ route('contrats.index') }}" method="GET" class="flex flex-wrap gap-4 items-center">
                <select name="type_contrat"
                    class="border border-slate-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">Tous les types de contrat</option>
                    @foreach($typesContrat as $type)
                        @if($type)
                            <option value="{{ $type }}" {{ request('type_contrat') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endif
                    @endforeach
                </select>
                <button type="submit"
                    class="bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-700 transition">Filtrer</button>
                <a href="{{ route('contrats.index') }}"
                    class="text-slate-500 hover:text-slate-800 text-sm font-medium">Réinitialiser</a>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold">Réf / Type</th>
                        <th class="px-6 py-4 font-semibold">Prestataire (Tiers)</th>
                        <th class="px-6 py-4 font-semibold">Période de validité</th>
                        <th class="px-6 py-4 font-semibold">Coût Annuel</th>
                        <th class="px-6 py-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($contrats as $c)
                        <tr class="hover:bg-slate-50 transition-colors text-sm">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">{{ $c->numero_contrat ?? 'Non numéroté' }}</div>
                                <div class="text-xs text-slate-500 mt-1">{{ $c->type_contrat }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-700">
                                    {{ $c->tiers->nom_affiche ?? 'Tiers #' . $c->id_tiers }}
                                </div>
                                <span
                                    class="text-[10px] bg-slate-100 text-slate-500 border border-slate-200 px-2 py-0.5 rounded block w-max mt-1">
                                    {{ $c->tiers->type_tiers ?? 'Inconnu' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $isExpired = $c->date_fin_contrat ? now()->greaterThan($c->date_fin_contrat) : false;
                                @endphp
                                <div class="text-slate-600 text-xs space-y-1">
                                    <div>Du : <span
                                            class="font-medium">{{ $c->date_debut_contrat ? $c->date_debut_contrat->format('d/m/Y') : '-' }}</span>
                                    </div>
                                    <div>Au : <span
                                            class="{{ $isExpired ? 'text-red-600 font-bold' : 'font-medium' }}">{{ $c->date_fin_contrat ? $c->date_fin_contrat->format('d/m/Y') : 'Indéterminée' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-800">
                                {{ $c->prix_annuel ? number_format($c->prix_annuel, 2, ',', ' ') . ' €' : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('contrats.show', $c->id_contrat) }}"
                                    class="text-blue-600 hover:text-blue-800 font-medium">
                                    Consulter →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400 italic">
                                Aucun contrat enregistré dans la base de données.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-100">
            {{ $contrats->appends(request()->query())->links() }}
        </div>
    </div>
@endsection