@extends('layouts.app')

@section('header_title', 'Ajouter une Voie')

@section('content')
    <div class="max-w-5xl mx-auto">
        <form action="{{ route('voies.store') }}" method="POST"
            class="bg-white p-8 rounded-xl border border-slate-200 shadow-sm space-y-8">
            @csrf

            <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                <span class="text-3xl">导</span>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Nouvelle Voie (Fiche Patrimoniale Complet)</h2>
                    <p class="text-sm text-slate-500">Saisie exhaustive des données juridiques, géométriques et historiques.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-slate-800 uppercase bg-slate-50 p-2 rounded">1. Identification &
                        Nomenclature</h3>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Nom de la voie <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nom_voie" required maxlength="100" value="{{ old('nom_voie') }}"
                            class="w-full border-slate-300 rounded-lg focus:ring-blue-500">
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Numéro Actuel</label>
                            <input type="text" name="numero_voie" maxlength="10" value="{{ old('numero_voie') }}"
                                placeholder="Ex: VC01" class="w-full border-slate-300 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Ancien Numéro</label>
                            <input type="text" name="ancien_numero" maxlength="20" value="{{ old('ancien_numero') }}"
                                class="w-full border-slate-300 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">N° Provisoire</label>
                            <input type="text" name="num_provisoire" maxlength="20" value="{{ old('num_provisoire') }}"
                                class="w-full border-slate-300 rounded-lg text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Catégorie</label>
                            <input type="text" name="categorie_voie" maxlength="80" value="{{ old('categorie_voie') }}"
                                placeholder="Ex: Voie Communale" class="w-full border-slate-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Statut Juridique</label>
                            <input type="text" name="statut_juridique" maxlength="50" value="{{ old('statut_juridique') }}"
                                placeholder="Ex: Domaine Public" class="w-full border-slate-300 rounded-lg">
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-slate-800 uppercase bg-slate-50 p-2 rounded">2. Données Topographiques
                    </h3>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Long. Réelle (ml)</label>
                            <input type="number" name="longueur_reelle_ml" value="{{ old('longueur_reelle_ml') }}"
                                class="w-full border-slate-300 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Long. Classée (ml)</label>
                            <input type="number" name="longueur_classee_ml" value="{{ old('longueur_classee_ml') }}"
                                class="w-full border-slate-300 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Larg. Moyenne (m)</label>
                            <input type="number" step="0.01" name="largeur_moyenne_m" value="{{ old('largeur_moyenne_m') }}"
                                class="w-full border-slate-300 rounded-lg text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Point Origine</label>
                            <input type="text" name="point_origine" maxlength="50" value="{{ old('point_origine') }}"
                                placeholder="Ex: Intersection RD903" class="w-full border-slate-300 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Point Extrémité</label>
                            <input type="text" name="point_extremite" maxlength="100" value="{{ old('point_extremite') }}"
                                placeholder="Ex: Limite commune" class="w-full border-slate-300 rounded-lg text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Définition du tracé</label>
                        <textarea name="definition_trace" rows="1" class="w-full border-slate-300 rounded-lg text-sm"
                            placeholder="Coordonnées, repères physiques..."></textarea>
                    </div>
                </div>

                <div class="space-y-4 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <h3 class="text-sm font-bold text-slate-800 uppercase bg-slate-50 p-2 rounded">3. Aspects Fonciers,
                            Historiques & Suivi</h3>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Conformité Cadastrale</label>
                        <input type="text" name="conformite_cadastrale" maxlength="100"
                            value="{{ old('conformite_cadastrale') }}" class="w-full border-slate-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Observations sur le Statut</label>
                        <textarea name="observations_statut" rows="2"
                            class="w-full border-slate-300 rounded-lg text-sm"></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 mb-1">Historique d'incorporation</label>
                        <textarea name="historique_incorporation" rows="2"
                            class="w-full border-slate-300 rounded-lg text-sm"
                            placeholder="Délibérations du conseil municipal, arrêtés préfectoraux..."></textarea>
                    </div>
                </div>

                <div class="space-y-4 md:col-span-2">
                    <h3 class="text-sm font-bold text-slate-800 uppercase bg-slate-50 p-2 rounded">4. Attrait & Intérêt
                        Territorial</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                        <div class="pt-2">
                            <label
                                class="flex items-center gap-3 cursor-pointer p-4 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 transition">
                                <input type="checkbox" name="est_pdipr" value="1" {{ old('est_pdipr') ? 'checked' : '' }}
                                    class="rounded border-slate-300 text-green-600 focus:ring-green-500 w-5 h-5">
                                <div>
                                    <span class="text-sm font-bold text-slate-700 block">Inscrit au PDIPR</span>
                                    <span class="text-[10px] text-slate-500 block">Plan Départemental des Itinéraires de
                                        Promenade</span>
                                </div>
                            </label>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-slate-700 mb-1">Intérêt Touristique /
                                Remarques</label>
                            <textarea name="interet_touristique" rows="2" class="w-full border-slate-300 rounded-lg text-sm"
                                placeholder="Points de vue, descriptif rando..."></textarea>
                        </div>
                    </div>
                </div>

            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('voies.index') }}"
                    class="px-5 py-2.5 border border-slate-300 text-slate-700 font-bold rounded-lg hover:bg-slate-50 transition">Annuler</a>
                <button type="submit"
                    class="px-5 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition">💾
                    Enregistrer la fiche complète</button>
            </div>
        </form>
    </div>
@endsection