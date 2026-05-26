@extends('layouts.app')

@section('title', 'Modifier : ' . $projet->nom_projet)

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white p-8 rounded-xl border border-slate-200 shadow-sm">
            <h1 class="text-2xl font-bold text-slate-800 mb-6 border-b pb-4">✏️ Modification du projet :
                {{ $projet->nom_projet }}
            </h1>

            <form action="{{ route('projets.update', $projet->id_projet) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nom du projet
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="nom_projet" required value="{{ old('nom_projet', $projet->nom_projet) }}"
                            class="w-full rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500">
                        @error('nom_projet') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Type de projet
                            <span class="text-red-500">*</span></label>
                        <select name="type_projet" required
                            class="w-full rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500">
                            @foreach(['Mandat', 'Petit projet', 'Maintenance', 'Étude'] as $type)
                                <option value="{{ $type }}" {{ old('type_projet', $projet->type_projet) == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Année Mandat
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="annee_mandat" required
                            value="{{ old('annee_mandat', $projet->annee_mandat) }}"
                            class="w-full rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Budget Alloué
                            (€)</label>
                        <input type="number" step="0.01" name="budget_global_alloue"
                            value="{{ old('budget_global_alloue', $projet->budget_global_alloue) }}"
                            class="w-full rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Chef de projet
                            <span class="text-red-500">*</span></label>
                        <select name="id_user" required
                            class="w-full rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500">
                            @foreach($utilisateurs as $user)
                                <option value="{{ $user->id_user }}" {{ old('id_user', $projet->id_user) == $user->id_user ? 'selected' : '' }}>
                                    {{ $user->prenom_user }} {{ $user->nom_user }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Avis /
                            Note</label>
                        <textarea name="avis" rows="3"
                            class="w-full rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500">{{ old('avis', $projet->avis) }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t">
                    <a href="{{ route('projets.index') }}"
                        class="px-6 py-2 bg-slate-100 text-slate-700 font-bold rounded-lg hover:bg-slate-200 transition">Annuler</a>
                    <button type="submit"
                        class="px-6 py-2 bg-amber-600 text-white font-bold rounded-lg hover:bg-amber-700 transition">Enregistrer
                        les modifications</button>
                </div>
            </form>
        </div>
    </div>
@endsection