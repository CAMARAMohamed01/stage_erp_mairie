@extends('layouts.app')

@section('header_title', 'Nouvel Acte Administratif')

@section('content')
<div class="max-w-3xl mx-auto pb-12">

    <form action="{{ route('decisions-admin.store') }}" method="POST"
        class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        @csrf

        <!-- En-tête formulaire -->
        <div class="p-6 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
            <div>
                <h1 class="text-lg font-bold text-slate-900">Enregistrement d'un acte officiel</h1>
                <p class="text-xs text-slate-500 mt-0.5">Veuillez remplir les références juridiques de la décision
                    municipale.</p>
            </div>
            <a href="{{ route('decisions-admin.index') }}"
                class="px-3 py-1.5 border rounded-lg text-xs font-semibold text-slate-600 hover:bg-white transition">
                Annuler
            </a>
        </div>

        <!-- Corps du formulaire -->
        <div class="p-6 space-y-5 text-sm">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Numéro de décision -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Numéro de décision / Arrêté
                        *</label>
                    <input type="text" name="numero_decision" required value="{{ old('numero_decision') }}"
                        placeholder="Ex: ARR_2026_042"
                        class="w-full border border-slate-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                    @error('numero_decision') <p class="text-xs text-red-600 font-medium mt-1">⚠️ {{ $message }}</p>
                    @enderror
                </div>

                <!-- Date de décision -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Date de signature / Prise
                        d'acte *</label>
                    <input type="date" name="date_decision" required value="{{ old('date_decision', date('Y-m-d')) }}"
                        class="w-full border border-slate-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Type de décision -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Type d'acte</label>
                    <select name="type_decision"
                        class="w-full border border-slate-300 rounded-lg p-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                        <option value="Arrêté municipal">Arrêté municipal</option>
                        <option value="Délibération">Délibération du Conseil</option>
                        <option value="Décision du Maire">Décision du Maire</option>
                        <option value="Autre acte réglementaire">Autre acte réglementaire</option>
                    </select>
                </div>

                <!-- Rédacteur -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Agent / Élu rédacteur</label>
                    <select name="id_user_redacteur"
                        class="w-full border border-slate-300 rounded-lg p-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                        <option value="">-- Sélectionner l'agent --</option>
                        @foreach($agents as $agent)
                        <option value="{{ $agent->id_user }}"
                            {{ old('id_user_redacteur') == $agent->id_user ? 'selected' : '' }}>
                            {{ $agent->nom_user }} {{ $agent->prenom_user }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Intitulé -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Intitulé court de la décision
                    *</label>
                <input type="text" name="intitule_decision" required value="{{ old('intitule_decision') }}"
                    placeholder="Ex: Réglementation du stationnement temporaire Rue de la Mairie"
                    class="w-full border border-slate-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>

            <!-- Corps / Objet -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Corps textuel de l'acte / Résumé
                    des articles</label>
                <textarea name="objet_decision" rows="5"
                    placeholder="Saisir ici les attendus ou l'objet précis des travaux / décisions..."
                    class="w-full border border-slate-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500/20 font-serif text-sm">{{ old('objet_decision') }}</textarea>
            </div>

            <!-- Télétransmission -->
            <div class="pt-2">
                <label class="relative flex items-center gap-3 cursor-pointer select-none">
                    <input type="checkbox" name="teletransmission_prefecture" value="1"
                        {{ old('teletransmission_prefecture') ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500/20">
                    <div>
                        <span class="text-xs font-bold text-slate-800 uppercase block">Télétransmission en Préfecture
                            effectuée</span>
                        <span class="text-[11px] text-slate-400 font-medium">Cocher la case si le fichier a déjà été
                            envoyé via l'interface @Actes.</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Pied de formulaire / Validation -->
        <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-2">
            <button type="submit"
                class="px-5 py-2 bg-blue-700 hover:bg-blue-700 text-white font-bold text-xs rounded-lg transition shadow-sm">
                💾 Enregistrer l'acte officiel
            </button>
        </div>
    </form>
</div>
@endsection