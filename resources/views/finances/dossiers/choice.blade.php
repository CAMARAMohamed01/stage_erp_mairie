@extends('layouts.app')

@section('title', 'Choisir le type de dossier')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-12">

    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Initialiser un nouveau dossier budgétaire</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Veuillez sélectionner la nature du flux comptable à
                enregistrer.</p>
        </div>
        <a href="{{ route('dossiers-financiers.index') }}"
            class="px-4 py-2 border rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
            Annuler
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <a href="{{ route('dossiers-financiers.create', ['type' => 'depense']) }}"
            class="bg-white border-2 border-slate-200 hover:border-blue-500 p-8 rounded-2xl shadow-sm hover:shadow-md transition group flex flex-col items-center text-center space-y-4">
            <div
                class="w-16 h-16 bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white rounded-2xl flex items-center justify-center text-3xl transition duration-300 shadow-sm">
                📉
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-900 group-hover:text-blue-600 transition">Dossier de Dépense
                    (Engagement)</h2>
                <p class="text-xs text-slate-400 mt-2 leading-relaxed font-medium"> Saisie de devis, émission de bons de
                    commande, liquidation de factures fournisseurs et mandatements vers la Trésorerie.</p>
            </div>
            <span class="text-xs font-bold text-blue-600 group-hover:underline pt-2 flex items-center gap-1">Ouvrir le
                formulaire →</span>
        </a>

        <a href="{{ route('dossiers-financiers.create', ['type' => 'recette']) }}"
            class="bg-white border-2 border-slate-200 hover:border-emerald-500 p-8 rounded-2xl shadow-sm hover:shadow-md transition group flex flex-col items-center text-center space-y-4">
            <div
                class="w-16 h-16 bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white rounded-2xl flex items-center justify-center text-3xl transition duration-300 shadow-sm">
                📈
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-900 group-hover:text-emerald-600 transition">Dossier de Recette
                    (Régie / Titre)</h2>
                <p class="text-xs text-slate-400 mt-2 leading-relaxed font-medium">Constatation de droits, émissions de
                    titres de recette, suivi des redevances d'occupation, subventions ou encaissements régie.</p>
            </div>
            <span class="text-xs font-bold text-emerald-600 group-hover:underline pt-2 flex items-center gap-1">Ouvrir
                le formulaire →</span>
        </a>

    </div>
</div>
@endsection