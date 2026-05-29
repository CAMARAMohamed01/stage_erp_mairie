@extends('layouts.app')
@section('header_title', 'Gestion des Crédits Budgétaires Votés')
@section('content')
<div class="max-w-5xl mx-auto space-y-6 pb-12">
    <div
        class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Enveloppes Budgétaires</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Crédits annuels affectés aux services pour
                l'ordonnancement.</p>
        </div>
        @can('check-permission', ['Finances & Achats', 'ecriture'])
        <a href="{{ route('enveloppes-budgetaires.create') }}"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-xs shadow-sm">➕ Allouer
            un Budget</a>
        @endcan
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-slate-50 border-b font-bold text-slate-500 text-xs uppercase tracking-wider">
                    <th class="p-4 w-32">Exercice</th>
                    <th class="p-4">Service Consommateur</th>
                    <th class="p-4 text-right">Crédit voté TTC</th>
                    <th class="p-4 text-center w-24">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                @foreach($enveloppes as $env)
                <tr class="hover:bg-slate-50/80 transition">
                    <td class="p-4 font-mono font-black text-slate-900">📅 {{ $env->annee_exercice }}</td>
                    <td class="p-4">🏛️ {{ $env->nom_service ?? 'Budget Général Commune' }}</td>
                    <td class="p-4 text-right font-bold text-emerald-600">
                        {{ number_format($env->montant_vote_ttc, 2, ',', ' ') }} €
                    </td>
                    <td class="p-4 text-center space-x-2 flex justify-center">
                        <a href="{{ route('enveloppes-budgetaires.edit', $env->id_budget) }}"
                            class="text-amber-600 hover:underline">✏️</a>
                        @can('check-permission', ['Finances & Achats', 'suppression'])
                        <form action="{{ route('enveloppes-budgetaires.destroy', $env->id_budget) }}" method="POST"
                            class="inline" onsubmit="return confirm('Retirer ce budget ?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500">🗑️</button>
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