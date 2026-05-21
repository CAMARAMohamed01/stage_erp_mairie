@extends('layouts.app')

@section('header_title', 'Ajouter un Ouvrage d\'Art')

@section('content')
    <div class="max-w-4xl mx-auto">
        <form action="{{ route('ouvrages.store') }}" method="POST"
            class="bg-white p-8 rounded-xl border border-slate-200 shadow-sm space-y-8">
            @csrf

            <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                <span class="text-3xl">🌉</span>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Nouvel Ouvrage d'Art</h2>
                    <p class="text-sm text-slate-500">Pont, mur de soutènement, passerelle...</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-slate-800 uppercase bg-slate-50 p-2 rounded">1. Identification</h3>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Nom de l'ouvrage <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nom_ouvrage" required placeholder="Ex: Pont de la République"
                            class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Type d'ouvrage</label>
                        <select name="type_ouvrage"
                            class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Sélectionner --</option>
                            <option value="Pont">Pont / Viaduc</option>
                            <option value="Mur de soutènement">Mur de soutènement</option>
                            <option value="Passerelle">Passerelle piétonne</option>
                            <option value="Buse">Buse / Dalot</option>
                            <option value="Autre">Autre</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Voie rattachée <span
                                class="text-red-500">*</span></label>
                        <select name="id_voie" required
                            class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Choisir une voie --</option>
                            @foreach($voies as $voie)
                                <option value="{{ $voie->id_voie }}" {{ request('id_voie') == $voie->id_voie ? 'selected' : '' }}>
                                    {{ $voie->nom_voie }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-slate-800 uppercase bg-slate-50 p-2 rounded">2. Caractéristiques</h3>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Franchissement</label>
                        <input type="text" name="franchissement" placeholder="Ex: Cours d'eau (Le Fier), Voie ferrée..."
                            class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Domaine</label>
                        <input type="text" name="domaine" placeholder="Ex: Routier, Piétonnier..."
                            class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg space-y-3">
                        <p class="text-xs font-bold text-slate-500 uppercase">Spécificités réglementaires</p>

                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="sous_loi_didier" value="1"
                                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-5 h-5">
                            <span class="text-sm font-semibold text-slate-700">Soumis à la Loi Didier</span>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="est_programme_national" value="1"
                                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-5 h-5">
                            <span class="text-sm font-semibold text-slate-700">Inscrit au Programme National (PNP)</span>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="dimension_sup_2m" value="1"
                                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-5 h-5">
                            <span class="text-sm font-semibold text-slate-700">Ouverture > 2 mètres</span>
                        </label>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Commentaires / État général</label>
                <textarea name="commentaire" rows="3"
                    class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Observations sur l'état de l'ouvrage, date de la dernière inspection..."></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="history.back()"
                    class="px-5 py-2.5 border border-slate-300 text-slate-700 font-bold rounded-lg hover:bg-slate-50 transition">
                    Annuler
                </button>
                <button type="submit"
                    class="px-5 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition">
                    💾 Enregistrer l'ouvrage
                </button>
            </div>
        </form>
    </div>
@endsection