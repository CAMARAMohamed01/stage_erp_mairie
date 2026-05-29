@extends('layouts.app')
@section('title', 'Plan de Comptes : Articles')

@section('content')
<div class="max-w-6xl mx-auto space-y-6 pb-12">
    <div
        class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Plan de Comptes (Articles)</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Détail analytique fin des comptes d'imputations de
                charges et produits de la régie.</p>
        </div>
        @can('check-permission', ['Finances & Achats', 'ecriture'])
        <a href="{{ route('articles-compta.create') }}"
            class="px-4 py-2 bg-blue-600 text-white font-bold rounded-lg text-xs shadow-sm transition hover:bg-blue-700">
            ➕ Nouvel Article
        </a>
        @endcan
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-slate-50 border-b font-bold text-slate-500 text-xs uppercase tracking-wider">
                    <th class="p-4 w-36">N° Article</th>
                    <th class="p-4 w-1/3">Libellé du compte</th>
                    <th class="p-4">Chapitres liés</th>
                    <th class="p-4">Enveloppes Budgétaires (Crédits ouverts)</th>
                    <th class="p-4 text-center w-24">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                @foreach($articles as $art)
                <tr class="hover:bg-slate-50/80 transition">
                    <td class="p-4 font-mono font-bold text-emerald-600">
                        🏷️ {{ $art->numero_article }}
                    </td>
                    <td class="p-4 text-slate-900 font-semibold">
                        {{ $art->libelle_article }}
                    </td>

                    <td class="p-4">
                        <div class="flex flex-wrap gap-1">
                            @forelse($art->chapitres as $chap)
                            <span
                                class="text-[10px] bg-blue-50 border border-blue-100 text-blue-700 px-2 py-0.5 rounded font-sans font-bold">
                                CH-{{ $chap->numero_chapitre }}
                            </span>
                            @empty
                            <span class="text-xs text-slate-400 italic font-normal">Non classifié</span>
                            @endforelse
                        </div>
                    </td>

                    <td class="p-4">
                        <div class="flex flex-col gap-1 max-w-xs">
                            @forelse($art->enveloppes as $env)
                            <div
                                class="text-[11px] bg-slate-50 border border-slate-200 p-1.5 rounded flex justify-between items-center gap-2">
                                <span class="font-bold text-slate-700">📅 {{ $env->annee_exercice }}</span>
                                <span
                                    class="text-slate-400 font-normal truncate max-w-[120px]">{{ $env->nom_service ?? 'Général' }}</span>
                                <span
                                    class="font-mono text-emerald-600 font-bold ml-auto">{{ number_format($env->montant_vote_ttc, 2, ',', ' ') }}
                                    €</span>
                            </div>
                            @empty
                            <span class="text-xs text-slate-400 italic font-normal">Aucun crédit alloué</span>
                            @endforelse
                        </div>
                    </td>

                    <td class="p-4 text-center space-x-3 flex justify-center items-center">
                        <a href="{{ route('articles-compta.edit', $art->id_article) }}"
                            class="text-amber-500 text-sm hover:underline">✏️</a>
                        @can('check-permission', ['Finances & Achats', 'suppression'])
                        <form action="{{ route('articles-compta.destroy', $art->id_article) }}" method="POST"
                            class="inline" onsubmit="return confirm('Supprimer cet article comptable ?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600 text-sm">🗑️</button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection