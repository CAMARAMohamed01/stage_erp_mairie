@extends('layouts.app')
@section('header_title', 'Configuration Enveloppe')

@section('content')
<div class="max-w-2xl mx-auto pb-12">
    <form action="{{ route('enveloppes-budgetaires.store') }}" method="POST"
        class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        @csrf

        <div class="p-6 bg-slate-50 border-b border-slate-200">
            <h1 class="text-base font-bold text-slate-900">Voter une nouvelle enveloppe budgétaire</h1>
        </div>

        <div class="p-6 space-y-5 text-xs font-medium text-slate-700">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Année d'exercice budgétaire *</label>
                    <input type="number" min="2020" max="2050" name="annee_exercice" required
                        value="{{ old('annee_exercice', '2026') }}"
                        class="w-full border rounded-lg p-2.5 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                </div>
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Montant global alloué TTC (€) *</label>
                    <input type="number" step="0.01" name="montant_vote_ttc" required
                        value="{{ old('montant_vote_ttc') }}" placeholder="0.00"
                        class="w-full border rounded-lg p-2.5 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                </div>
            </div>
            <div>
                <label class="block font-bold text-slate-600 mb-1">Service délégataire principal</label>
                <select name="id_service"
                    class="w-full border rounded-lg p-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700">
                    <option value="">-- Budget Général Communal --</option>
                    @foreach($services as $s)
                    <option value="{{ $s->id_service }}" {{ old('id_service') == $s->id_service ? 'selected' : '' }}>
                        {{ $s->nom_service }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-600 mb-2">Rattacher les articles comptables ouverts pour ce
                    budget</label>
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto border p-3 rounded-lg bg-slate-50/50 custom-scrollbar">
                    @foreach($articles as $art)
                    <label
                        class="flex items-center gap-2 p-1.5 hover:bg-white rounded cursor-pointer transition border border-transparent hover:border-slate-100">
                        <input type="checkbox" name="articles[]" value="{{ $art->id_article }}"
                            {{ is_array(old('articles')) && in_array($art->id_article, old('articles')) ? 'checked' : '' }}
                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500/20">
                        <div>
                            <span class="font-mono font-bold text-emerald-600">🏷️ {{ $art->numero_article }}</span>
                            <span
                                class="text-[11px] text-slate-500 font-sans block mt-0.5">{{ Str::limit($art->libelle_article, 40) }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-2">
            <a href="{{ route('enveloppes-budgetaires.index') }}"
                class="px-4 py-2 border rounded-lg font-bold text-slate-600 bg-white hover:bg-slate-50">Annuler</a>
            <button type="submit"
                class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-sm transition">💾
                Notifier le crédit budget</button>
        </div>
    </form>
</div>
@endsection