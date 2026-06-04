@extends('layouts.app')

@section('header_title', 'Ajouter un Type d\'ERP')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6 pb-12">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">➕ Nouvelle classification ERP</h1>
            <a href="{{ route('types-erp.index') }}"
                class="text-sm font-bold text-blue-600 hover:text-blue-800 transition">← Retour à la liste</a>
        </div>

        <form action="{{ route('types-erp.store') }}" method="POST"
            class="bg-white p-8 rounded-xl border border-slate-200 shadow-sm space-y-8">
            @csrf

            {{-- 1. CARACTÉRISTIQUES DE L'ERP --}}
            <div class="space-y-6">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b pb-2">📋 Propriétés de
                    l'établissement</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Catégorie ERP (1 à 5)</label>
                        <input type="number" name="categorie_erp" value="{{ old('categorie_erp') }}" min="1" max="5"
                            class="w-full border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Lettre du Type (ex: L, M,
                            R...)</label>
                        <input type="text" name="type_erp" value="{{ old('type_erp') }}" maxlength="2"
                            class="w-full border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 uppercase bg-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Public Cible</label>
                        <input type="text" name="public_cible" value="{{ old('public_cible') }}"
                            placeholder="Ex: Établissement scolaire, Salle des fêtes..."
                            class="w-full border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Réglementation applicable
                            *</label>
                        <input type="text" name="reglementation_applicable" value="{{ old('reglementation_applicable') }}"
                            required placeholder="Ex: Arrêté du 25 juin 1980..."
                            class="w-full border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 bg-white">
                    </div>
                </div>
            </div>

            {{-- 2. LISTE DES CONTROLES REGLEMENTAIRES ASSOCIES --}}
            <div class="space-y-4">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b pb-2">🛡️ Obligations de
                    Contrôles Réglementaires</h3>
                <p class="text-xs text-slate-400 font-medium italic mt-1">Cochez les contrôles applicables à ce type
                    d'établissement et spécifiez la date de dernière réalisation.</p>

                <div class="border border-slate-200 rounded-xl divide-y divide-slate-100 overflow-hidden bg-slate-50/50">
                    @forelse($controles as $controle)
                        <div
                            class="p-4 bg-white flex flex-wrap justify-between items-center gap-4 hover:bg-slate-50/50 transition">
                            <label class="flex items-start cursor-pointer select-none max-w-xl">
                                <input type="checkbox" name="controles[]" value="{{ $controle->id_controle }}"
                                    data-id="{{ $controle->id_controle }}"
                                    class="controle-checkbox w-5 h-5 text-blue-600 rounded border-slate-300 focus:ring-blue-500 mt-0.5">
                                <div class="ml-3">
                                    <span class="block text-sm font-bold text-slate-800">{{ $controle->designation }}</span>
                                    <span class="block text-xs text-slate-400 font-medium mt-0.5">Domaine :
                                        {{ $controle->domaine_technique ?? 'Général' }} — Périodicité :
                                        {{ $controle->frequence_mois ? $controle->frequence_mois . ' mois' : 'Périodique' }}</span>
                                </div>
                            </label>

                            <div class="w-48">
                                <input type="date" name="date_controle[{{ $controle->id_controle }}]"
                                    id="date_{{ $controle->id_controle }}" disabled
                                    class="w-full border border-slate-200 bg-slate-50 text-slate-400 rounded-lg p-2 text-xs focus:ring-2 focus:ring-blue-500 transition">
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-sm text-slate-400 italic">
                            Aucun contrôle réglementaire référencé en base de données.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ACTIONNEURS --}}
            <div class="pt-6 flex justify-end gap-3 border-t border-slate-200">
                <a href="{{ route('types-erp.index') }}"
                    class="px-5 py-2.5 border border-slate-300 text-slate-700 text-sm font-bold rounded-lg hover:bg-slate-50 transition">Annuler</a>
                <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 text-white font-black rounded-lg hover:bg-blue-700 transition shadow-md text-sm">
                    Créer la catégorie ERP
                </button>
            </div>
        </form>
    </div>

    <script>
        document.querySelectorAll('.controle-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const id = this.getAttribute('data-id');
                const dateInput = document.getElementById('date_' + id);
                if (this.checked) {
                    dateInput.disabled = false;
                    dateInput.classList.remove('bg-slate-100', 'text-slate-400', 'border-slate-200');
                    dateInput.classList.add('bg-white', 'text-slate-800', 'border-slate-300');
                } else {
                    dateInput.disabled = true;
                    dateInput.value = '';
                    dateInput.classList.add('bg-slate-100', 'text-slate-400', 'border-slate-200');
                    dateInput.classList.remove('bg-white', 'text-slate-800', 'border-slate-300');
                }
            });
        });
    </script>
@endsection