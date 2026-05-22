@extends('layouts.app')

@section('header_title', 'Ajouter une Commune Partenaire')

@section('content')
    <div class="max-w-3xl mx-auto">
        <form action="{{ route('communes.store') }}" method="POST"
            class="bg-white p-8 rounded-xl border border-slate-200 shadow-sm space-y-6">
            @csrf

            <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                <span class="text-3xl">🏛️</span>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Nouvelle Commune Partenaire</h2>
                    <p class="text-sm text-slate-500">Ajout d'une collectivité pour la gestion partagée d'ouvrages.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-1">Nom de la commune <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="nom_commune" required maxlength="80" value="{{ old('nom_commune') }}"
                        placeholder="Ex: Annecy" class="w-full border-slate-300 rounded-lg focus:ring-blue-500">
                    @error('nom_commune') <p class="text-xs text-red-600 font-bold mt-1">⚠️ {{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Code Postal</label>
                    <input type="text" name="code_postal" maxlength="5" value="{{ old('code_postal') }}"
                        placeholder="Ex: 74000" class="w-full border-slate-300 rounded-lg focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Numéro SIRET</label>
                    <input type="text" name="siret_mairie" maxlength="14" value="{{ old('siret_mairie') }}"
                        placeholder="Ex: 21740010000010" class="w-full border-slate-300 rounded-lg focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-1">Email de contact (Services
                        techniques)</label>
                    <input type="email" name="email_contact" maxlength="100" value="{{ old('email_contact') }}"
                        placeholder="Ex: dst@annecy.fr" class="w-full border-slate-300 rounded-lg focus:ring-blue-500">
                </div>

            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                <button type="button" onclick="history.back()"
                    class="px-5 py-2.5 border border-slate-300 text-slate-700 font-bold rounded-lg hover:bg-slate-50 transition">Annuler</button>
                <button type="submit"
                    class="px-5 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition">💾
                    Enregistrer la commune</button>
            </div>
        </form>
    </div>
@endsection