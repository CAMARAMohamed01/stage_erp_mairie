@extends('layouts.app')

@section('header_title', 'Modifier le Local : ' . $local->nom_local)

@section('content')
    <div class="max-w-4xl mx-auto pb-12">

        <div class="mb-6 flex justify-between items-end">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Modification du Local</h1>
                <p class="text-sm text-slate-500 mt-1">Mise à jour des dimensions et de l'affectation de la pièce.</p>
            </div>
            <div class="flex items-center gap-3">
                @can('check-permission', ['Patrimoine & Equipements', 'suppression'])
                    <button type="button"
                        onclick="if(confirm('Supprimer définitivement ce local ?')) document.getElementById('delete-local-form').submit();"
                        class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold rounded-lg transition border border-red-200">
                        🗑️ Supprimer le local
                    </button>
                @endcan
                <a href="{{ route('locaux.show', $local->id_local) }}"
                    class="text-sm font-semibold text-slate-600 hover:text-slate-900">Annuler</a>
            </div>
        </div>

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 text-red-700 rounded-lg border border-red-200 text-sm font-semibold">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('locaux.update', $local->id_local) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">📋 Identité de la pièce
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Désignation /
                            Nom du local <span class="text-red-500">*</span></label>
                        <input type="text" name="nom_local" value="{{ old('nom_local', $local->nom_local) }}" required
                            class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Usage
                            Principal</label>
                        <select name="id_usage"
                            class="w-full rounded-lg border-slate-300 text-sm bg-slate-50 focus:ring-slate-900">
                            <option value="">-- Sélectionner l'usage --</option>
                            @foreach($usages as $usage)
                                <option value="{{ $usage->id_usage }}" {{ $local->id_usage == $usage->id_usage ? 'selected' : '' }}>{{ $usage->libelle_usage }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Statut
                            d'occupation</label>
                        <select name="statut_occupation"
                            class="w-full rounded-lg border-slate-300 text-sm bg-slate-50 focus:ring-slate-900">
                            <option value="">-- Sélectionner --</option>
                            <option value="Occupé" {{ $local->statut_occupation === 'Occupé' ? 'selected' : '' }}>Occupé
                            </option>
                            <option value="Inoccupé" {{ $local->statut_occupation === 'Inoccupé' ? 'selected' : '' }}>
                                Inoccupé</option>
                            <option value="En travaux" {{ $local->statut_occupation === 'En travaux' ? 'selected' : '' }}>En
                                travaux</option>
                            <option value="Loué" {{ $local->statut_occupation === 'Loué' ? 'selected' : '' }}>Loué à un
                                tiers</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">📏 Dimensions</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Longueur
                            (m)</label>
                        <input type="number" step="0.01" id="input_longueur" name="longueur"
                            value="{{ old('longueur', $local->longueur) }}"
                            class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Largeur
                            (m)</label>
                        <input type="number" step="0.01" id="input_largeur" name="largeur"
                            value="{{ old('largeur', $local->largeur) }}"
                            class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Surface Totale
                            (m²)</label>
                        <input type="number" step="0.01" id="input_surface" name="surface_m2"
                            value="{{ old('surface_m2', $local->surface_m2) }}"
                            class="w-full rounded-lg border-slate-300 bg-slate-100 text-sm font-mono focus:ring-slate-900">
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">📍 Rattachement
                    Géographique</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Bâtiment
                            hôte</label>
                        <select name="id_batiment"
                            class="w-full rounded-lg border-slate-300 text-sm bg-slate-50 focus:ring-slate-900">
                            <option value="">-- Aucun bâtiment --</option>
                            @foreach($batiments as $bat)
                                <option value="{{ $bat->id_batiment }}" {{ $local->id_batiment == $bat->id_batiment ? 'selected' : '' }}>🏢 {{ $bat->nom_bat }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Niveau /
                            Étage</label>
                        <input type="text" name="niveau" value="{{ old('niveau', $local->niveau) }}"
                            class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900">
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Ou Lieu Public (Cas
                        particulier)</label>
                    <select name="id_lieu" class="w-full rounded-lg border-slate-300 text-sm bg-white focus:ring-slate-900">
                        <option value="">-- Aucun lieu public --</option>
                        @foreach($lieux as $lieu)
                            <option value="{{ $lieu->id_lieu }}" {{ $local->id_lieu == $lieu->id_lieu ? 'selected' : '' }}>🌳
                                {{ $lieu->nom_lieu }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">🛡️ Assurance & Notes</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Réf. Article
                            Assurance</label>
                        <input type="text" name="ref_article_assurance"
                            value="{{ old('ref_article_assurance', $local->ref_article_assurance) }}"
                            class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Prime Assurance
                            TTC (€)</label>
                        <input type="number" step="0.01" name="prime_assurance_ttc"
                            value="{{ old('prime_assurance_ttc', $local->prime_assurance_ttc) }}"
                            class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Remarques /
                            Observations</label>
                        <textarea name="remarque" rows="2"
                            class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900">{{ old('remarque', $local->remarque) }}</textarea>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm mb-6">
                <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">📄 Contrats associés</h2>

                <div>
                    <label for="id_contrats" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Lier à un ou plusieurs contrats (Assurance, Entretien, etc.)
                    </label>
                    <select name="id_contrats[]" id="id_contrats" multiple size="4"
                        class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-slate-50 focus:ring-slate-900 text-sm">
                        @foreach($contrats as $c)
                            @php
                                $isSelected = isset($local) && $local->contratsAdministratifs->contains(
                                    'id_contrat',
                                    $c->id_contrat
                                );
                                $isOldSelected = is_array(old('id_contrats')) && in_array($c->id_contrat, old('id_contrats'));
                            @endphp
                            <option value="{{ $c->id_contrat }}" {{ ($isSelected || $isOldSelected) ? 'selected' : '' }}>
                                {{ $c->numero_contrat ?? 'Sans N°' }} - {{ $c->type_contrat }}
                            </option>
                        @endforeach
                    </select>
                    <span class="text-[10px] text-slate-500 mt-1 block">Maintenez CTRL (ou CMD sur Mac) pour sélectionner
                        plusieurs contrats.</span>
                </div>
            </div>
            <div class="flex justify-end pt-4">
                <button type="submit"
                    class="px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-lg shadow-md transition">
                    💾 Enregistrer les modifications
                </button>
            </div>
        </form>

        <form id="delete-local-form" action="{{ route('locaux.destroy', $local->id_local) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const inputLongueur = document.getElementById('input_longueur');
            const inputLargeur = document.getElementById('input_largeur');
            const inputSurface = document.getElementById('input_surface');

            function calculerSurface() {
                const l = parseFloat(inputLongueur.value);
                const w = parseFloat(inputLargeur.value);
                if (!isNaN(l) && !isNaN(w)) {
                    inputSurface.value = (l * w).toFixed(2);
                }
            }
            inputLongueur.addEventListener('input', calculerSurface);
            inputLargeur.addEventListener('input', calculerSurface);
        });
    </script>
@endsection