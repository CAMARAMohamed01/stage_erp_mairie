@extends('layouts.app')

@section('header_title', 'Modifier le contrôle réglementaire')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-12">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">✏️ Modifier un contrôle</h1>
        <a href="{{ route('controles.index') }}"
            class="text-sm font-semibold text-slate-500 hover:text-slate-800 transition">← Retour au catalogue</a>
    </div>

    <form action="{{ route('controles.update', $controle->id_controle) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-6">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Désignation du contrôle *</label>
                <input type="text" name="designation" value="{{ old('designation', $controle->designation) }}" required
                    class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Domaine technique</label>
                    <input type="text" name="domaine_technique"
                        value="{{ old('domaine_technique', $controle->domaine_technique) }}"
                        placeholder="Ex: Électricité, Incendie..."
                        class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Type de contrôle</label>
                    <input type="text" name="type_controle" value="{{ old('type_controle', $controle->type_controle) }}"
                        placeholder="Ex: Périodique, Initial..."
                        class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Fréquence (en mois)</label>
                    <input type="number" name="frequence_mois"
                        value="{{ old('frequence_mois', $controle->frequence_mois) }}" min="1"
                        class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-center h-full pt-6">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="est_legalement_obligatoire" value="1"
                            {{ old('est_legalement_obligatoire', $controle->est_legalement_obligatoire) ? 'checked' : '' }}
                            class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-gray-300">
                        <span class="text-sm font-semibold text-slate-800">Ce contrôle est légalement obligatoire</span>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Type de document attendu</label>
                    <input type="text" name="type_document_attendu"
                        value="{{ old('type_document_attendu', $controle->type_document_attendu) }}"
                        class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Intervenant prévu</label>
                    <input type="text" name="intervenant_prevu"
                        value="{{ old('intervenant_prevu', $controle->intervenant_prevu) }}"
                        placeholder="Ex: Bureau de contrôle agréé"
                        class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm mt-6">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2">
                🏢 Types d'ERP soumis à ce contrôle
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($typesErp as $erp)
                <label
                    class="flex items-start gap-3 p-3 border {{ in_array($erp->id_type_erp, $erp_lies) ? 'border-blue-500 bg-blue-50/50' : 'border-slate-200 bg-white' }} rounded-lg hover:bg-slate-50 cursor-pointer transition">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="types_erp[]" value="{{ $erp->id_type_erp }}"
                            {{ in_array($erp->id_type_erp, $erp_lies) ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                    </div>
                    <div class="text-sm">
                        <p class="font-bold text-slate-800">Catégorie {{ $erp->categorie_erp }} - Type
                            {{ $erp->type_erp }}</p>
                        <p class="text-xs text-slate-500">{{ Str::limit($erp->reglementation_applicable, 40) }}</p>
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('controles.index') }}"
                class="px-6 py-2.5 border border-slate-300 text-slate-700 font-semibold rounded-lg hover:bg-slate-50 transition">Annuler</a>
            <button type="submit"
                class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition shadow-sm">Enregistrer
                les modifications</button>
        </div>
    </form>
</div>
@endsection