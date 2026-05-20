@extends('layouts.app')

@section('header_title', 'Modifier l\'emplacement')

@section('content')
<div class="max-w-3xl mx-auto pb-12">

    <div class="mb-6 flex justify-between items-end">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Modifier l'Emplacement</h1>
            <p class="text-sm text-slate-500 mt-1">Mise à jour de la tombe, caveau ou case :
                {{ $emplacement->reference_emplacement }}.</p>
        </div>
        <a href="{{ route('emplacements.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">←
            Retour à la liste</a>
    </div>

    <form action="{{ route('emplacements.update', $emplacement->id_emplacement) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">🪦 Détails de l'emplacement
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Cimetière (Lieu
                        Public) <span class="text-red-500">*</span></label>
                    <select name="id_lieu" required
                        class="w-full rounded-lg border-slate-300 text-sm bg-slate-50 focus:ring-slate-900 focus:border-slate-900">
                        <option value="">-- Sélectionner le cimetière --</option>
                        @foreach($cimetieres as $cimetiere)
                        <option value="{{ $cimetiere->id_lieu }}"
                            {{ (old('id_lieu', $emplacement->id_lieu) == $cimetiere->id_lieu) ? 'selected' : '' }}>
                            {{ $cimetiere->nom_lieu }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Référence <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="reference_emplacement"
                        value="{{ old('reference_emplacement', $emplacement->reference_emplacement) }}" required
                        placeholder="Ex: Allée C, Rang 4, N°12"
                        class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900 focus:border-slate-900">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Type
                        d'emplacement</label>
                    <select name="type_emplacement"
                        class="w-full rounded-lg border-slate-300 text-sm bg-slate-50 focus:ring-slate-900 focus:border-slate-900">
                        @foreach(['Pleine terre', 'Caveau', 'Case Columbarium', 'Cavurne', 'Enfeu'] as $type)
                        <option value="{{ $type }}"
                            {{ (old('type_emplacement', $emplacement->type_emplacement) == $type) ? 'selected' : '' }}>
                            {{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Capacité Max
                        (Places)</label>
                    <input type="number" name="capacite_max" min="1"
                        value="{{ old('capacite_max', $emplacement->capacite_max) }}"
                        class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900 focus:border-slate-900">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Statut</label>
                    <select name="statut_occupation"
                        class="w-full rounded-lg border-slate-300 text-sm bg-slate-50 focus:ring-slate-900 focus:border-slate-900">
                        @foreach(['Libre', 'Occupé', 'Réservé', 'Repris'] as $statut)
                        <option value="{{ $statut }}"
                            {{ (old('statut_occupation', $emplacement->statut_occupation) == $statut) ? 'selected' : '' }}>
                            {{ $statut }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="flex justify-between pt-2">
            <button type="button"
                onclick="if(confirm('Êtes-vous sûr de vouloir supprimer définitivement cet emplacement ?')) document.getElementById('delete-form').submit();"
                class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 font-semibold rounded-lg shadow-sm transition flex items-center gap-2">
                🗑️ Supprimer
            </button>

            <button type="submit"
                class="px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-lg shadow-md transition">
                💾 Mettre à jour l'emplacement
            </button>
        </div>
    </form>

    <form id="delete-form" action="{{ route('emplacements.destroy', $emplacement->id_emplacement) }}" method="POST"
        class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection