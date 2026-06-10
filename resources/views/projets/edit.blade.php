@extends('layouts.app')

@section('title', 'Modifier : ' . $projet->nom_projet)

@section('content')
    <div class="max-w-4xl mx-auto pb-12">
        <div class="bg-white p-8 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3 mb-6 border-b pb-4">
                <span class="text-3xl">✏️</span>
                <h1 class="text-2xl font-bold text-slate-800">Modification du projet : {{ $projet->nom_projet }}</h1>
            </div>

            <form action="{{ route('projets.update', $projet->id_projet) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nom du projet
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="nom_projet" required value="{{ old('nom_projet', $projet->nom_projet) }}"
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

                    <div>
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

                    <div class="md:col-span-2 pt-4 border-t border-slate-100">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-lg">📍</span>
                            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Périmètre d'action du
                                projet</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @php
                                $batimentsLies = $projet->batiments->pluck('id_batiment')->toArray();
                                $lieuxLies = $projet->lieuxPublics->pluck('id_lieu')->toArray();
                            @endphp

                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Bâtiments
                                    concernés</label>
                                <select name="batiments[]" multiple
                                    class="w-full rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500 h-40">
                                    @foreach($batiments as $bat)
                                        <option value="{{ $bat->id_batiment }}" {{ in_array($bat->id_batiment, $batimentsLies) ? 'selected' : '' }}>
                                            {{ $bat->nom_bat }} (Réf:
                                            #{{ $bat->id_batiment }}{{ $bat->surface_totale_m2 ? ' - ' . $bat->surface_totale_m2 . ' m²' : '' }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-[10px] text-slate-500 mt-2 italic">Maintenez CTRL (ou Cmd sur Mac) pour
                                    sélectionner plusieurs bâtiments.</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Lieux
                                    publics concernés</label>
                                <select name="lieux[]" multiple
                                    class="w-full rounded-lg border-slate-300 shadow-sm focus:ring-2 focus:ring-blue-500 h-40">
                                    @foreach($lieux as $lieu)
                                        <option value="{{ $lieu->id_lieu }}" {{ in_array($lieu->id_lieu, $lieuxLies) ? 'selected' : '' }}>
                                            {{ $lieu->nom_lieu }}
                                            {{ $lieu->typologie_lieu ? '[' . $lieu->typologie_lieu . ']' : '' }} (Réf:
                                            #{{ $lieu->id_lieu }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
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
                        class="px-6 py-2 bg-amber-600 text-white font-bold rounded-lg hover:bg-amber-700 transition shadow-md">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection