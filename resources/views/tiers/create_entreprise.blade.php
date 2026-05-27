@extends('layouts.app')

@section('title', 'Nouvelle Entreprise')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-sm border border-slate-200">
        <div class="mb-8 border-b pb-4">
            <h1 class="text-2xl font-bold text-slate-800">🏢 Ajouter un Prestataire / Entreprise</h1>
            <p class="text-slate-500 text-sm mt-1">Création d'un Tiers Moral dans la base de données.</p>
        </div>

        <form action="{{ route('tiers.store_entreprise') }}" method="POST" class="space-y-6">
            @csrf

            <div class="bg-slate-50 p-6 rounded-lg border border-slate-100 mb-6">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 mb-4">Informations Légales</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Raison Sociale *</label>
                        <input type="text" name="raison_sociale" required value="{{ old('raison_sociale') }}"
                            class="w-full border-slate-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">N° SIRET</label>
                        <input type="text" name="siret" maxlength="14" value="{{ old('siret') }}"
                            class="w-full border-slate-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">N° TVA Intracommunautaire</label>
                        <input type="text" name="numero_tva_intra" value="{{ old('numero_tva_intra') }}"
                            class="w-full border-slate-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 p-6 rounded-lg border border-slate-100 mb-6">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 mb-4">Contact & Gestion</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nom du Contact Principal</label>
                        <input type="text" name="nom_contact" value="{{ old('nom_contact') }}"
                            class="w-full border-slate-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Téléphone de l'entreprise</label>
                        <input type="text" name="tel_tiers" maxlength="12" value="{{ old('tel_tiers') }}"
                            class="w-full border-slate-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Email générique</label>
                        <input type="email" name="email_tiers" value="{{ old('email_tiers') }}"
                            class="w-full border-slate-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
                        @error('email_tiers') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">N° Compte Client (Interne
                            Mairie)</label>
                        <input type="text" name="num_compte_client" value="{{ old('num_compte_client') }}"
                            class="w-full border-slate-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Alias (Nom court)</label>
                        <input type="text" name="alias_tiers" maxlength="10" value="{{ old('alias_tiers') }}"
                            class="w-full border-slate-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-200">
                <a href="{{ route('tiers.entreprises') }}"
                    class="px-6 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg mr-3 hover:bg-slate-50 transition">Annuler</a>
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-emerald-700 shadow-sm transition">
                    Enregistrer l'entreprise
                </button>
            </div>
        </form>
    </div>
@endsection