@extends('layouts.app')

@section('header_title', 'Créer un contrôle réglementaire')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-12">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">📋 Nouveau contrôle réglementaire</h1>
            <p class="text-sm text-slate-500">Ajouter une nouvelle obligation de vérification technique pour le
                patrimoine communal.</p>
        </div>
        <a href="{{ route('controles.index') }}"
            class="text-sm font-semibold text-slate-500 hover:text-slate-800 transition">← Catalogue</a>
    </div>

    <form action="{{ route('controles.store') }}" method="POST">
        @csrf

        {{-- BLOC 1 : FORMULAIRE PRINCIPAL --}}
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-6">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Désignation du contrôle *</label>
                <input type="text" name="designation" value="{{ old('designation') }}" required
                    placeholder="Ex: Vérification périodique des installations électriques"
                    class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 @error('designation') border-red-500 @enderror">
                @error('designation') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Domaine technique</label>
                    <input type="text" name="domaine_technique" value="{{ old('domaine_technique') }}"
                        placeholder="Ex: Électricité, Incendie, Gaz, Ascenseur..."
                        class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Type de contrôle</label>
                    <input type="text" name="type_controle" value="{{ old('type_controle') }}"
                        placeholder="Ex: Périodique, Initial, Quinquennal..."
                        class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t pt-4 border-slate-100">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Fréquence réglementaire (en
                        mois)</label>
                    <input type="number" name="frequence_mois" value="{{ old('frequence_mois', 12) }}" min="1"
                        class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-center h-full pt-6">
                    <label
                        class="flex items-center gap-3 cursor-pointer p-3 bg-slate-50 border border-slate-200 rounded-lg w-full transition hover:bg-slate-100">
                        <input type="checkbox" name="est_legalement_obligatoire" value="1"
                            {{ old('est_legalement_obligatoire') ? 'checked' : '' }}
                            class="w-5 h-5 text-red-600 rounded focus:ring-red-500 border-gray-300">
                        <div>
                            <span class="block text-sm font-bold text-slate-800">Caractère Obligatoire</span>
                            <span class="block text-xs text-slate-500">Ce contrôle est légalement exigé par le Code de
                                la Construction</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t pt-4 border-slate-100">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Type de document attendu</label>
                    <input type="text" name="type_document_attendu" value="{{ old('type_document_attendu') }}"
                        placeholder="Ex: Rapport de vérification sans réserve, Attestation..."
                        class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Intervenant prévu</label>
                    <input type="text" name="intervenant_prevu" value="{{ old('intervenant_prevu') }}"
                        placeholder="Ex: Bureau de contrôle agréé (Apave, Socotec...)"
                        class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        {{-- BLOC 2 : TYPES D'ERP SOUMIS REGROUPÉS --}}
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm mt-6">
            <div class="border-b pb-3 mb-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">
                    🏢 Types d'ERP soumis à ce contrôle
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Cochez les catégories d'établissements qui requièrent ce
                    contrôle technique.</p>
            </div>

            {{-- Regroupement par Catégorie d'ERP --}}
            <div class="space-y-6">
                @foreach($typesErp->groupBy('categorie_erp') as $categorie => $groupes)
                <div class="bg-slate-50/50 p-4 rounded-xl border border-slate-100">
                    <h4 class="text-xs font-bold text-slate-600 uppercase mb-3 tracking-wide">
                        📍 Établissements de Catégorie {{ $categorie }}
                    </h4>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($groupes as $erp)
                        <label id="label-erp-{{ $erp->id_type_erp }}"
                            class="flex items-start gap-3 p-3 bg-white border border-slate-200 rounded-lg hover:border-blue-300 hover:shadow-sm cursor-pointer transition select-none">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="types_erp[]" value="{{ $erp->id_type_erp }}"
                                    onchange="toggleErpStyle(this, 'label-erp-{{ $erp->id_type_erp }}')"
                                    {{ is_array(old('types_erp')) && in_array($erp->id_type_erp, old('types_erp')) ? 'checked' : '' }}
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                            </div>
                            <div class="text-sm">
                                <p class="font-bold text-slate-800">Type {{ $erp->type_erp }}</p>
                                <p class="text-xs text-slate-500" title="{{ $erp->reglementation_applicable }}">
                                    {{ Str::limit($erp->reglementation_applicable, 35) }}
                                </p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ACTIONS DE FIN --}}
        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('controles.index') }}"
                class="px-5 py-2.5 border border-slate-300 text-slate-700 font-semibold rounded-lg hover:bg-slate-50 transition">Annuler</a>
            <button type="submit"
                class="px-5 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition shadow-sm">
                ➕ Créer le contrôle réglementaire
            </button>
        </div>
    </form>
</div>

<script>
function toggleErpStyle(checkbox, labelId) {
    const label = document.getElementById(labelId);
    if (checkbox.checked) {
        label.classList.add('border-blue-500', 'bg-blue-50/40');
        label.classList.remove('border-slate-200', 'bg-white');
    } else {
        label.classList.remove('border-blue-500', 'bg-blue-50/40');
        label.classList.add('border-slate-200', 'bg-white');
    }
}
</script>
@endsection