@extends('layouts.app')
@section('header_title', 'Configuration Article')

@section('content')
<div class="max-w-3xl mx-auto pb-12">
    <form action="{{ route('articles-compta.store') }}" method="POST"
        class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        @csrf

        <div class="p-6 bg-slate-50 border-b border-slate-200">
            <h1 class="text-base font-bold text-slate-900">Ajouter un nouveau compte d'article</h1>
        </div>

        <div class="p-6 space-y-5 text-xs font-medium text-slate-700">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Numéro d'article *</label>
                    <input type="text" name="numero_article" required value="{{ old('numero_article') }}"
                        placeholder="Ex: 60612"
                        class="w-full border rounded-lg p-2.5 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                </div>
                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-600 mb-1">Libellé du compte *</label>
                    <input type="text" name="libelle_article" required value="{{ old('libelle_article') }}"
                        placeholder="Ex: Énergie et électricité"
                        class="w-full border rounded-lg p-2.5 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-600 mb-2">Rattacher cet article à un ou plusieurs
                        chapitres</label>
                    <div
                        class="grid grid-cols-1 gap-2 max-h-52 overflow-y-auto border p-3 rounded-lg bg-slate-50/50 custom-scrollbar">
                        @foreach($chapitres as $chap)
                        <label
                            class="flex items-center gap-2 p-1.5 hover:bg-white rounded cursor-pointer transition border border-transparent hover:border-slate-100">
                            <input type="checkbox" name="chapitres[]" value="{{ $chap->id_chapitre }}"
                                {{ is_array(old('chapitres')) && in_array($chap->id_chapitre, old('chapitres')) ? 'checked' : '' }}
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
                    <label class="block font-bold text-slate-600 mb-2">Rattacher directement à des enveloppes
                        budgétaires</label>
                    <div
                        class="grid grid-cols-1 gap-2 max-h-52 overflow-y-auto border p-3 rounded-lg bg-slate-50/50 custom-scrollbar">
                        @foreach($enveloppes as $env)
                        <label
                            class="flex items-center gap-2 p-1.5 hover:bg-white rounded cursor-pointer transition border border-transparent hover:border-slate-100">
                            <input type="checkbox" name="enveloppes[]" value="{{ $env->id_budget }}"
                                {{ is_array(old('enveloppes')) && in_array($env->id_budget, old('enveloppes')) ? 'checked' : '' }}
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
                💾 Sauvegarder l'article
            </button>
        </div>
    </form>
</div>
@endsection