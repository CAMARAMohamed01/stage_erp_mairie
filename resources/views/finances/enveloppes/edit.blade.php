@extends('layouts.app')
@section('title', 'Modifier l\'Enveloppe Budgétaire')

@section('content')
<div class="max-w-2xl mx-auto pb-12">
    <form action="{{ route('enveloppes-budgetaires.update', $enveloppe->id_budget) }}" method="POST"
        class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        @csrf
        @method('PUT')

        <div class="p-6 bg-slate-50 border-b border-slate-200 flex items-center gap-4">
            <div
                class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl shadow-sm">
                ⚙️</div>
            <div>
                <h1 class="text-base font-bold text-slate-900">Ajuster l'enveloppe budgétaire
                    #{{ $enveloppe->id_budget }}</h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Modification des crédits votés ou ajustement des
                    liaisons d'articles.</p>
            </div>
        </div>

        <div class="p-6 space-y-4 text-xs font-medium text-slate-700">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Année d'exercice budgétaire *</label>
                    <input type="number" min="2020" max="2050" name="annee_exercice" required
                        value="{{ old('annee_exercice', $enveloppe->annee_exercice) }}"
                        class="w-full border border-slate-300 rounded-lg p-2.5 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition">
                </div>
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Montant global alloué TTC (€) *</label>
                    <input type="number" step="0.01" name="montant_vote_ttc" required
                        value="{{ old('montant_vote_ttc', $enveloppe->montant_vote_ttc) }}" placeholder="0.00"
                        class="w-full border border-slate-300 rounded-lg p-2.5 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-600 mb-1">Service délégataire principal</label>
                <select name="id_service"
                    class="w-full border border-slate-300 rounded-lg p-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700">
                    <option value="">-- Budget Général Communal --</option>
                    @foreach($services as $s)
                    <option value="{{ $s->id_service }}"
                        {{ old('id_service', $enveloppe->id_service) == $s->id_service ? 'selected' : '' }}>🏛️
                        {{ $s->nom_service }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-600 mb-2">Modifier les articles comptables autorisés pour ce
                    budget</label>
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto border p-3 rounded-lg bg-slate-50/50 custom-scrollbar">
                    @foreach($articles as $art)
                    <label
                        class="flex items-center gap-2 p-1.5 hover:bg-white rounded cursor-pointer transition border border-transparent hover:border-slate-100">
                        <input type="checkbox" name="articles[]" value="{{ $art->id_article }}"
                            {{ $enveloppe->articles->contains($art->id_article) ? 'checked' : '' }}
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
                class="px-5 py-2 text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg font-bold transition text-xs">Annuler</a>
            <button type="submit"
                class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-xs transition shadow-sm">💾
                Enregistrer les rectifications</button>
        </div>
    </form>
</div>
@endsection