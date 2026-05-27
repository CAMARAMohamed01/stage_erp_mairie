@extends('layouts.app')

@section('title', 'Ajouter un compte bancaire')

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-sm border border-slate-200">
        <div class="mb-8 border-b pb-4">
            <h1 class="text-2xl font-bold text-slate-800">💶 Ajouter un compte bancaire</h1>
            <p class="text-slate-500 text-sm mt-1">
                Ce compte sera rattaché à : <span class="font-bold">{{ $tiers->nom_affiche ?? 'Ce dossier' }}</span>
            </p>
        </div>

        <form action="{{ route('tiers.comptes.store', $tiers->id_tiers) }}" method="POST" enctype="multipart/form-data"
            class="space-y-6">
            @csrf

            <div class="bg-slate-50 p-6 rounded-lg border border-slate-100">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Titulaire du compte *</label>
                        <input type="text" name="titulaire_compte" required
                            value="{{ old('titulaire_compte', $tiers->nom_affiche ?? '') }}"
                            class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                        <span class="text-xs text-slate-500">Généralement le nom de la personne ou l'entreprise.</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">IBAN *</label>
                            <input type="text" name="iban" required maxlength="34" value="{{ old('iban') }}"
                                placeholder="FR76..."
                                class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm font-mono uppercase">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">BIC (SWIFT) *</label>
                            <input type="text" name="bic" required maxlength="11" value="{{ old('bic') }}"
                                class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm font-mono uppercase">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Clé RIB (Optionnel)</label>
                        <input type="text" name="rib" maxlength="50" value="{{ old('rib') }}"
                            class="w-full md:w-1/3 border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm font-mono">
                    </div>
                </div>
            </div>

            <div class="bg-blue-50/50 p-6 rounded-lg border border-blue-100">
                <label class="block text-sm font-medium text-slate-700 mb-2">Joindre un document (RIB, Mandat SEPA...) -
                    Optionnel</label>
                <input type="file" name="document_rib" accept=".pdf,.jpg,.jpeg,.png"
                    class="block w-full text-sm text-slate-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-full file:border-0
                            file:text-sm file:font-semibold
                            file:bg-blue-50 file:text-blue-700
                            hover:file:bg-blue-100
                            border border-slate-200 rounded-lg bg-white p-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                <p class="mt-2 text-xs text-slate-500">Formats autorisés : PDF, JPG, PNG. Poids maximum : 5 Mo.</p>
                @error('document_rib')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-200">
                <div class="flex justify-end pt-4 border-t border-slate-200">
                    @php
                        // Si ce n'est pas "Physique", c'est forcément une entreprise (Morale ou Personne Morale)
                        $backRoute = ($tiers->type_tiers !== 'Physique')
                            ? route('tiers.show_entreprise', $tiers->id_tiers)
                            : route('tiers.show', $tiers->id_tiers);
                    @endphp

                    <a href="{{ $backRoute }}"
                        class="px-6 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg mr-3 hover:bg-slate-50 transition">Annuler</a>

                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 shadow-sm transition">
                        Enregistrer le compte
                    </button>
                </div>

        </form>
    </div>
@endsection