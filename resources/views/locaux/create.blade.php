@extends('layouts.app')

@section('header_title', 'Ajouter un Local / Pièce')

@section('content')
    <div class="max-w-4xl mx-auto pb-12">

        <div class="mb-6 flex justify-between items-end">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Nouveau Local</h1>
                <p class="text-sm text-slate-500 mt-1">Saisie d'une pièce, d'un bureau ou d'un espace au sein du patrimoine.
                </p>
            </div>
            <a href="{{ route('locaux.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
                ← Annuler
            </a>
        </div>

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 text-red-700 rounded-lg border border-red-200 text-sm font-semibold">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('locaux.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">📋 Identité de la pièce
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Désignation /
                            Nom du local <span class="text-red-500">*</span></label>
                        <input type="text" name="nom_local" required
                            placeholder="Ex: Bureau du Maire, Salle de Classe CM2..."
                            class="w-full rounded-lg border-slate-300 focus:ring-slate-900 focus:border-slate-900 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Usage
                            Principal</label>
                        <select name="id_usage"
                            class="w-full rounded-lg border-slate-300 focus:ring-slate-900 focus:border-slate-900 text-sm bg-slate-50">
                            <option value="">-- Sélectionner l'usage --</option>
                            @foreach($usages as $usage)
                                <option value="{{ $usage->id_usage }}">{{ $usage->libelle_usage }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Statut
                            d'occupation</label>
                        <select name="statut_occupation"
                            class="w-full rounded-lg border-slate-300 focus:ring-slate-900 focus:border-slate-900 text-sm bg-slate-50">
                            <option value="">-- Sélectionner --</option>
                            <option value="Occupé">Occupé</option>
                            <option value="Inoccupé">Inoccupé</option>
                            <option value="En travaux">En travaux</option>
                            <option value="Loué">Loué à un tiers</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-2">
                    <h2 class="text-sm font-bold text-slate-800">📏 Dimensions</h2>
                    <span class="text-xs text-slate-400 italic">La surface se calcule automatiquement</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Longueur
                            (m)</label>
                        <input type="number" step="0.01" id="input_longueur" name="longueur" placeholder="Ex: 5.50"
                            class="w-full rounded-lg border-slate-300 focus:ring-slate-900 focus:border-slate-900 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Largeur
                            (m)</label>
                        <input type="number" step="0.01" id="input_largeur" name="largeur" placeholder="Ex: 4.00"
                            class="w-full rounded-lg border-slate-300 focus:ring-slate-900 focus:border-slate-900 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Surface Totale
                            (m²)</label>
                        <input type="number" step="0.01" id="input_surface" name="surface_m2"
                            placeholder="Calcul automatique"
                            class="w-full rounded-lg border-slate-300 bg-slate-100 text-sm font-mono focus:ring-slate-900 focus:border-slate-900">
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">📍 Rattachement
                    Géographique</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Bâtiment
                            hôte</label>
                        <select name="id_batiment"
                            class="w-full rounded-lg border-slate-300 focus:ring-slate-900 focus:border-slate-900 text-sm bg-slate-50">
                            <option value="">-- Aucun bâtiment --</option>
                            @foreach($batiments as $bat)
                                <option value="{{ $bat->id_batiment }}">🏢 {{ $bat->nom_bat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Niveau /
                            Étage</label>
                        <input type="text" name="niveau" placeholder="Ex: RDC, 1er Étage, Sous-sol..."
                            class="w-full rounded-lg border-slate-300 focus:ring-slate-900 focus:border-slate-900 text-sm">
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Ou Lieu Public (Cas
                        particulier)</label>
                    <p class="text-[10px] text-slate-500 mb-2">À remplir uniquement si le local ne se trouve pas dans un
                        bâtiment (ex: vestiaire de foot, buvette de parc).</p>
                    <select name="id_lieu"
                        class="w-full rounded-lg border-slate-300 focus:ring-slate-900 focus:border-slate-900 text-sm bg-white">
                        <option value="">-- Aucun lieu public --</option>
                        @foreach($lieux as $lieu)
                            <option value="{{ $lieu->id_lieu }}">🌳 {{ $lieu->nom_lieu }}</option>
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
                            class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Prime Assurance
                            TTC (€)</label>
                        <input type="number" step="0.01" name="prime_assurance_ttc"
                            class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Remarques /
                            Observations</label>
                        <textarea name="remarque" rows="2"
                            class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900"></textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit"
                    class="px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-lg shadow-md transition">
                    💾 Sauvegarder le local
                </button>
            </div>
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
                    // Arrondi à 2 décimales
                    const surface = (l * w).toFixed(2);
                    inputSurface.value = surface;
                }
            }

            inputLongueur.addEventListener('input', calculerSurface);
            inputLargeur.addEventListener('input', calculerSurface);
        });
    </script>
@endsection