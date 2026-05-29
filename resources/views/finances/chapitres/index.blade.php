@extends('layouts.app')
@section('title', 'Nomenclature des Chapitres')

@section('content')
<div class="max-w-6xl mx-auto space-y-6 pb-12">

    <div
        class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Nomenclature des Chapitres (M57)</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Regroupement réglementaire des crédits par nature
                globale pour le vote du budget communal.</p>
        </div>
        @can('check-permission', ['Finances & Achats', 'ecriture'])
        <a href="{{ route('chapitres.create') }}"
            class="px-4 py-2 bg-blue-600 text-white font-bold rounded-lg text-xs shadow-sm transition hover:bg-blue-700 flex items-center gap-1.5">
            ➕ Nouveau Chapitre
        </a>
        @endcan
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b font-bold text-slate-500 text-xs uppercase tracking-wider">
                        <th class="p-4 w-32">N° Chapitre</th>
                        <th class="p-4">Désignation</th>
                        <th class="p-4">Section Budgétaire</th>
                        <th class="p-4">Sens Financier</th>
                        <th class="p-4">Articles associés</th>
                        <th class="p-4 text-center w-24">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @foreach($chapitres as $chap)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="p-4 font-mono font-black text-blue-600 whitespace-nowrap">
                            📖 {{ $chap->numero_chapitre }}
                        </td>

                        <td class="p-4 text-slate-900 font-bold">
                            {{ $chap->libelle_chapitre ?? '—' }}
                        </td>

                        <td class="p-4 whitespace-nowrap">
                            @php
                            $sectionColor = $chap->section_budgetaire === 'Investissement'
                            ? 'bg-purple-50 text-purple-700 border-purple-100'
                            : 'bg-indigo-50 text-indigo-700 border-indigo-100';
                            @endphp
                            <span class="px-2.5 py-0.5 text-xs font-bold rounded-full border {{ $sectionColor }}">
                                {{ $chap->section_budgetaire }}
                            </span>
                        </td>

                        <td class="p-4 whitespace-nowrap">
                            @php
                            $sensColor = $chap->sens_financier === 'Recette'
                            ? 'bg-emerald-50 text-emerald-700 border-emerald-100'
                            : 'bg-rose-50 text-rose-700 border-rose-100';
                            @endphp
                            <span class="px-2.5 py-0.5 text-xs font-bold rounded-full border {{ $sensColor }}">
                                {{ $chap->sens_financier ?? 'Non défini' }}
                            </span>
                        </td>

                        <td class="p-4">
                            <div class="flex flex-wrap gap-1 max-w-xs">
                                @forelse($chap->articles as $art)
                                <span
                                    class="text-[10px] bg-slate-100 border border-slate-200 text-slate-600 px-2 py-0.5 rounded font-mono"
                                    title="{{ $art->libelle_article }}">
                                    {{ $art->numero_article }}
                                </span>
                                @empty
                                <span class="text-xs text-slate-400 italic font-normal">Aucun article lié</span>
                                @endforelse
                            </div>
                        </td>

                        <td class="p-4 text-center space-x-3 flex justify-center items-center whitespace-nowrap">
                            <a href="{{ route('chapitres.edit', $chap->id_chapitre) }}"
                                class="text-amber-500 text-sm hover:underline" title="Modifier la configuration">✏️</a>

                            @can('check-permission', ['Finances & Achats', 'suppression'])
                            <form action="{{ route('chapitres.destroy', $chap->id_chapitre) }}" method="POST"
                                class="inline"
                                onsubmit="return confirm('⚠️ Supprimer définitivement ce chapitre ? Les articles rattachés seront simplement détachés.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 text-sm transition"
                                    title="Supprimer">
                                    🗑️
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection