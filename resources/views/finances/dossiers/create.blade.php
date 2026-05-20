@extends('layouts.app')

@section('title', 'Nouveau Dossier Financier')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-xl border border-slate-200 shadow-sm">
        <div class="mb-6 border-b border-slate-100 pb-4">
            <h1 class="text-2xl font-bold text-slate-800">💳 Initialiser un Dossier Financier</h1>
            <p class="text-sm text-slate-500">Créez un dossier pour suivre le règlement d'une facture ou l'engagement d'une
                dépense.</p>
        </div>

        <form action="{{ route('dossiers-financiers.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <h3 class="text-sm font-bold text-blue-600 uppercase tracking-wider mb-3">1. Identification & Acteurs</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Objet de la dépense / recette *</label>
                        <input type="text" name="objet_dossier" required value="{{ old('objet_dossier') }}"
                            placeholder="Ex: Remplacement chaudière Gymnase"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tiers (Prestataire / Fournisseur)
                            *</label>
                        <select name="id_tiers" required
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm bg-white outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Sélectionner le tiers --</option>
                            @foreach($tiers as $t)
                                <option value="{{ $t->id_tiers }}" {{ old('id_tiers') == $t->id_tiers ? 'selected' : '' }}>
                                    {{ $t->raison_sociale ?? ($t->nom_tiers . ' ' . $t->prenom_tiers) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Contrat cadre associé
                            (Optionnel)</label>
                        <select name="id_contrat"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm bg-white outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Hors contrat --</option>
                            @foreach($contrats as $c)
                                <option value="{{ $c->id_contrat }}" {{ old('id_contrat') == $c->id_contrat ? 'selected' : '' }}>
                                    {{ $c->numero_contrat }} ({{ $c->type_contrat }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <h3 class="text-sm font-bold text-blue-600 uppercase tracking-wider mb-3">2. Suivi de l'avancement</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Statut actuel *</label>
                        <select name="statut_actuel" required
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm bg-white outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="Devis demandé">Devis demandé</option>
                            <option value="Devis reçu">Devis reçu</option>
                            <option value="Bon de commande émis">Bon de commande émis</option>
                            <option value="Facture reçue">Facture reçue / En attente de paiement</option>
                            <option value="Transmis Trésorerie">Transmis Trésorerie (Mandat)</option>
                            <option value="Payé">Payé / Clôturé</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <h3 class="text-sm font-bold text-blue-600 uppercase tracking-wider mb-3">3. Références des pièces (À
                    remplir selon l'avancement)</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-slate-50 rounded-lg border border-slate-200">

                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">N° Devis</label>
                        <input type="text" name="numero_devis" value="{{ old('numero_devis') }}"
                            class="w-full border border-slate-300 rounded px-3 py-1.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Date réception Devis</label>
                        <input type="date" name="date_reception_devis" value="{{ old('date_reception_devis') }}"
                            class="w-full border border-slate-300 rounded px-3 py-1.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="hidden md:block"></div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">N° Bon de commande</label>
                        <input type="text" name="numero_bon_commande" value="{{ old('numero_bon_commande') }}"
                            class="w-full border border-slate-300 rounded px-3 py-1.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">N° Bon de livraison</label>
                        <input type="text" name="numero_bon_livraison" value="{{ old('numero_bon_livraison') }}"
                            class="w-full border border-slate-300 rounded px-3 py-1.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="hidden md:block"></div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">N° Facture</label>
                        <input type="text" name="numero_facture" value="{{ old('numero_facture') }}"
                            class="w-full border border-slate-300 rounded px-3 py-1.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Date réception Facture</label>
                        <input type="date" name="date_reception_facture" value="{{ old('date_reception_facture') }}"
                            class="w-full border border-slate-300 rounded px-3 py-1.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Date Trans. Trésorerie</label>
                        <input type="date" name="date_transmission_compta" value="{{ old('date_transmission_compta') }}"
                            class="w-full border border-slate-300 rounded px-3 py-1.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                </div>
            </div>

            <div class="flex justify-end pt-6 border-t border-slate-200 gap-3">
                <a href="{{ route('dossiers-financiers.index') }}"
                    class="px-6 py-2 text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg font-medium transition text-sm">
                    Annuler
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-md transition text-sm">
                    Créer le dossier financier
                </button>
            </div>
        </form>
    </div>
@endsection