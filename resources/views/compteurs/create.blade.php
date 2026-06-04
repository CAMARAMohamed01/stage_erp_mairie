@extends('layouts.app')

@section('header_title', 'Ajouter un compteur')

@section('content')
    <div class="max-w-4xl mx-auto pb-12">
        <div class="mb-6 flex justify-between items-end">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Nouveau Compteur</h1>
                <p class="text-sm text-slate-500 mt-1">Enregistrement d'un point de comptage (Eau, Élec, Gaz...).</p>
            </div>
            <a href="{{ route('compteurs.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">←
                Retour à la liste</a>
        </div>

        <form action="{{ route('compteurs.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                    <h2 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-2">⚡ Informations Techniques
                    </h2>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Point de comptage (Nom/PDL)
                            *</label>
                        <input type="text" name="point_comptage" required placeholder="Ex: PDL-123456789"
                            class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Numéro de série du
                            compteur</label>
                        <input type="text" name="numero_compteur" placeholder="Ex: CPT-999888"
                            class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Type de réseau *</label>
                            <select name="type_reseau" required
                                class="w-full rounded-lg border-slate-300 text-sm bg-slate-50 focus:ring-slate-900">
                                <option value="Électricité">Électricité</option>
                                <option value="Eau Potable">Eau Potable</option>
                                <option value="Gaz">Gaz</option>
                                <option value="Chauffage urbain">Chauffage urbain</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Unité de mesure</label>
                            <select name="unite_mesure"
                                class="w-full rounded-lg border-slate-300 text-sm bg-slate-50 focus:ring-slate-900">
                                <option value="kWh">kWh</option>
                                <option value="m3">m³</option>
                                <option value="L">Litres</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 mt-4 bg-slate-50 p-3 rounded-lg border border-slate-100">
                        <input type="checkbox" name="dessert_tout_le_batiment" id="dessert_tout" value="1"
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
                            <option value="">-- Choisir un local --</option>
                            @foreach($locaux as $local)
                                <option value="{{ $local->id_local }}">
                                    {{ $local->nom_local }} ({{ $local->batiment->nom_bat ?? 'Bât. inconnu' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Localisation de la vanne
                            d'arrêt / disjoncteur</label>
                        <input type="text" name="localisation_vanne_arret"
                            placeholder="Ex: Dans le couloir au RDC, 2ème porte"
                            class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Contrat de fourniture
                            lié</label>
                        <select name="id_contrat"
                            class="w-full rounded-lg border-slate-300 text-sm bg-slate-50 focus:ring-slate-900">
                            <option value="">-- Aucun contrat (ou à définir) --</option>
                            @foreach($contrats as $contrat)
                                <option value="{{ $contrat->id_contrat }}">
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
                                <option value="{{ $cp->id_compteur }}">{{ $cp->point_comptage }} ({{ $cp->type_reseau }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="md:col-span-2 bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Date de pose</label>
                            <input type="date" name="date_pose"
                                class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Date de dépose /
                                arrêt</label>
                            <input type="date" name="date_arret"
                                class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Observations générales</label>
                        <textarea name="observations" rows="2"
                            class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900"></textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit"
                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-md transition">
                    💾 Enregistrer le compteur
                </button>
            </div>
        </form>
    </div>
@endsection