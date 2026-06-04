@extends('layouts.app')

@section('header_title', 'Modifier le Type d\'ERP')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-12">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">✏️ Modifier classification ERP</h1>
        <a href="{{ route('types-erp.show', $type_erp->id_type_erp) }}"
            class="text-sm font-bold text-slate-500 hover:text-slate-800 transition">← Annuler et retour</a>
    </div>

    <form action="{{ route('types-erp.update', $type_erp->id_type_erp) }}" method="POST"
        class="bg-white p-8 rounded-xl border border-slate-200 shadow-sm space-y-8">
        @csrf
        @method('PUT')

        {{-- 1. CARACTÉRISTIQUES DE L'ERP --}}
        <div class="space-y-6">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b pb-2">📋 Propriétés de
                l'établissement</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Catégorie ERP (1 à 5)</label>
                    <input type="number" name="categorie_erp"
                        value="{{ old('categorie_erp', $type_erp->categorie_erp) }}" min="1" max="5"
                        class="w-full border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-amber-500 bg-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Lettre du Type (ex: L, M,
                        R...)</label>
                    <input type="text" name="type_erp" value="{{ old('type_erp', $type_erp->type_erp) }}" maxlength="2"
                        class="w-full border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-amber-500 uppercase bg-white">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Public Cible</label>
                    <input type="text" name="public_cible" value="{{ old('public_cible', $type_erp->public_cible) }}"
                        class="w-full border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-amber-500 bg-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Réglementation applicable
                        *</label>
                    <input type="text" name="reglementation_applicable"
                        value="{{ old('reglementation_applicable', $type_erp->reglementation_applicable) }}" required
                        class="w-full border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-amber-500 bg-white">
                </div>
            </div>
        </div>

        {{-- 2. LISTE DES CONTROLES REGLEMENTAIRES ASSOCIES --}}
        <div class="space-y-4">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b pb-2">🛡️ Obligations de
                Contrôles Réglementaires</h3>
            <p class="text-xs text-slate-400 font-medium italic mt-1">Mettez à jour les contrôles requis ainsi que leurs
                dates d'exécution correspondantes.</p>

            <div class="border border-slate-200 rounded-xl divide-y divide-slate-100 overflow-hidden bg-slate-50/50">
                @foreach($controles as $controle)
                @php
                $estLie = array_key_exists($controle->id_controle, $controles_lies);
                $datePivot = $estLie ? $controles_lies[$controle->id_controle] : null;
                @endphp
                <div
                    class="p-4 bg-white flex flex-wrap justify-between items-center gap-4 hover:bg-slate-50/50 transition">
                    <label class="flex items-start cursor-pointer select-none max-w-xl">
                        <input type="checkbox" name="controles[]" value="{{ $controle->id_controle }}"
                            data-id="{{ $controle->id_controle }}" {{ $estLie ? 'checked' : '' }}
                            class="controle-checkbox w-5 h-5 text-amber-600 rounded border-slate-300 focus:ring-amber-500 mt-0.5">
                        <div class="ml-3">
                            <span class="block text-sm font-bold text-slate-800">{{ $controle->designation }}</span>
                            <span class="block text-xs text-slate-400 font-medium mt-0.5">Domaine :
                                {{ $controle->domaine_technique ?? 'Général' }} — Périodicité :
                                {{ $controle->frequence_mois ? $controle->frequence_mois.' mois' : 'Périodique' }}</span>
                        </div>
                    </label>

                    <div class="w-48">
                        <input type="date" name="date_controle[{{ $controle->id_controle }}]"
                            id="date_{{ $controle->id_controle }}" value="{{ $datePivot }}"
                            {{ $estLie ? '' : 'disabled' }}
                            class="w-full border rounded-lg p-2 text-xs focus:ring-2 focus:ring-blue-500 transition {{ $estLie ? 'bg-white text-slate-800 border-slate-300' : 'bg-slate-50 text-slate-400 border-slate-200' }}">
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ACTIONNEURS --}}
        <div class="pt-6 flex justify-end gap-3 border-t border-slate-200">
            <a href="{{ route('types-erp.show', $type_erp->id_type_erp) }}"
                class="px-5 py-2.5 border border-slate-300 text-slate-700 text-sm font-bold rounded-lg hover:bg-slate-50 transition">Annuler</a>
            <button type="submit"
                class="px-6 py-2.5 bg-blue-600 text-white font-black rounded-lg hover:bg-blue-700 transition shadow-md text-sm">
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>

<script>
document.querySelectorAll('.controle-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const id = this.getAttribute('data-id');
        const dateInput = document.getElementById('date_' + id);
        if (this.checked) {
            dateInput.disabled = false;
            dateInput.classList.remove('bg-slate-50', 'text-slate-400', 'border-slate-200');
            dateInput.classList.add('bg-white', 'text-slate-800', 'border-slate-300');
        } else {
            dateInput.disabled = true;
            dateInput.value = '';
            dateInput.classList.add('bg-slate-50', 'text-slate-400', 'border-slate-200');
            dateInput.classList.remove('bg-white', 'text-slate-800', 'border-slate-300');
        }
    });
});
</script>
@endsection