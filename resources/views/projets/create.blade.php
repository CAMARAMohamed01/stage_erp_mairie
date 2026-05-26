@extends('layouts.app')

@section('title', 'Nouveau Projet')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white p-8 rounded-xl border border-slate-200 shadow-sm">
            <h1 class="text-2xl font-bold text-slate-800 mb-6 border-b pb-4">🏗️ Création d'un nouveau projet</h1>

            <form action="{{ route('projets.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nom du
                            projet <span class="text-red-500">*</span></label>
                        <input type="text" name="nom_projet" required value="{{ old('nom_projet') }}"
                            class="w-full rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500">
                        @error('nom_projet') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Type de
                            projet <span class="text-red-500">*</span></label>
                        <select name="type_projet" required
                            class="w-full rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Choisir --</option>
                            <option value="Mandat" {{ old('type_projet') == 'Mandat' ? 'selected' : '' }}>Projet de
                                Mandat</option>
                            <option value="Petit projet" {{ old('type_projet') == 'Petit projet' ? 'selected' : '' }}>
                                Petit projet</option>
                            <option value="Maintenance" {{ old('type_projet') == 'Maintenance' ? 'selected' : '' }}>
                                Maintenance</option>
                            <option value="Étude" {{ old('type_projet') == 'Étude' ? 'selected' : '' }}>Étude</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Année Mandat
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="annee_mandat" placeholder="ex: 2026" required
                            value="{{ old('annee_mandat') }}"
                            class="w-full rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Budget
                            Alloué (€)</label>
                        <input type="number" step="0.01" name="budget_global_alloue"
                            value="{{ old('budget_global_alloue') }}"
                            class="w-full rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Chef de
                            projet (Utilisateur) <span class="text-red-500">*</span></label>
                        <select name="id_user" required
                            class="w-full rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Choisir un utilisateur --</option>
                            @foreach($utilisateurs as $user)
                                <option value="{{ $user->id_user }}" {{ old('id_user') == $user->id_user ? 'selected' : '' }}>
                                    {{ $user->prenom_user }}
                                    {{ $user->nom_user }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Avis /
                            Note</label>
                        <textarea name="avis" rows="3"
                            class="w-full rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500">{{ old('avis') }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t">
                    <a href="{{ route('projets.index') }}"
                        class="px-6 py-2 bg-slate-100 text-slate-700 font-bold rounded-lg hover:bg-slate-200 transition">Annuler</a>
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition">Enregistrer
                        le projet</button>
                </div>
            </form>
        </div>
    </div>
@endsection