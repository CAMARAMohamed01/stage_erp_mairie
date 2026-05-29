@extends('layouts.app')
@section('title', 'Inscrire une Immobilisation')

@section('content')
<div class="max-w-2xl mx-auto pb-12">
    <form action="{{ route('immobilisations.store') }}" method="POST"
        class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        @csrf

        <div class="p-6 bg-slate-50 border-b border-slate-200">
            <h1 class="text-base font-bold text-slate-900">Inscrire un nouveau bien à l'inventaire</h1>
            <p class="text-xs text-slate-400 mt-0.5">L'identifiant d'inventaire doit être unique pour assurer le suivi
                analytique du patrimoine.</p>
        </div>

        <div class="p-6 space-y-4 text-xs font-medium text-slate-700">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-600 mb-1">N° d'inventaire unique *</label>
                    <input type="text" name="num_inventaire" required value="{{ old('num_inventaire') }}"
                        placeholder="Ex: IMMO-2026-042"
                        class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-slate-50 font-mono uppercase focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                </div>
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Date d'acquisition</label>
                    <input type="date" name="date_acquisition" value="{{ old('date_acquisition') }}"
                        class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-slate-50 focus:bg-white focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-600 mb-1">Désignation comptable du bien *</label>
                <input type="text" name="libelle_comptable" required value="{{ old('libelle_comptable') }}"
                    placeholder="Ex: Serveur central de la mairie - Baie réseau"
                    class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-slate-50 focus:bg-white focus:outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                <div class="sm:col-span-1">
                    <label class="block font-bold text-slate-600 mb-1">Valeur d'achat (€ TTC)</label>
                    <input type="number" step="0.01" min="0" name="valeur_achat" value="{{ old('valeur_achat') }}"
                        placeholder="0.00"
                        class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-slate-50 font-mono focus:bg-white focus:outline-none">
                </div>
                <div class="sm:col-span-2 flex items-center h-10 px-3 bg-slate-50 border border-slate-200 rounded-lg">
                    <input type="checkbox" name="est_amortissable" id="est_amortissable" value="1"
                        {{ old('est_amortissable') ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600 accent-blue-600 border-slate-300 rounded">
                    <label for="est_amortissable"
                        class="ml-2.5 text-xs font-bold text-slate-700 cursor-pointer select-none">Ce bien est
                        amortissable (Régime linéaire/dégressif)</label>
                </div>
            </div>

            <div class="border-t border-dashed pt-4">
                <label class="block font-bold text-slate-600 mb-1">Associer à une ligne de facture d'achat
                    (Optionnel)</label>
                <select name="id_ligne_achat"
                    class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white text-slate-700 font-semibold focus:outline-none">
                    <option value="">-- Aucune ligne de facture associée --</option>
                    @foreach($lignesDisponibles as $ligne)
                    <option value="{{ $ligne->id_ligne }}"
                        {{ old('id_ligne_achat') == $ligne->id_ligne ? 'selected' : '' }}>
                        [{{ \Carbon\Carbon::parse($ligne->date_comptable)->format('d/m/Y') }}]
                        {{ $ligne->designation_ligne }} ({{ number_format($ligne->montant_ttc, 2, ',', ' ') }} €)
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-2">
            <a href="{{ route('immobilisations.index') }}"
                class="px-4 py-2 border rounded-lg font-bold text-slate-600 bg-white hover:bg-slate-50 transition">Annuler</a>
            <button type="submit"
                class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-sm transition">💾
                Inscrire le Bien</button>
        </div>
    </form>
</div>
@endsection