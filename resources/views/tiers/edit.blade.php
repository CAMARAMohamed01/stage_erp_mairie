@extends('layouts.app')

@section('title', 'Modifier : ' . ($citoyen->physique->nom_tiers ?? 'Citoyen'))

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-sm border border-slate-200">
        <div class="mb-8 border-b pb-4">
            <h1 class="text-2xl font-bold text-slate-800">✏️ Modifier le dossier citoyen</h1>
            <p class="text-slate-500 text-sm mt-1">Mise à jour des informations de
                {{ $citoyen->physique->prenom_tiers ?? '' }} {{ $citoyen->physique->nom_tiers ?? '' }}
            </p>
        </div>

        <form action="{{ route('tiers.update', $citoyen->id_tiers) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-blue-50 p-6 rounded-lg border border-blue-100 mb-6">
                <h3 class="text-sm font-bold uppercase tracking-wider text-blue-800 mb-4">Identité</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Civilité</label>
                        <select name="civilite"
                            class="w-full md:w-1/3 border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm bg-white">
                            <option value="">-- Sélectionner --</option>
                            <option value="Monsieur" {{ old('civilite', $citoyen->physique->civilite) == 'Monsieur' ? 'selected' : '' }}>Monsieur
                            </option>
                            <option value="Madame" {{ old('civilite', $citoyen->physique->civilite) == 'Madame' ? 'selected' : '' }}>Madame
                            </option>
                            <option value="Autre" {{ old('civilite', $citoyen->physique->civilite) == 'Autre' ? 'selected' : '' }}>Autre
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nom <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nom_tiers" required maxlength="50"
                            value="{{ old('nom_tiers', $citoyen->physique->nom_tiers) }}"
                            class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm uppercase">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Prénom <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="prenom_tiers" required maxlength="50"
                            value="{{ old('prenom_tiers', $citoyen->physique->prenom_tiers) }}"
                            class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm capitalize">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Date de naissance</label>
                        <input type="date" name="date_naissance"
                            value="{{ old('date_naissance', $citoyen->physique->date_naissance) }}"
                            class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 p-6 rounded-lg border border-slate-100 mb-6">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 mb-4">Coordonnées</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Numéro de téléphone</label>
                        <input type="text" name="tel_tiers" maxlength="12"
                            value="{{ old('tel_tiers', $citoyen->tel_tiers) }}"
                            class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Adresse Email</label>
                        <input type="email" name="email_tiers" value="{{ old('email_tiers', $citoyen->email_tiers) }}"
                            class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                        @error('email_tiers') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-200">
                <a href="{{ route('tiers.show', $citoyen->id_tiers) }}"
                    class="px-6 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg mr-3 hover:bg-slate-50 transition">Annuler</a>
                <button type="submit"
                    class="px-6 py-2 bg-amber-500 text-white font-bold rounded-lg hover:bg-amber-600 shadow-sm transition">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
@endsection