@extends('layouts.app')

@section('title', 'Ajouter un document')

@section('content')
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-sm border border-slate-200">
        <div class="mb-8 border-b pb-4">
            <h1 class="text-2xl font-bold text-slate-800">📄 Ajouter un document</h1>
            <p class="text-slate-500 text-sm mt-1">
                Dossier : <span class="font-bold">{{ $tiers->nom_affiche ?? 'Tiers #' . $tiers->id_tiers }}</span>
            </p>
        </div>

        <form action="{{ route('tiers.documents.store', $tiers->id_tiers) }}" method="POST" enctype="multipart/form-data"
            class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Type de document *</label>
                <select name="type_doc" required
                    class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                    <option value="">Sélectionner un type...</option>
                    <option value="Carte d'identité">Carte d'identité</option>
                    <option value="KBIS">Extrait KBIS</option>
                    <option value="Contrat">Contrat / Convention</option>
                    <option value="Attestation d'assurance">Attestation d'assurance</option>
                    <option value="Autre">Autre</option>
                </select>
            </div>

            <div class="bg-blue-50/50 p-6 rounded-lg border border-blue-100">
                <label class="block text-sm font-medium text-slate-700 mb-2">Fichier à téléverser *</label>
                <input type="file" name="fichier" required accept=".pdf,.jpg,.jpeg,.png"
                    class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-200 rounded-lg bg-white p-2" />
                <p class="mt-2 text-xs text-slate-500">Formats autorisés : PDF, JPG, PNG. Poids maximum : 5 Mo.</p>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-200">
                @php
                    $backRoute = ($tiers->type_tiers !== 'Physique')
                        ? route('tiers.show_entreprise', $tiers->id_tiers)
                        : route('tiers.show', $tiers->id_tiers);
                @endphp
                <a href="{{ $backRoute }}"
                    class="px-6 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg mr-3 hover:bg-slate-50 transition">Annuler</a>
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 shadow-sm transition">
                    Enregistrer le document
                </button>
            </div>
        </form>
    </div>
@endsection