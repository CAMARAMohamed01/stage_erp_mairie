@extends('layouts.app')
@section('title', 'Modifier Immo #' . $immo->num_inventaire)

@section('content')
<div class="max-w-2xl mx-auto pb-12">
    <form action="{{ route('immobilisations.update', $immo->id_immo) }}" method="POST"
        class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        @csrf
        @method('PUT')

        <div class="p-6 bg-slate-50 border-b border-slate-200 flex items-center gap-4">
            <div
                class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-sm select-none">
                📦</div>
            <div>
                <h1 class="text-base font-bold text-slate-900">Rectifier l'immobilisation : {{ $immo->num_inventaire }}
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">Mise à jour des valeurs comptables ou déclaration de sortie du
                    patrimoine.</p>
            </div>
        </div>

        <div class="p-6 space-y-5 text-xs font-medium text-slate-700">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-600 mb-1">N° d'inventaire unique *</label>
                    <input type="text" name="num_inventaire" required
                        value="{{ old('num_inventaire', $immo->num_inventaire) }}"
                        class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-slate-50 font-mono uppercase focus:bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Date d'acquisition</label>
                    <input type="date" name="date_acquisition"
                        value="{{ old('date_acquisition', $immo->date_acquisition?->format('Y-m-d')) }}"
                        class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-slate-50 focus:bg-white focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-600 mb-1">Désignation comptable du bien *</label>
                <input type="text" name="libelle_comptable" required
                    value="{{ old('libelle_comptable', $immo->libelle_comptable) }}"
                    class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-slate-50 focus:bg-white focus:outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                <div class="sm:col-span-1">
                    <label class="block font-bold text-slate-600 mb-1">Valeur d'achat (€ TTC)</label>
                    <input type="number" step="0.01" min="0" name="valeur_achat"
                        value="{{ old('valeur_achat', $immo->valeur_achat) }}"
                        class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-slate-50 font-mono focus:bg-white focus:outline-none">
                </div>
                <div class="sm:col-span-2 flex items-center h-10 px-3 bg-slate-50 border border-slate-200 rounded-lg">
                    <input type="checkbox" name="est_amortissable" id="est_amortissable" value="1"
                        {{ old('est_amortissable', $immo->est_amortissable) ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600 accent-blue-600 rounded">
                    <label for="est_amortissable"
                        class="ml-2.5 text-xs font-bold text-slate-700 cursor-pointer select-none">Ce bien est
                        amortissable (Régime en cours)</label>
                </div>
            </div>

            <div class="border-t border-slate-200 pt-4 space-y-4">
                <div class="bg-rose-50/50 border border-rose-100 p-4 rounded-xl space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-rose-800 flex items-center gap-1.5">⚠️
                        Sortie d'inventaire & Réforme</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-600 mb-1">Date effective de sortie</label>
                            <input type="date" name="date_sortie"
                                value="{{ old('date_sortie', $immo->date_sortie?->format('Y-m-d')) }}"
                                class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white focus:outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 mb-1">Valeur de revente ou de cession
                                (€)</label>
                            <input type="number" step="0.01" min="0" name="valeur_revente"
                                value="{{ old('valeur_revente', $immo->valeur_revente) }}" placeholder="0.00"
                                class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white font-mono focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-600 mb-1">Motif de sortie de l'inventaire
                            communal</label>
                        <input type="text" name="motif_sortie" value="{{ old('motif_sortie', $immo->motif_sortie) }}"
                            placeholder="Ex: Cession à un tiers, Mise à la réforme, Destruction, Vente..."
                            class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white focus:outline-none">
                    </div>
                </div>
            </div>

        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-2">
            <a href="{{ route('immobilisations.show', $immo->id_immo) }}"
                class="px-4 py-2 border rounded-lg font-bold text-slate-600 bg-white hover:bg-slate-50 transition">Annuler</a>
            <button type="submit"
                class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-sm transition">💾
                Sauvegarder les changements</button>
        </div>
    </form>
</div>
@endsection