@extends('layouts.app')

@section('header_title', 'Ajouter un Type d\'ERP')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6 pb-12">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">➕ Nouvelle classification ERP</h1>
            <a href="{{ route('types-erp.index') }}"
                class="text-sm font-semibold text-slate-500 hover:text-slate-800 transition">← Retour à la liste</a>
        </div>

        <form action="{{ route('types-erp.store') }}" method="POST"
            class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Catégorie ERP (1 à 5)</label>
                    <input type="number" name="categorie_erp" value="{{ old('categorie_erp') }}" min="1" max="5"
                        class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Lettre du Type (ex: L, M, R...)</label>
                    <input type="text" name="type_erp" value="{{ old('type_erp') }}" maxlength="2"
                        class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 uppercase">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Public Cible</label>
                <input type="text" name="public_cible" value="{{ old('public_cible') }}"
                    placeholder="Ex: Établissement de soins, Salle de spectacle..."
                    class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Réglementation applicable *</label>
                <input type="text" name="reglementation_applicable" value="{{ old('reglementation_applicable') }}" required
                    placeholder="Ex: Arrêté du 25 juin 1980..."
                    class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('types-erp.index') }}"
                    class="px-6 py-2.5 border border-slate-300 text-slate-700 font-semibold rounded-lg hover:bg-slate-50 transition">Annuler</a>
                <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition shadow-sm">Créer
                    l'ERP</button>
            </div>
        </form>
    </div>
@endsection