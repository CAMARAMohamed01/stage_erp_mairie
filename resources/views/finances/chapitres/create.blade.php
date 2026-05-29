@extends('layouts.app')
@section('header_title', 'Configuration Chapitre')

@section('content')
<div class="max-w-2xl mx-auto pb-12">
    <form action="{{ isset($chapitre) ? route('chapitres.update', $chapitre->id_chapitre) : route('chapitres.store') }}"
        method="POST" class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        @csrf
        @if(isset($chapitre)) @method('PUT') @endif

        <div class="p-6 bg-slate-50 border-b border-slate-200">
            <h1 class="text-base font-bold text-slate-900">{{ isset($chapitre) ? 'Modifier' : 'Ajouter' }} un chapitre
                comptable</h1>
        </div>

        <div class="p-6 space-y-5 text-xs font-medium text-slate-700">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Numéro du chapitre *</label>
                    <input type="text" name="numero_chapitre" required value="{{ old('numero_chapitre') }}"
                        placeholder="Ex: 011"
                        class="w-full border rounded-lg p-2.5 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                </div>
                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-600 mb-1">Désignation officielle *</label>
                    <input type="text" name="libelle_chapitre" required value="{{ old('libelle_chapitre') }}"
                        placeholder="Ex: Charges à caractère général"
                        class="w-full border rounded-lg p-2.5 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Section Budgétaire *</label>
                    <select name="section_budgetaire" required
                        class="w-full border rounded-lg p-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700">
                        <option value="Fonctionnement"
                            {{ old('section_budgetaire') == 'Fonctionnement' ? 'selected' : '' }}>Fonctionnement
                            (Charges courantes)</option>
                        <option value="Investissement"
                            {{ old('section_budgetaire') == 'Investissement' ? 'selected' : '' }}>Investissement
                            (Patrimoine / Travaux)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Sens Financier *</label>
                    <select name="sens_financier" required
                        class="w-full border rounded-lg p-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700">
                        <option value="Dépense" {{ old('sens_financier') == 'Dépense' ? 'selected' : '' }}>Dépense
                        </option>
                        <option value="Recette" {{ old('sens_financier') == 'Recette' ? 'selected' : '' }}>Recette
                        </option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-600 mb-2">Sélectionner et rattacher les articles comptables
                    (Optionnel)</label>
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-52 overflow-y-auto border p-3 rounded-lg bg-slate-50/50 custom-scrollbar">
                    @foreach($articles as $art)
                    <label
                        class="flex items-center gap-2 p-1.5 hover:bg-white rounded cursor-pointer transition border border-transparent hover:border-slate-100">
                        <input type="checkbox" name="articles[]" value="{{ $art->id_article }}"
                            {{ (isset($chapitre) && $chapitre->articles->contains($art->id_article)) ? 'checked' : '' }}
                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500/20">
                        <div>
                            <span class="font-mono font-bold text-slate-900">[{{ $art->numero_article }}]</span>
                            <span
                                class="text-[11px] text-slate-500 font-sans block mt-0.5">{{ Str::limit($art->libelle_article, 40) }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-2">
            <a href="{{ route('chapitres.index') }}"
                class="px-4 py-2 border rounded-lg font-bold text-slate-600 bg-white hover:bg-slate-50 transition">Annuler</a>
            <button type="submit"
                class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-sm transition">💾
                Sauvegarder le chapitre</button>
        </div>
    </form>
</div>
@endsection