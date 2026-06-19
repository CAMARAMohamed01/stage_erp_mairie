@extends('layouts.app')

@section('title', 'Nouveau Projet')

@section('content')
    <div class="max-w-4xl mx-auto pb-12">
        <div class="bg-white p-8 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3 mb-6 border-b pb-4">
                <span class="text-3xl">🏗️</span>
                <h1 class="text-2xl font-bold text-slate-800">Création d'un nouveau projet</h1>
            </div>

            <form action="{{ route('projets.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nom du projet
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="nom_projet" required value="{{ old('nom_projet') }}"
                            class="w-full rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500">
                        @error('nom_projet')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Type de projet
                            <span class="text-red-500">*</span></label>
                        <select name="type_projet" required
                            class="w-full rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Choisir --</option>
                            <option value="Mandat" {{ old('type_projet') == 'Mandat' ? 'selected' : '' }}>Projet de Mandat
                            </option>
                            <option value="Petit projet" {{ old('type_projet') == 'Petit projet' ? 'selected' : '' }}>Petit
                                projet</option>
                            <option value="Maintenance" {{ old('type_projet') == 'Maintenance' ? 'selected' : '' }}>
                                Maintenance</option>
                            <option value="Aménagement" {{ old('type_projet') == 'Aménagement' ? 'selected' : '' }}>
                                Aménagement</option>
                            <option value="Renovation" {{ old('type_projet') == 'Renovation' ? 'selected' : '' }}>
                                Rénovation</option>
                            <option value="Grand projet" {{ old('type_projet') == 'Grand projet' ? 'selected' : '' }}>Grand
                                projet</option>
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
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Budget Alloué
                            (€)</label>
                        <input type="number" step="0.01" name="budget_global_alloue"
                            value="{{ old('budget_global_alloue') }}"
                            class="w-full rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Chef de projet
                            <span class="text-red-500">*</span></label>
                        <select name="id_user" required
                            class="w-full rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Choisir un agent --</option>
                            @foreach($utilisateurs as $user)
                                <option value="{{ $user->id_user }}" {{ old('id_user') == $user->id_user ? 'selected' : '' }}>
                                    {{ $user->prenom_user }} {{ $user->nom_user }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2 pt-4 border-t border-slate-100">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-lg">📍</span>
                            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Périmètre prévisionnel de
                                l'opération</h3>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Bâtiments</label>
                                <select name="batiments[]" multiple
                                    class="w-full rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500 h-32">
                                    @foreach($batiments as $bat)
                                        <option value="{{ $bat->id_batiment }}">{{ $bat->nom_bat }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Lieux
                                    publics</label>
                                <select name="lieux[]" multiple
                                    class="w-full rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500 h-32">
                                    @foreach($lieux as $lieu)
                                        <option value="{{ $lieu->id_lieu }}">{{ $lieu->nom_lieu }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Voies</label>
                                <select name="voies[]" multiple
                                    class="w-full rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500 h-32">
                                    @foreach($voies as $voie)
                                        <option value="{{ $voie->id_voie }}">{{ $voie->nom_voie ?? 'Sans nom' }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Quartiers /
                                    Lieux-dits</label>
                                <select name="lieux_dits[]" multiple
                                    class="w-full rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500 h-32">
                                    @foreach($lieuxDits as $ld)
                                        <option value="{{ $ld->id_lieu_dit }}">{{ $ld->nom_lieu_dit }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                        <p class="text-[10px] text-slate-500 mt-2 italic">Maintenez CTRL (ou Cmd sur Mac) pour sélectionner
                            plusieurs éléments.</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Avis / Note
                            d'opportunité</label>
                        <textarea name="avis" rows="3" placeholder="Expliquez ici le but du projet..."
                            class="w-full rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500">{{ old('avis') }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t">
                    <a href="{{ route('projets.index') }}"
                        class="px-6 py-2 bg-slate-100 text-slate-700 font-bold rounded-lg hover:bg-slate-200 transition">Annuler</a>
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition shadow-md">
                        Enregistrer le projet
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection