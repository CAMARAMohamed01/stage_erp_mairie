@extends('layouts.app')

@section('header_title', 'Modifier le compteur')

@section('content')
<div class="max-w-4xl mx-auto pb-12">
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Modifier le Compteur</h1>
            <p class="text-sm text-slate-500 mt-1">Mise à jour du point de comptage :
                <strong>{{ $compteur->point_comptage }}</strong>
            </p>
        </div>
        <a href="{{ route('compteurs.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">←
            Retour à la liste</a>
    </div>

    <form action="{{ route('compteurs.update', $compteur->id_compteur) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                <h2 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-2">⚡ Informations Techniques
                </h2>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Point de comptage (Nom/PDL)
                        *</label>
                    <input type="text" name="point_comptage"
                        value="{{ old('point_comptage', $compteur->point_comptage) }}" required
                        class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Numéro de série du
                        compteur</label>
                    <input type="text" name="numero_compteur"
                        value="{{ old('numero_compteur', $compteur->numero_compteur) }}"
                        class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Type de réseau *</label>
                        <select name="type_reseau" required
                            class="w-full rounded-lg border-slate-300 text-sm bg-slate-50 focus:ring-slate-900">
                            @foreach(['Électricité', 'Eau Potable', 'Gaz', 'Chauffage urbain'] as $reseau)
                            <option value="{{ $reseau }}"
                                {{ old('type_reseau', $compteur->type_reseau) == $reseau ? 'selected' : '' }}>
                                {{ $reseau }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Unité de mesure</label>
                        <select name="unite_mesure"
                            class="w-full rounded-lg border-slate-300 text-sm bg-slate-50 focus:ring-slate-900">
                            @foreach(['kWh', 'm3', 'L'] as $unite)
                            <option value="{{ $unite }}"
                                {{ old('unite_mesure', $compteur->unite_mesure) == $unite ? 'selected' : '' }}>
                                {{ $unite }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-2 mt-4 bg-slate-50 p-3 rounded-lg border border-slate-100">
                    <input type="checkbox" name="dessert_tout_le_batiment" id="dessert_tout" value="1"
                        {{ old('dessert_tout_le_batiment', $compteur->dessert_tout_le_batiment) ? 'checked' : '' }}
                        class="rounded text-slate-900 focus:ring-slate-900">
                    <label for="dessert_tout" class="text-sm font-semibold text-slate-700">Ce compteur dessert tout le
                        bâtiment</label>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                <h2 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-2">📍 Localisation &
                    Rattachement</h2>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Local physique *</label>
                    <select name="id_local" required
                        class="w-full rounded-lg border-slate-300 text-sm bg-slate-50 focus:ring-slate-900">
                        @foreach($locaux as $local)
                        <option value="{{ $local->id_local }}"
                            {{ old('id_local', $compteur->id_local) == $local->id_local ? 'selected' : '' }}>
                            {{ $local->nom_local }} ({{ $local->batiment->nom_bat ?? 'Bât. inconnu' }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Localisation
                        vanne/disjoncteur</label>
                    <input type="text" name="localisation_vanne_arret"
                        value="{{ old('localisation_vanne_arret', $compteur->localisation_vanne_arret) }}"
                        class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Contrat de fourniture
                        lié</label>
                    <select name="id_contrat"
                        class="w-full rounded-lg border-slate-300 text-sm bg-slate-50 focus:ring-slate-900">
                        <option value="">-- Aucun contrat lié --</option>
                        @foreach($contrats as $contrat)
                        <option value="{{ $contrat->id_contrat }}"
                            {{ old('id_contrat', $compteur->id_contrat) == $contrat->id_contrat ? 'selected' : '' }}>
                            N° {{ $contrat->numero_contrat }} ({{ $contrat->tiers->raison_sociale ?? 'Fournisseur' }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Sous-compteur de :</label>
                    <select name="id_compteur_principal"
                        class="w-full rounded-lg border-slate-300 text-sm bg-slate-50 focus:ring-slate-900">
                        <option value="">-- C'est un compteur principal --</option>
                        @foreach($compteursPrincipaux as $cp)
                        <option value="{{ $cp->id_compteur }}"
                            {{ old('id_compteur_principal', $compteur->id_compteur_principal) == $cp->id_compteur ? 'selected' : '' }}>
                            {{ $cp->point_comptage }} ({{ $cp->type_reseau }})
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="md:col-span-2 bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Date de pose</label>
                        <input type="date" name="date_pose" value="{{ old('date_pose', $compteur->date_pose) }}"
                            class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Date de dépose /
                            arrêt</label>
                        <input type="date" name="date_arret" value="{{ old('date_arret', $compteur->date_arret) }}"
                            class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Observations générales</label>
                    <textarea name="observations" rows="2"
                        class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900">{{ old('observations', $compteur->observations) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-between items-center pt-4">
            @if(auth()->user()->can('check-permission', ['Patrimoine', 'suppression']))
            <button type="button"
                onclick="if(confirm('Supprimer ce compteur ?')) document.getElementById('delete-form').submit();"
                class="px-4 py-2 bg-red-50 text-red-600 font-bold rounded-lg hover:bg-red-100 transition">
                🗑️ Supprimer
            </button>
            @else
            <div></div>
            @endif

            <button type="submit"
                class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-md transition">
                💾 Enregistrer les modifications
            </button>
        </div>
    </form>

    <form id="delete-form" action="{{ route('compteurs.destroy', $compteur->id_compteur) }}" method="POST"
        class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection