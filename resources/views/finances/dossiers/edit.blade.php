@extends('layouts.app')

@section('title', 'Modifier le Dossier Financier')

@section('content')
@php
// On détermine dynamiquement s'il s'agit d'une recette ou d'une dépense pour adapter l'affichage
$isRecette = $dossier->numero_titre_recette || $dossier->date_constatation_recette;
@endphp

<div class="max-w-4xl mx-auto bg-white p-8 rounded-xl border border-slate-200 shadow-sm pb-12">

    <div class="mb-6 border-b border-slate-100 pb-4 flex items-center gap-4">
        <div
            class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shadow-sm {{ !$isRecette ? 'bg-blue-50 text-blue-600' : 'bg-emerald-50 text-emerald-600' }}">
            {{ !$isRecette ? '📉' : '📈' }}
        </div>
        <div>
            <h1 class="text-xl font-bold text-slate-900">
                Modifier le Dossier de {{ !$isRecette ? 'Dépense' : 'Recette' }} :
                DOS-{{ str_pad($dossier->id_dossier_f, 4, '0', STR_PAD_LEFT) }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Mise à jour des informations générales et des références de pièces jointes.
            </p>
        </div>
    </div>

    <form action="{{ route('dossiers-financiers.update', $dossier->id_dossier_f) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
            <h3
                class="text-xs font-bold {{ !$isRecette ? 'text-blue-600' : 'text-emerald-600' }} uppercase tracking-wider mb-3">
                1. Identification & Acteurs</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="md:col-span-2">
                    <label class="block font-semibold text-slate-700 mb-1">Objet du dossier financier *</label>
                    <input type="text" name="objet_dossier" required
                        value="{{ old('objet_dossier', $dossier->objet_dossier) }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-slate-400">
                </div>

                <div>
                    <label
                        class="block font-semibold text-slate-700 mb-1">{{ !$isRecette ? 'Tiers (Fournisseur) *' : 'Tiers (Redevable) *' }}</label>
                    <select name="id_tiers" required
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-xs bg-white outline-none focus:ring-2 focus:ring-blue-500/20">
                        <option value="">-- Sélectionner le tiers --</option>
                        @foreach($tiers as $t)
                        <option value="{{ $t->id_tiers }}"
                            {{ old('id_tiers', $dossier->id_tiers) == $t->id_tiers ? 'selected' : '' }}>
                            {{ $t->raison_sociale ?? ($t->nom_tiers . ' ' . $t->prenom_tiers) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Contrat de marché public lié
                        (Optionnel)</label>
                    <select name="id_contrat"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-xs bg-white outline-none focus:ring-2 focus:ring-blue-500/20">
                        <option value="">-- Hors contrat cadre --</option>
                        @foreach($contrats as $c)
                        <option value="{{ $c->id_contrat }}"
                            {{ old('id_contrat', $dossier->id_contrat) == $c->id_contrat ? 'selected' : '' }}>
                            {{ $c->numero_contrat }} ({{ $c->type_contrat }})
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100">
            <h3
                class="text-xs font-bold {{ !$isRecette ? 'text-blue-600' : 'text-emerald-600' }} uppercase tracking-wider mb-3">
                2. Suivi de l'avancement</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Statut actuel *</label>
                    <select name="statut_actuel" required
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-xs bg-white outline-none focus:ring-2 focus:ring-blue-500/20">
                        @if(!$isRecette)
                        <option value="Devis demandé"
                            {{ old('statut_actuel', $dossier->statut_actuel) === 'Devis demandé' ? 'selected' : '' }}>
                            Devis demandé</option>
                        <option value="Devis reçu"
                            {{ old('statut_actuel', $dossier->statut_actuel) === 'Devis reçu' ? 'selected' : '' }}>Devis
                            reçu</option>
                        <option value="Bon de commande émis"
                            {{ old('statut_actuel', $dossier->statut_actuel) === 'Bon de commande émis' ? 'selected' : '' }}>
                            Bon de commande émis</option>
                        <option value="Facture reçue"
                            {{ old('statut_actuel', $dossier->statut_actuel) === 'Facture reçue' ? 'selected' : '' }}>
                            Facture reçue / En attente de paiement</option>
                        <option value="Transmis Trésorerie"
                            {{ old('statut_actuel', $dossier->statut_actuel) === 'Transmis Trésorerie' ? 'selected' : '' }}>
                            Transmis Trésorerie (Ordonnancé)</option>
                        <option value="Payé"
                            {{ old('statut_actuel', $dossier->statut_actuel) === 'Payé' ? 'selected' : '' }}>Payé /
                            Clôturé</option>
                        @else
                        <option value="Droits constatés"
                            {{ old('statut_actuel', $dossier->statut_actuel) === 'Droits constatés' ? 'selected' : '' }}>
                            Droits constatés</option>
                        <option value="Titre émis"
                            {{ old('statut_actuel', $dossier->statut_actuel) === 'Titre émis' ? 'selected' : '' }}>Titre
                            de recette émis</option>
                        <option value="Transmis Trésorerie"
                            {{ old('statut_actuel', $dossier->statut_actuel) === 'Transmis Trésorerie' ? 'selected' : '' }}>
                            Transmis Trésorerie (Prise en charge)</option>
                        <option value="Payé"
                            {{ old('statut_actuel', $dossier->statut_actuel) === 'Payé' ? 'selected' : '' }}>Encaissé /
                            Soldé</option>
                        @endif
                    </select>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100">
            <h3
                class="text-xs font-bold {{ !$isRecette ? 'text-blue-600' : 'text-emerald-600' }} uppercase tracking-wider mb-3">
                3. Références réglementaires des pièces
            </h3>

            @if(!$isRecette)
            <div
                class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-slate-50 border rounded-xl text-xs font-medium text-slate-700">
                <div>
                    <label class="block text-slate-500 mb-1">N° Devis Référence</label>
                    <input type="text" name="numero_devis" value="{{ old('numero_devis', $dossier->numero_devis) }}"
                        class="w-full border rounded p-1.5 bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-slate-500 mb-1">Date réception Devis</label>
                    <input type="date" name="date_reception_devis"
                        value="{{ old('date_reception_devis', $dossier->date_reception_devis ? $dossier->date_reception_devis->format('Y-m-d') : '') }}"
                        class="w-full border rounded p-1.5 bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-slate-500 mb-1">N° Engagement Comptable</label>
                    <input type="text" name="numero_engagement"
                        value="{{ old('numero_engagement', $dossier->numero_engagement) }}"
                        class="w-full border rounded p-1.5 bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-slate-500 mb-1">N° Bon de Commande</label>
                    <input type="text" name="numero_bon_commande"
                        value="{{ old('numero_bon_commande', $dossier->numero_bon_commande) }}"
                        class="w-full border rounded p-1.5 bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-slate-500 mb-1">N° Bon de Livraison</label>
                    <input type="text" name="numero_bon_livraison"
                        value="{{ old('numero_bon_livraison', $dossier->numero_bon_livraison) }}"
                        class="w-full border rounded p-1.5 bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-slate-500 mb-1">Date Service Fait</label>
                    <input type="date" name="date_service_fait"
                        value="{{ old('date_service_fait', $dossier->date_service_fait ? $dossier->date_service_fait->format('Y-m-d') : '') }}"
                        class="w-full border rounded p-1.5 bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-slate-500 mb-1">N° Facture</label>
                    <input type="text" name="numero_facture"
                        value="{{ old('numero_facture', $dossier->numero_facture) }}"
                        class="w-full border rounded p-1.5 bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-slate-500 mb-1">Date réception Facture</label>
                    <input type="date" name="date_reception_facture"
                        value="{{ old('date_reception_facture', $dossier->date_reception_facture ? $dossier->date_reception_facture->format('Y-m-d') : '') }}"
                        class="w-full border rounded p-1.5 bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-slate-500 mb-1">Date Transmission Compta</label>
                    <input type="date" name="date_transmission_compta"
                        value="{{ old('date_transmission_compta', $dossier->date_transmission_compta ? $dossier->date_transmission_compta->format('Y-m-d') : '') }}"
                        class="w-full border rounded p-1.5 bg-white focus:outline-none">
                </div>
            </div>
            @else
            <div
                class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-slate-50 border rounded-xl text-xs font-medium text-slate-700">
                <div>
                    <label class="block text-slate-500 mb-1">N° Titre de recette (Régie)</label>
                    <input type="text" name="numero_titre_recette"
                        value="{{ old('numero_titre_recette', $dossier->numero_titre_recette) }}"
                        class="w-full border rounded p-1.5 bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-slate-500 mb-1">Date de constatation de la recette</label>
                    <input type="date" name="date_constatation_recette"
                        value="{{ old('date_constatation_recette', $dossier->date_constatation_recette ? $dossier->date_constatation_recette->format('Y-m-d') : '') }}"
                        class="w-full border rounded p-1.5 bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-slate-500 mb-1">Date d'émission du titre officiel</label>
                    <input type="date" name="date_emission_titre"
                        value="{{ old('date_emission_titre', $dossier->date_emission_titre ? $dossier->date_emission_titre->format('Y-m-d') : '') }}"
                        class="w-full border rounded p-1.5 bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-slate-500 mb-1">Date d'encaissement effectif</label>
                    <input type="date" name="date_encaissement"
                        value="{{ old('date_encaissement', $dossier->date_encaissement ? $dossier->date_encaissement->format('Y-m-d') : '') }}"
                        class="w-full border rounded p-1.5 bg-white focus:outline-none">
                </div>
            </div>
            @endif
        </div>

        <div class="flex justify-end pt-6 border-t border-slate-200 gap-3">
            <a href="{{ route('dossiers-financiers.show', $dossier->id_dossier_f) }}"
                class="px-5 py-2 text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg font-bold transition text-xs">
                Annuler
            </a>
            <button type="submit"
                class="px-5 py-2 {{ !$isRecette ? 'bg-blue-600 hover:bg-blue-700' : 'bg-emerald-600 hover:bg-emerald-700' }} text-white font-bold rounded-lg shadow-sm transition text-xs">
                💾 Sauvegarder les modifications
            </button>
        </div>
    </form>
</div>
@endsection