@extends('layouts.app')

@section('header_title', 'Ajouter un Lieu / Espace Public')

@section('content')
    <div class="max-w-4xl mx-auto pb-12">

        <div class="mb-6 flex justify-between items-end">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Nouvel Espace Public</h1>
                <p class="text-sm text-slate-500 mt-1">Référencement d'un parc, terrain de sport, place ou cimetière.</p>
            </div>
            <a href="{{ route('lieux.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
                ← Annuler
            </a>
        </div>

        @if($errors->any())
            <div class="mb-4 p-4 bg-red-50 text-red-700 rounded-lg border border-red-200 text-sm font-semibold">
                Veuillez vérifier les informations saisies.
            </div>
        @endif

        <form action="{{ route('lieux.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">🌳 Identité de l'espace
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nom du Lieu
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="nom_lieu" required
                            placeholder="Ex: Parc des Capucins, Place de la Mairie..."
                            class="w-full rounded-lg border-slate-300 focus:ring-slate-900 focus:border-slate-900 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Typologie /
                            Catégorie</label>
                        <input type="text" name="typologie_lieu" placeholder="Ex: Espace Vert, Équipement Sportif..."
                            class="w-full rounded-lg border-slate-300 focus:ring-slate-900 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Surface Totale
                            (m²)</label>
                        <input type="number" step="0.01" name="surface_m2"
                            class="w-full rounded-lg border-slate-300 focus:ring-slate-900 text-sm font-mono">
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">🕒 Modalités d'accès au
                    public</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Heure
                            d'Ouverture</label>
                        <input type="time" name="horaire_ouverture"
                            class="w-full rounded-lg border-slate-300 focus:ring-slate-900 text-sm text-slate-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Heure de
                            Fermeture</label>
                        <input type="time" name="horaire_fermeture"
                            class="w-full rounded-lg border-slate-300 focus:ring-slate-900 text-sm text-slate-600">
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">📍 Cadastre & Rattachement
                    Physique</h2>

                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Parcelle
                            Cadastrale Principale <span class="text-red-500">*</span></label>
                        <select name="id_parcelle" required
                            class="w-full rounded-lg border-slate-300 focus:ring-slate-900 text-sm bg-slate-50">
                            <option value="">-- Obligatoire --</option>
                            @foreach($parcelles as $parcelle)
                                <option value="{{ $parcelle->id_parcelle }}">Section {{ $parcelle->section_cadastrale }} - N°
                                    {{ $parcelle->num_parcelle }} ({{ $parcelle->nom_lieu_dit }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Bâtiment
                                Rattaché (Optionnel)</label>
                            <select name="id_batiment"
                                class="w-full rounded-lg border-slate-300 focus:ring-slate-900 text-sm bg-white">
                                <option value="">-- Aucun --</option>
                                @foreach($batiments as $bat)
                                    <option value="{{ $bat->id_batiment }}">{{ $bat->nom_bat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label
                                class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Classification
                                ERP (Optionnel)</label>
                            <select name="id_type_erp"
                                class="w-full rounded-lg border-slate-300 focus:ring-slate-900 text-sm bg-white">
                                <option value="">-- Non classé ERP --</option>
                                @foreach($types_erp as $erp)
                                    <option value="{{ $erp->id_type_erp }}">Cat. {{ $erp->categorie_erp }} - Type
                                        {{ $erp->type_erp }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">💼 Comptabilité &
                    Règlementation</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Immobilisation
                            Comptable</label>
                        <select name="id_immo"
                            class="w-full rounded-lg border-slate-300 focus:ring-slate-900 text-sm bg-white">
                            <option value="">-- Aucune --</option>
                            @foreach($immos as $immo)
                                <option value="{{ $immo->id_immo }}">{{ $immo->num_inventaire }}
                                    ({{ $immo->libelle_comptable }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Arrêté ou
                            Décision Réglementaire</label>
                        <select name="id_decision_reglement"
                            class="w-full rounded-lg border-slate-300 focus:ring-slate-900 text-sm bg-white">
                            <option value="">-- Aucune décision liée --</option>
                            @foreach($decisions as $dec)
                                <option value="{{ $dec->id_decision }}">{{ $dec->numero_decision }}
                                    ({{ \Carbon\Carbon::parse($dec->date_decision)->format('Y') }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit"
                    class="px-6 py-3 bg-green-700 hover:bg-green-600 text-white font-bold rounded-lg shadow-md transition">
                    💾 Valider la création du Lieu
                </button>
            </div>
        </form>
    </div>
@endsection