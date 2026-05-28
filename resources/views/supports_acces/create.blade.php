@extends('layouts.app')

@section('header_title', 'Enregistrer un Support d\'Accès')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6 pb-12">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">🔑 Enregistrer un support</h1>
            <a href="{{ route('supports-acces.index') }}"
                class="text-sm font-semibold text-slate-500 hover:text-slate-800 transition">← Retour au registre</a>
        </div>

        <form action="{{ route('supports-acces.store') }}" method="POST"
            class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Numéro de série / Identifiant *</label>
                    <input type="text" name="numero_serie" value="{{ old('numero_serie') }}" required
                        placeholder="Ex: G-2026-XYZ"
                        class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-slate-500 uppercase font-mono">
                    @error('numero_serie') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Type de support d'accès</label>
                    <select name="type_support"
                        class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-slate-500">
                        <option value="Clé physique">Clé physique</option>
                        <option value="Badge RFID">Badge RFID</option>
                        <option value="Vigik">Vigik</option>
                        <option value="Télécommande">Télécommande</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Observations / Commentaires</label>
                <textarea name="observations" rows="3"
                    placeholder="Description de la clé (Ex: Clé passe-partout services techniques, Badge d'accès mairie...)"
                    class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-slate-500">{{ old('observations') }}</textarea>
            </div>

            <div class="flex items-center">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="est_actif" value="1" checked
                        class="w-5 h-5 text-slate-900 rounded focus:ring-slate-500 border-gray-300">
                    <span class="text-sm font-semibold text-slate-800">Activer le support dès l'enregistrement</span>
                </label>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('supports-acces.index') }}"
                    class="px-6 py-2.5 border border-slate-300 text-slate-700 font-semibold rounded-lg hover:bg-slate-50 transition">Annuler</a>
                <button type="submit"
                    class="px-6 py-2.5 bg-slate-900 text-white font-bold rounded-lg hover:bg-slate-800 transition shadow-sm">Créer
                    l'élément</button>
            </div>
        </form>
    </div>
@endsection