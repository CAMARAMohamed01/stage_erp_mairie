@extends('layouts.app')

@section('title', 'Modifier l\'Opération Comptable')

@section('content')
<div class="max-w-2xl mx-auto pb-12">
    <form action="{{ route('operations-comptables.update', $operation->id_operation) }}" method="POST"
        class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        @csrf
        @method('PUT')

        <div class="p-6 bg-slate-50 border-b border-slate-200 flex items-center gap-4">
            <div
                class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl shadow-sm">
                ✏️
            </div>
            <div>
                <h1 class="text-base font-bold text-slate-900">Modifier l'opération comptable :
                    {{ $operation->numero_operation }}
                </h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Mise à jour des libellés analytiques et de la
                    nature de l'écriture.</p>
            </div>
        </div>

        <div class="p-6 space-y-4 text-xs font-medium text-slate-700">
            <div>
                <label class="block font-bold text-slate-600 mb-1">Code / Numéro d'opération (Unique) *</label>
                <input type="text" name="numero_operation" required
                    value="{{ old('numero_operation', $operation->numero_operation) }}" placeholder="Ex: OP_2026_VRD"
                    class="w-full border border-slate-300 rounded-lg p-2.5 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition">
            </div>

            <div>
                <label class="block font-bold text-slate-600 mb-1">Libellé analytique *</label>
                <input type="text" name="libelle_operation" required
                    value="{{ old('libelle_operation', $operation->libelle_operation) }}"
                    placeholder="Ex: Travaux d'aménagement des voies urbaines"
                    class="w-full border border-slate-300 rounded-lg p-2.5 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition">
            </div>

            <div>
                <label class="block font-bold text-slate-600 mb-1">Nature de l'opération</label>
                <input type="text" name="nature_operation"
                    value="{{ old('nature_operation', $operation->nature_operation) }}"
                    placeholder="Ex: Section d'investissement / Fonctionnement"
                    class="w-full border border-slate-300 rounded-lg p-2.5 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition">
            </div>
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-2">
            <a href="{{ route('operations-comptables.index') }}"
                class="px-5 py-2 text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg font-bold transition text-xs">
                Annuler
            </a>
            <button type="submit"
                class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-xs transition shadow-sm">
                💾 Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
@endsection