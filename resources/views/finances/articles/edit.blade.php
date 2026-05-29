@extends('layouts.app')
@section('title', 'Modifier l\'Article')

@section('content')
<div class="max-w-3xl mx-auto pb-12">
    <form action="{{ route('articles-compta.update', $article->id_article) }}" method="POST"
        class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        @csrf
        @method('PUT')

        <div class="p-6 bg-slate-50 border-b border-slate-200 flex items-center gap-4">
            <div
                class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl shadow-sm">
                ✏️
            </div>
            <div>
                <h1 class="text-base font-bold text-slate-900">Modifier l'article comptable :
                    {{ $article->numero_article }}</h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Ajustement du libellé du compte ou des
                    classifications budgétaires.</p>
            </div>
        </div>

        <div class="p-6 space-y-5 text-xs font-medium text-slate-700">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Numéro d'article *</label>
                    <input type="text" name="numero_article" required
                        value="{{ old('numero_article', $article->numero_article) }}" placeholder="Ex: 60612"
                        class="w-full border rounded-lg p-2.5 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition">
                </div>
                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-600 mb-1">Libellé du compte *</label>
                    <input type="text" name="libelle_article" required
                        value="{{ old('libelle_article', $article->libelle_article) }}"
                        placeholder="Ex: Énergie et électricité"
                        class="w-full border rounded-lg p-2.5 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-600 mb-2">Modifier le rattachement aux chapitres</label>
                    <div
                        class="grid grid-cols-1 gap-2 max-h-52 overflow-y-auto border p-3 rounded-lg bg-slate-50/50 custom-scrollbar">
                        @foreach($chapitres as $chap)
                        <label
                            class="flex items-center gap-2 p-1.5 hover:bg-white rounded cursor-pointer transition border border-transparent hover:border-slate-100">
                            <input type="checkbox" name="chapitres[]" value="{{ $chap->id_chapitre }}"
                                {{ $article->chapitres->contains($chap->id_chapitre) ? 'checked' : '' }}
                                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500/20">
                            <div>
                                <span class="font-mono font-bold text-blue-600">CH-{{ $chap->numero_chapitre }}</span>
                                <span
                                    class="text-[11px] text-slate-500 font-sans block mt-0.5">{{ Str::limit($chap->libelle_chapitre, 35) }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-600 mb-2">Ouvrir cet article aux enveloppes
                        budgétaires</label>
                    <div
                        class="grid grid-cols-1 gap-2 max-h-52 overflow-y-auto border p-3 rounded-lg bg-slate-50/50 custom-scrollbar">
                        @foreach($enveloppes as $env)
                        <label
                            class="flex items-center gap-2 p-1.5 hover:bg-white rounded cursor-pointer transition border border-transparent hover:border-slate-100">
                            <input type="checkbox" name="enveloppes[]" value="{{ $env->id_budget }}"
                                {{ $article->enveloppes->contains($env->id_budget) ? 'checked' : '' }}
                                class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500/20">
                            <div>
                                <span class="font-bold text-slate-900">📅 Exercice {{ $env->annee_exercice }}</span>
                                <span class="text-[11px] text-slate-500 font-sans block mt-0.5 truncate max-w-[200px]">
                                    🏛️ {{ $env->nom_service ?? 'Budget Général' }} — <span
                                        class="text-emerald-600 font-semibold">{{ number_format($env->montant_vote_ttc, 2, ',', ' ') }}
                                        €</span>
                                </span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-2">
            <a href="{{ route('articles-compta.index') }}"
                class="px-4 py-2 border rounded-lg font-bold text-slate-600 bg-white hover:bg-slate-50 transition">Annuler</a>
            <button type="submit"
                class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg shadow-sm transition">
                💾 Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
@endsection