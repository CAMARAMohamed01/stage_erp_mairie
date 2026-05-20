@extends('layouts.app')

@section('header_title', 'Modifier le Lieu : ' . $lieu->nom_lieu)

@section('content')
    <div class="max-w-4xl mx-auto pb-12">

        <div class="mb-6 flex justify-between items-end">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Modification du Lieu</h1>
                <p class="text-sm text-slate-500 mt-1">Mise à jour des informations pour {{ $lieu->nom_lieu }}.</p>
            </div>
            <a href="{{ route('lieux.show', $lieu->id_lieu) }}"
                class="text-sm font-semibold text-slate-600 hover:text-slate-900">← Annuler</a>
        </div>

        <form action="{{ route('lieux.update', $lieu->id_lieu) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">📋 Identité de l'espace
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nom du Lieu
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="nom_lieu" value="{{ old('nom_lieu', $lieu->nom_lieu) }}" required
                            class="w-full rounded-lg border-slate-300 focus:ring-slate-900 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Typologie /
                            Catégorie</label>
                        <input type="text" name="typologie_lieu" value="{{ old('typologie_lieu', $lieu->typologie_lieu) }}"
                            class="w-full rounded-lg border-slate-300 focus:ring-slate-900 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Surface
                            (m²)</label>
                        <input type="number" step="0.01" name="surface_m2"
                            value="{{ old('surface_m2', $lieu->surface_m2) }}"
                            class="w-full rounded-lg border-slate-300 focus:ring-slate-900 text-sm">
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">🕒 Horaires</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label
                            class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Ouverture</label>
                        <input type="time" name="horaire_ouverture"
                            value="{{ old('horaire_ouverture', $lieu->horaire_ouverture ? \Carbon\Carbon::parse($lieu->horaire_ouverture)->format('H:i') : '') }}"
                            class="w-full rounded-lg border-slate-300 text-sm">
                    </div>
                    <div>
                        <label
                            class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Fermeture</label>
                        <input type="time" name="horaire_fermeture"
                            value="{{ old('horaire_fermeture', $lieu->horaire_fermeture ? \Carbon\Carbon::parse($lieu->horaire_fermeture)->format('H:i') : '') }}"
                            class="w-full rounded-lg border-slate-300 text-sm">
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">📍 Cadastre et Rattachement
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Parcelle
                            Cadastrale <span class="text-red-500">*</span></label>
                        <select name="id_parcelle" required class="w-full rounded-lg border-slate-300 text-sm bg-slate-50">
                            @foreach($parcelles as $p)
                                <option value="{{ $p->id_parcelle }}" {{ $lieu->id_parcelle == $p->id_parcelle ? 'selected' : '' }}>Section
                                    {{ $p->section_cadastrale }} - N° {{ $p->num_parcelle }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Bâtiment
                                hôte</label>
                            <select name="id_batiment" class="w-full rounded-lg border-slate-300 text-sm">
                                <option value="">-- Aucun --</option>
                                @foreach($batiments as $bat)
                                    <option value="{{ $bat->id_batiment }}" {{ $lieu->id_batiment == $bat->id_batiment ? 'selected' : '' }}>{{ $bat->nom_bat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Classification
                                ERP</label>
                            <select name="id_type_erp" class="w-full rounded-lg border-slate-300 text-sm">
                                <option value="">-- Non classé --</option>
                                @foreach($types_erp as $erp)
                                    <option value="{{ $erp->id_type_erp }}" {{ $lieu->id_type_erp == $erp->id_type_erp ? 'selected' : '' }}>Cat.
                                        {{ $erp->categorie_erp }} - Type {{ $erp->type_erp }}
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
                        <select name="id_immo" class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900">
                            <option value="">-- Aucune --</option>
                            @foreach($immos as $immo)
                                <option value="{{ $immo->id_immo }}" {{ $lieu->id_immo == $immo->id_immo ? 'selected' : '' }}>
                                    {{ $immo->num_inventaire }} ({{ $immo->libelle_comptable }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Arrêté ou
                            Décision Réglementaire</label>
                        <select name="id_decision_reglement"
                            class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900">
                            <option value="">-- Aucune décision liée --</option>
                            @foreach($decisions as $dec)
                                <option value="{{ $dec->id_decision }}" {{ $lieu->id_decision_reglement == $dec->id_decision ? 'selected' : '' }}>
                                    {{ $dec->numero_decision }} ({{ \Carbon\Carbon::parse($dec->date_decision)->format('Y') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm mb-6">
                <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">🌿 Contrats & Prestations
                    associés</h2>

                <div>
                    <label for="id_contrats" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Contrats rattachés (Entretien espaces verts, Gardiennage, Assurance...)
                    </label>
                    <select name="id_contrats[]" id="id_contrats" multiple size="4"
                        class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 focus:ring-slate-900 text-sm">
                        @foreach($contrats as $c)
                            @php
                                $isSelected = isset($lieu) && $lieu->contratsAdministratifs->contains('id_contrat', $c->id_contrat);
                                $isOldSelected = is_array(old('id_contrats')) && in_array($c->id_contrat, old('id_contrats'));
                            @endphp
                            <option value="{{ $c->id_contrat }}" {{ ($isSelected || $isOldSelected) ? 'selected' : '' }}>
                                {{ $c->numero_contrat ?? 'Sans N°' }} - {{ $c->type_contrat }}
                            </option>
                        @endforeach
                    </select>
                    <span class="text-[10px] text-slate-500 mt-1 block">Maintenez la touche CTRL (ou CMD sur Mac) pour
                        sélectionner plusieurs lignes.</span>
                </div>
            </div>
            <div class="flex justify-end pt-4">
                <button type="submit"
                    class="px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-lg shadow-md transition">
                    💾 Sauvegarder les modifications
                </button>
            </div>
        </form>
    </div>
@endsection