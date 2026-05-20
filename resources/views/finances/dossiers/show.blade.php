@extends('layouts.app')

@section('title', 'Dossier Financier DOS-' . str_pad($dossier->id_dossier, 4, '0', STR_PAD_LEFT))

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">

        <div
            class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-wrap justify-between items-start gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <span class="text-3xl">💳</span>
                    <h1 class="text-2xl font-bold text-slate-800">Dossier
                        DOS-{{ str_pad($dossier->id_dossier, 4, '0', STR_PAD_LEFT) }}</h1>
                    <span
                        class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-blue-100 text-blue-700 uppercase tracking-wider">
                        {{ $dossier->statut_actuel }}
                    </span>
                </div>
                <p class="text-slate-500 font-medium ml-11">{{ $dossier->objet_dossier }}</p>
            </div>
            <div>
                <button onclick="history.back()"
                    class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50">←
                    Retour</button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">📄 Ventilation / Détail des
                            ventilations financières</h3>
                        @php
                            $totalHT = $lignes->sum('montant_ht');
                            $totalTTC = $lignes->sum('montant_ttc');
                        @endphp
                    </div>

                    <div class="divide-y divide-slate-100 bg-white">
                        @forelse($lignes as $ligne)
                            <div class="p-4 flex justify-between items-center text-sm hover:bg-slate-50">
                                <div>
                                    <p class="font-semibold text-slate-800">{{ $ligne->designation_ligne }}</p>
                                    <p class="text-xs text-slate-400">
                                        Imputation Budget ID: <span
                                            class="font-medium text-slate-600">#{{ $ligne->id_budget }}</span>
                                        | Charge : <span
                                            class="font-medium text-slate-600">{{ $ligne->nature_charge ?? 'N/A' }}</span>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="font-bold text-slate-900">{{ number_format($ligne->montant_ttc, 2, ',', ' ') }}
                                        € TTC</span>
                                    <p class="text-xs text-slate-400">HT : {{ number_format($ligne->montant_ht, 2, ',', ' ') }}
                                        €</p>
                                </div>
                            </div>
                        @empty
                            <p class="p-6 text-center text-sm text-slate-400 italic">Aucun montant ventilé sur ce dossier pour
                                le moment.</p>
                        @endforelse
                    </div>

                    @if(auth()->user()->can('check-permission', ['Finances & Achats', 'ecriture']))
                        <div class="p-4 bg-slate-50 border-t border-slate-200">
                            <span class="text-xs font-bold uppercase text-slate-400 tracking-wider block mb-3">➕ Ajouter un
                                article / une ligne comptable</span>
                            <form action="{{ route('dossiers-financiers.ligne.store', $dossier->id_dossier) }}" method="POST"
                                class="space-y-3">
                                @csrf
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div class="sm:col-span-2">
                                        <input type="text" name="designation_ligne" required
                                            placeholder="Désignation de la prestation ou marchandise"
                                            class="w-full text-xs border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <input type="text" name="nature_charge"
                                            placeholder="Nature charge (Ex: Fournitures, MO)"
                                            class="w-full text-xs border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <input type="number" step="0.01" name="montant_ht" id="montant_ht" required
                                            placeholder="Montant HT (€)"
                                            class="w-full text-xs border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <input type="number" step="0.01" name="montant_tva" id="montant_tva" required
                                            placeholder="Montant TVA (€)"
                                            class="w-full text-xs border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <input type="number" step="0.01" name="montant_ttc" id="montant_ttc" required
                                            placeholder="Montant TTC (€)"
                                            class="w-full text-xs border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                    </div>

                                    <div class="sm:col-span-2">
                                        <select name="id_budget" required
                                            class="w-full text-xs border border-slate-300 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-blue-500">
                                            <option value="">-- Enveloppe Budgétaire d'affectation * --</option>
                                            @foreach($budgets as $b)
                                                <option value="{{ $b->id_budget }}">Exercice {{ $b->annee_exercice }} — Enveloppe
                                                    #{{ $b->id_budget }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <select name="id_operation"
                                            class="w-full text-xs border border-slate-300 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-blue-500">
                                            <option value="">-- Opération comptable --</option>
                                            @foreach($operations as $o)
                                                <option value="{{ $o->id_operation }}">{{ $o->numero_operation }} -
                                                    {{ $o->libelle_operation }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="flex justify-end pt-2">
                                    <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2 rounded-lg shadow transition">
                                        Ventiler le montant
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2">💶 Totalisation
                    </h3>
                    <div class="space-y-3">
                        <div class="p-3 bg-slate-50 rounded-lg flex justify-between text-sm">
                            <span class="text-slate-500">Total HT</span>
                            <span class="font-semibold text-slate-800">{{ number_format($totalHT, 2, ',', ' ') }} €</span>
                        </div>
                        <div class="p-4 bg-blue-50 border border-blue-100 rounded-lg flex justify-between items-center">
                            <span class="text-xs font-bold text-blue-700 uppercase">Total TTC</span>
                            <span class="text-xl font-extrabold text-blue-900">{{ number_format($totalTTC, 2, ',', ' ') }}
                                €</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2">📋 Références
                        Administratif</h3>
                    <ul class="text-xs space-y-3 font-mono text-slate-700">
                        <li><span class="text-slate-400 block font-sans text-[10px] uppercase">Titulaire (Tiers)</span>
                            <span
                                class="font-sans font-bold text-sm text-slate-800">{{ $dossier->tiers->nom_affiche ?? 'N/A' }}</span>
                        </li>
                        <li class="border-t pt-2"><span class="text-slate-400 block font-sans">N° Devis :</span>
                            {{ $dossier->numero_devis ?? '-' }}</li>
                        <li><span class="text-slate-400 block font-sans">N° Bon de commande :</span>
                            {{ $dossier->numero_bon_commande ?? '-' }}</li>
                        <li><span class="text-slate-400 block font-sans">N° Bon de livraison :</span>
                            {{ $dossier->numero_bon_livraison ?? '-' }}</li>
                        <li><span class="text-slate-400 block font-sans">N° Facture :</span>
                            {{ $dossier->numero_facture ?? '-' }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Script d'aide pour pré-calculer le TTC automatiquement (TTC = HT + TVA) --}}
    <script>
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
    </script>
@endsection