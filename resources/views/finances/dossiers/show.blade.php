@extends('layouts.app')

@section('title', 'Dossier Financier DOS-' . str_pad($dossier->id_dossier_f, 4, '0', STR_PAD_LEFT))

@section('content')
@php
// Détermination de la nature du dossier pour l'affichage conditionnel des dates et pièces
$isRecette = $dossier->numero_titre_recette || $dossier->date_constatation_recette;
@endphp

<div class="max-w-6xl mx-auto space-y-6 pb-12">

    <div
        class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-l-4 {{ !$isRecette ? 'border-l-blue-600' : 'border-l-emerald-600' }}">
        <div>
            <div class="flex flex-wrap items-center gap-3 mb-1">
                <span class="text-3xl drop-shadow-sm">{{ !$isRecette ? '📉' : '📈' }}</span>
                <h1 class="text-2xl font-bold text-slate-800">
                    Dossier DOS-{{ str_pad($dossier->id_dossier_f, 4, '0', STR_PAD_LEFT) }}
                </h1>

                @can('check-permission', ['Finances & Achats', 'ecriture'])
                <form action="{{ route('dossiers-financiers.statut.update', $dossier->id_dossier_f) }}" method="POST"
                    class="inline-flex items-center">
                    @csrf
                    @method('PATCH')
                    <select name="statut_actuel" onchange="this.form.submit()"
                        class="text-xs bg-slate-100 border border-slate-200 font-bold text-slate-700 px-2.5 py-1 rounded-full cursor-pointer focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500/20 transition">
                        @if(!$isRecette)
                        <option value="Devis demandé"
                            {{ $dossier->statut_actuel == 'Devis demandé' ? 'selected' : '' }}>Devis demandé</option>
                        <option value="Devis reçu" {{ $dossier->statut_actuel == 'Devis reçu' ? 'selected' : '' }}>Devis
                            reçu</option>
                        <option value="Bon de commande émis"
                            {{ $dossier->statut_actuel == 'Bon de commande émis' ? 'selected' : '' }}>Bon de commande
                            émis</option>
                        <option value="Facture reçue"
                            {{ $dossier->statut_actuel == 'Facture reçue' ? 'selected' : '' }}>Facture reçue / En
                            attente</option>
                        <option value="Transmis Trésorerie"
                            {{ $dossier->statut_actuel == 'Transmis Trésorerie' ? 'selected' : '' }}>Transmis Trésorerie
                        </option>
                        <option value="Payé" {{ $dossier->statut_actuel == 'Payé' ? 'selected' : '' }}>Payé / Clôturé
                        </option>
                        @else
                        <option value="Droits constatés"
                            {{ $dossier->statut_actuel == 'Droits constatés' ? 'selected' : '' }}>Droits constatés
                        </option>
                        <option value="Titre émis" {{ $dossier->statut_actuel == 'Titre émis' ? 'selected' : '' }}>Titre
                            émis</option>
                        <option value="Transmis Trésorerie"
                            {{ $dossier->statut_actuel == 'Transmis Trésorerie' ? 'selected' : '' }}>Transmis Trésorerie
                        </option>
                        <option value="Payé" {{ $dossier->statut_actuel == 'Payé' ? 'selected' : '' }}>Encaissé / Soldé
                        </option>
                        @endif
                    </select>
                </form>
                @else
                @php
                $badgeColor = match ($dossier->statut_actuel) {
                'Payé', 'Encaissé / Soldé' => 'bg-green-50 text-green-700 border-green-100',
                'Annulé' => 'bg-red-50 text-red-700 border-red-100',
                default => 'bg-blue-50 text-blue-700 border-blue-100'
                };
                @endphp
                <span
                    class="px-2.5 py-1 text-[10px] font-bold rounded-full border {{ $badgeColor }} uppercase tracking-wider">
                    {{ $dossier->statut_actuel }}
                </span>
                @endcan
            </div>
            <p class="text-sm text-slate-500 font-medium ml-1">{{ $dossier->objet_dossier }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <a href="{{ route('dossiers-financiers.index') }}"
                class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 shadow-sm transition w-full md:w-auto text-center">
                ← Registre
            </a>

            @can('check-permission', ['Finances & Achats', 'ecriture'])
            <a href="{{ route('dossiers-financiers.edit', $dossier->id_dossier_f) }}"
                class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-bold transition shadow-sm w-full md:w-auto text-center">
                ✏️ Modifier
            </a>
            @endcan

            @can('check-permission', ['Finances & Achats', 'suppression'])
            <form action="{{ route('dossiers-financiers.destroy', $dossier->id_dossier_f) }}" method="POST"
                onsubmit="return confirm('⚠️ Attention ! La suppression de ce dossier supprimera également toutes les ventilations comptables rattachées. Confirmer ?');"
                class="w-full md:w-auto">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="w-full px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-lg text-sm font-bold transition text-center">
                    🗑️ Supprimer
                </button>
            </form>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-200">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">📄 Articles comptables imputés
                        (Ventilation)</h3>
                    @php
                    $totalHT = $dossier->lignes->sum('montant_ht');
                    $totalTTC = $dossier->lignes->sum('montant_ttc');
                    @endphp
                </div>

                <div class="divide-y divide-slate-100 bg-white">
                    @forelse($dossier->lignes as $ligne)
                    <div class="p-4 flex justify-between items-center text-sm hover:bg-slate-50 transition">
                        <div class="space-y-0.5 pr-4">
                            <p class="font-bold text-slate-900">{{ $ligne->designation_ligne }}</p>
                            <p class="text-xs text-slate-400">
                                Enveloppe : <span class="font-medium text-slate-700">#{{ $ligne->id_budget }}</span>
                                @if($ligne->operationComptable)
                                | Opération : <span
                                    class="font-mono text-blue-600">[{{ $ligne->operationComptable->numero_operation }}]</span>
                                @endif
                                | Nature : <span
                                    class="text-slate-600 font-medium">{{ $ligne->nature_charge ?? 'Non spécifié' }}</span>
                            </p>
                        </div>
                        <div class="text-right flex items-center gap-4 flex-shrink-0">
                            <div>
                                <span
                                    class="font-bold text-slate-900 block">{{ number_format($ligne->montant_ttc, 2, ',', ' ') }}
                                    € TTC</span>
                                <p class="text-xs text-slate-400 font-normal">HT :
                                    {{ number_format($ligne->montant_ht, 2, ',', ' ') }} €</p>
                            </div>

                            @can('check-permission', ['Finances & Achats', 'suppression'])
                            <form
                                action="{{ route('dossiers-financiers.lignes.destroy', [$dossier->id_dossier_f, $ligne->id_ligne]) }}"
                                method="POST"
                                onsubmit="return confirm('⚠️ Retirer cette ligne comptable de la facture ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-slate-400 hover:text-red-600 font-bold p-1 text-sm transition"
                                    title="Supprimer la ligne">
                                    🗑️
                                </button>
                            </form>
                            @endcan
                        </div>
                    </div>
                    @empty
                    <p class="p-8 text-center text-sm text-slate-400 italic">Aucune ventilation budgétaire n'a encore
                        été saisie sur ce dossier.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="space-y-6">

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b pb-2">💵 Totalisation</h3>
                <div class="space-y-2 text-xs">
                    <div class="p-2.5 bg-slate-50 rounded-lg flex justify-between font-medium">
                        <span class="text-slate-400">Total net HT</span>
                        <span class="text-slate-800 font-bold">{{ number_format($totalHT, 2, ',', ' ') }} €</span>
                    </div>
                    <div
                        class="p-3 bg-blue-50 border border-blue-100 rounded-lg flex justify-between items-center font-bold">
                        <span class="text-blue-700 uppercase tracking-wider text-[10px]">Total TTC engagé</span>
                        <span class="text-xl text-blue-900 font-black">{{ number_format($totalTTC, 2, ',', ' ') }}
                            €</span>
                    </div>
                </div>
            </div>

            @can('check-permission', ['Finances & Achats', 'ecriture'])
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b pb-2">➕ Imputer une ligne
                    comptable</h3>
                <form action="{{ route('dossiers-financiers.lignes.store', $dossier->id_dossier_f) }}" method="POST"
                    class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-600 mb-1">Désignation de l'article *</label>
                        <input type="text" name="designation_ligne" required
                            placeholder="Ex: Achat enrobé à froid ou prestation"
                            class="w-full border rounded-lg p-2 bg-slate-50 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500/20">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-bold text-slate-600 mb-1">Montant HT (€) *</label>
                            <input type="number" step="0.01" name="montant_ht" id="montant_ht" required
                                placeholder="0.00"
                                class="w-full border rounded-lg p-2 bg-slate-50 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500/20">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 mb-1">Montant TVA (€) *</label>
                            <input type="number" step="0.01" name="montant_tva" id="montant_tva" required
                                placeholder="0.00"
                                class="w-full border rounded-lg p-2 bg-slate-50 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500/20">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-600 mb-1">Montant TTC calculé (€)</label>
                        <input type="number" step="0.01" name="montant_ttc" id="montant_ttc" required readonly
                            placeholder="0.00"
                            class="w-full border rounded-lg p-2 bg-slate-100 font-bold text-slate-700 outline-none cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-600 mb-1">Nature de charge / Compte</label>
                        <input type="text" name="nature_charge" placeholder="Ex: Fournitures, Maintenance"
                            class="w-full border rounded-lg p-2 bg-slate-50 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500/20">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-600 mb-1">Enveloppe Budgétaire Affectée *</label>
                        <select name="id_budget" required
                            class="w-full border rounded-lg p-2 bg-white outline-none focus:ring-2 focus:ring-blue-500/20">
                            <option value="">-- Choisir une enveloppe annuelle --</option>
                            @foreach($budgets as $b)
                            <option value="{{ $b->id_budget }}">Exercice {{ $b->annee_exercice }} — Enveloppe
                                #{{ $b->id_budget }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-600 mb-1">Opération Comptable</label>
                        <select name="id_operation"
                            class="w-full border rounded-lg p-2 bg-white outline-none focus:ring-2 focus:ring-blue-500/20">
                            <option value="">-- Optionnel : Choix écriture --</option>
                            @foreach($operations as $o)
                            <option value="{{ $o->id_operation }}">{{ $o->numero_operation }} -
                                {{ $o->libelle_operation }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-lg shadow-sm transition mt-2">
                        💾 Valider l'imputation
                    </button>
                </form>
            </div>
            @endcan

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b pb-2">📋 Suivi &
                    Références</h3>
                <ul class="text-xs space-y-3 font-mono text-slate-700">
                    <li>
                        <span class="text-slate-400 block font-sans text-[10px] font-bold uppercase">Créancier / Tiers
                            rattaché</span>
                        <span class="font-sans font-black text-sm text-slate-800">
                            @if($dossier->tiers)
                            👤
                            {{ $dossier->tiers->type_tiers === 'Physique' ? $dossier->tiers->physique?->prenom_tiers . ' ' . $dossier->tiers->physique?->nom_tiers : $dossier->tiers->morale?->raison_sociale }}
                            @else
                            <span class="text-slate-400 italic font-normal font-sans">Aucun tiers associé</span>
                            @endif
                        </span>
                    </li>
                    <li class="border-t pt-2"><span class="text-slate-400 block font-sans font-medium text-[11px]">N°
                            Devis :</span> {{ $dossier->numero_devis ?? '—' }}</li>
                    <li><span class="text-slate-400 block font-sans font-medium text-[11px]">N° Bon de commande :</span>
                        {{ $dossier->numero_bon_commande ?? '—' }}</li>
                    <li><span class="text-slate-400 block font-sans font-medium text-[11px]">N° Bon de livraison
                            :</span> {{ $dossier->numero_bon_livraison ?? '—' }}</li>
                    <li><span class="text-slate-400 block font-sans font-medium text-[11px]">N° Engagement :</span>
                        {{ $dossier->numero_engagement ?? '—' }}</li>
                    <li><span class="text-slate-400 block font-sans font-medium text-[11px]">N° Facture :</span>
                        {{ $dossier->numero_facture ?? '—' }}</li>
                    @if($dossier->numero_titre_recette)
                    <li class="border-t pt-2 text-emerald-700 font-bold">
                        <span class="text-slate-400 block font-sans font-medium text-[11px]">N° Titre de recette (Régie)
                            :</span> {{ $dossier->numero_titre_recette }}
                    </li>
                    @endif
                </ul>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b pb-2">📅 Calendrier &
                    Jalons</h3>
                <div class="text-xs space-y-2.5 font-medium text-slate-600">
                    @if(!$isRecette)
                    <div class="flex justify-between items-center p-1.5 rounded bg-slate-50/60">
                        <span>📄 Réception du devis :</span>
                        <strong
                            class="text-slate-800 font-mono">{{ $dossier->date_reception_devis ? \Carbon\Carbon::parse($dossier->date_reception_devis)->format('d/m/Y') : '—' }}</strong>
                    </div>
                    <div class="flex justify-between items-center p-1.5 rounded bg-slate-50/60">
                        <span>✍️ Signature engagement :</span>
                        <strong
                            class="text-slate-800 font-mono">{{ $dossier->date_signature_engagement ? \Carbon\Carbon::parse($dossier->date_signature_engagement)->format('d/m/Y') : '—' }}</strong>
                    </div>
                    <div class="flex justify-between items-center p-1.5 rounded bg-slate-50/60">
                        <span>📦 Réception bon livraison :</span>
                        <strong
                            class="text-slate-800 font-mono">{{ $dossier->date_bon_livraison ? \Carbon\Carbon::parse($dossier->date_bon_livraison)->format('d/m/Y') : '—' }}</strong>
                    </div>
                    <div
                        class="flex justify-between items-center p-1.5 rounded bg-slate-50/60 border-l-2 border-l-blue-500 pl-2">
                        <span>✅ Date de service fait :</span>
                        <strong
                            class="text-slate-800 font-mono">{{ $dossier->date_service_fait ? \Carbon\Carbon::parse($dossier->date_service_fait)->format('d/m/Y') : '—' }}</strong>
                    </div>
                    <div class="flex justify-between items-center p-1.5 rounded bg-slate-50/60">
                        <span>📥 Réception de facture :</span>
                        <strong
                            class="text-slate-800 font-mono">{{ $dossier->date_reception_facture ? \Carbon\Carbon::parse($dossier->date_reception_facture)->format('d/m/Y') : '—' }}</strong>
                    </div>
                    <div
                        class="flex justify-between items-center p-1.5 rounded bg-slate-50/60 border-l-2 border-l-amber-500 pl-2">
                        <span>🏛️ Trans. Trésorerie (Compta) :</span>
                        <strong
                            class="text-slate-800 font-mono">{{ $dossier->date_transmission_compta ? \Carbon\Carbon::parse($dossier->date_transmission_compta)->format('d/m/Y') : '—' }}</strong>
                    </div>
                    @else
                    <div
                        class="flex justify-between items-center p-1.5 rounded bg-slate-50/60 border-l-2 border-l-emerald-500 pl-2">
                        <span>📊 Constatation de recette :</span>
                        <strong
                            class="text-slate-800 font-mono">{{ $dossier->date_constatation_recette ? \Carbon\Carbon::parse($dossier->date_constatation_recette)->format('d/m/Y') : '—' }}</strong>
                    </div>
                    <div class="flex justify-between items-center p-1.5 rounded bg-slate-50/60">
                        <span>📜 Émission du titre officiel :</span>
                        <strong
                            class="text-slate-800 font-mono">{{ $dossier->date_emission_titre ? \Carbon\Carbon::parse($dossier->date_emission_titre)->format('d/m/Y') : '—' }}</strong>
                    </div>
                    <div
                        class="flex justify-between items-center p-1.5 rounded bg-slate-50/60 border-l-2 border-l-green-500 pl-2">
                        <span>💰 Date d'encaissement régie :</span>
                        <strong
                            class="text-slate-800 font-mono">{{ $dossier->date_encaissement ? \Carbon\Carbon::parse($dossier->date_encaissement)->format('d/m/Y') : '—' }}</strong>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ht = document.getElementById('montant_ht');
    const tva = document.getElementById('montant_tva');
    const ttc = document.getElementById('montant_ttc');

    if (ht && tva && ttc) {
        const calcTtc = () => {
            const valHt = parseFloat(ht.value) || 0;
            const valTva = parseFloat(tva.value) || 0;
            ttc.value = (valHt + valTva).toFixed(2);
        };
        ht.addEventListener('input', calcTtc);
        tva.addEventListener('input', calcTtc);
    }
});
</script>
@endsection