@extends('layouts.app')
@section('header_title', 'Configuration Écriture')
@section('content')
<div class="max-w-2xl mx-auto">
    <form
        action="{{ isset($operation) ? route('operations-comptables.update', $operation->id_operation) : route('operations-comptables.store') }}"
        method="POST" class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        @csrf
        @if(isset($operation)) @method('PUT') @endif

        <div class="p-6 bg-slate-50 border-b border-slate-200">
            <h1 class="text-base font-bold text-slate-900">{{ isset($operation) ? 'Modifier' : 'Ajouter' }} une ligne
                d'opération comptable</h1>
        </div>

        <div class="p-6 space-y-4 text-xs font-medium text-slate-700">
            <div>
                <label class="block font-bold text-slate-600 mb-1">Code / Numéro d'opération (Unique) *</label>
                <input type="text" name="numero_operation" required
                    value="{{ old('numero_operation', $operation->numero_operation ?? '') }}"
                    placeholder="Ex: OP_2026_VRD"
                    class="w-full border rounded-lg p-2.5 bg-slate-50 focus:bg-white focus:outline-none">
            </div>
            <div>
                <label class="block font-bold text-slate-600 mb-1">Libellé analytique *</label>
                <input type="text" name="libelle_operation"
                    value="{{ old('libelle_operation', $operation->libelle_operation ?? '') }}"
                    placeholder="Ex: Travaux d'aménagement des voies urbaines"
                    class="w-full border rounded-lg p-2.5 bg-slate-50 focus:bg-white focus:outline-none">
            </div>
            <div>
                <label class="block font-bold text-slate-600 mb-1">Nature de l'opération</label>
                <input type="text" name="nature_operation"
                    value="{{ old('nature_operation', $operation->nature_operation ?? '') }}"
                    placeholder="Ex: Section d'investissement / Fonctionnement"
                    class="w-full border rounded-lg p-2.5 bg-slate-50 focus:bg-white focus:outline-none">
            </div>
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-2">
            <button type="submit"
                class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-xs transition shadow-sm">💾
                Sauvegarder l'écriture</button>
        </div>
    </form>
</div>
@endsection