@extends('layouts.app')
@section('title', 'Registre des Immobilisations')

@section('content')
<div class="max-w-6xl mx-auto space-y-5 pb-12">

    <div
        class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Inventaire du Patrimoine & Immobilisations</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Registre comptable des biens de la commune. Consultez
                une fiche pour la modifier ou la supprimer.</p>
        </div>
        @can('check-permission', ['Finances & Achats', 'ecriture'])
        <a href="{{ route('immobilisations.create') }}"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-xs shadow-sm transition">➕
            Inscrire un Bien</a>
        @endcan
    </div>

    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <form action="{{ route('immobilisations.index') }}" method="GET"
            class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            <div class="sm:col-span-6 text-xs font-bold text-slate-600">
                <label for="search" class="block mb-1.5 pl-0.5">Recherche par N° d'inventaire ou désignation</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}"
                    placeholder="Ex: IMMO-2026-004..."
                    class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-slate-50 focus:bg-white text-slate-700 font-semibold focus:outline-none">
            </div>
            <div class="sm:col-span-4 text-xs font-bold text-slate-600">
                <label for="status" class="block mb-1.5 pl-0.5">Statut de l'actif</label>
                <select id="status" name="status"
                    class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white text-slate-700 font-semibold focus:outline-none">
                    <option value="">-- Tous statuts --</option>
                    <option value="actif" {{ request('status') === 'actif' ? 'selected' : '' }}>En cours (Actif)
                    </option>
                    <option value="amortissable" {{ request('status') === 'amortissable' ? 'selected' : '' }}>Biens
                        amortissables</option>
                    <option value="sorti" {{ request('status') === 'sorti' ? 'selected' : '' }}>Sortis de l'inventaire
                    </option>
                </select>
            </div>
            <div class="sm:col-span-2 flex gap-2">
                <button type="submit"
                    class="flex-1 px-3 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-lg text-xs transition">Filtrer</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-slate-50 border-b font-bold text-slate-500 text-xs uppercase tracking-wider">
                    <th class="p-4 w-40">N° Inventaire</th>
                    <th class="p-4">Désignation Comptable</th>
                    <th class="p-4 w-32 text-right">Valeur d'Achat</th>
                    <th class="p-4 w-36 text-center">Date Acquis.</th>
                    <th class="p-4 w-32 text-center">Régime</th>
                    <th class="p-4 text-center w-36">Consultation</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                @forelse($immobilisations as $immo)
                <tr
                    class="hover:bg-slate-50/80 transition {{ $immo->date_sortie ? 'bg-slate-50/40 text-slate-400' : '' }}">
                    <td class="p-4 font-mono font-bold text-blue-600"><span>📦 {{ $immo->num_inventaire }}</span></td>
                    <td class="p-4">
                        <p class="font-bold text-slate-900">{{ $immo->libelle_comptable }}</p>
                        @if($immo->date_sortie)<span
                            class="text-[10px] font-bold text-rose-600 bg-rose-50 border border-rose-100 px-1.5 py-0.5 rounded">Sorti</span>@endif
                    </td>
                    <td class="p-4 text-right font-mono font-bold text-slate-900">
                        {{ number_format($immo->valeur_achat, 2, ',', ' ') }} €
                    </td>
                    <td class="p-4 text-center font-mono text-xs">
                        {{ $immo->date_acquisition ? $immo->date_acquisition->format('d/m/Y') : '—' }}
                    </td>
                    <td class="p-4 text-center">
                        <span
                            class="px-2 py-0.5 text-[10px] font-bold rounded-md {{ $immo->est_amortissable ? 'bg-amber-50 text-amber-700 border border-amber-100' : 'bg-slate-100 text-slate-600 border' }}">
                            {{ $immo->est_amortissable ? 'Amortissable' : 'Immo N/A' }}
                        </span>
                    </td>
                    <td class="p-4 text-center">
                        <a href="{{ route('immobilisations.show', $immo->id_immo) }}"
                            class="px-3 py-1.5 bg-blue-100 hover:bg-blue-50 hover:text-blue-600 border rounded-lg text-xs font-bold transition flex items-center justify-center gap-1">
                            Consulter
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-sm text-slate-400 italic bg-slate-50/30">Aucun bien
                        inscrit au registre.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection