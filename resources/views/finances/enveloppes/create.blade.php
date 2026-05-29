@extends('layouts.app')
@section('header_title', 'Configuration Enveloppe')
@section('content')
<div class="max-w-2xl mx-auto">
    <form
        action="{{ isset($enveloppe) ? route('enveloppes-budgetaires.update', $enveloppe->id_budget) : route('enveloppes-budgetaires.store') }}"
        method="POST" class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        @csrf
        @if(isset($enveloppe)) @method('PUT') @endif

        <div class="p-6 bg-slate-50 border-b border-slate-200">
            <h1 class="text-base font-bold text-slate-900">{{ isset($enveloppe) ? 'Ajuster' : 'Voter' }} une enveloppe
                budgétaire annuelle</h1>
        </div>

        <div class="p-6 space-y-4 text-xs font-medium text-slate-700">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Année d'exercice budgétaire *</label>
                    <input type="number" min="2020" max="2050" name="annee_exercice" required
                        value="{{ old('annee_exercice', $enveloppe->annee_exercice ?? '2026') }}"
                        class="w-full border rounded-lg p-2.5 bg-slate-50 focus:bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Montant global alloué TTC (€) *</label>
                    <input type="number" step="0.01" name="montant_vote_ttc" required
                        value="{{ old('montant_vote_ttc', $enveloppe->montant_vote_ttc ?? '') }}" placeholder="0.00"
                        class="w-full border rounded-lg p-2.5 bg-slate-50 focus:bg-white focus:outline-none">
                </div>
            </div>
            <div>
                <label class="block font-bold text-slate-600 mb-1">Service délégataire principal</label>
                <select name="id_service" class="w-full border rounded-lg p-2.5 bg-white focus:outline-none">
                    <option value="">-- Budget Général Communal --</option>
                    @foreach($services as $s)
                    <option value="{{ $s->id_service }}"
                        {{ old('id_service', $enveloppe->id_service ?? '') == $s->id_service ? 'selected' : '' }}>
                        {{ $s->nom_service }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-2">
            <button type="submit"
                class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-xs transition shadow-sm">💾
                Notifier le crédit budget</button>
        </div>
    </form>
</div>
@endsection