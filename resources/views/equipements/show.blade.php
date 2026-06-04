@extends('layouts.app')

@section('header_title', 'Fiche Équipement - ' . $equipement->nom_equipement)

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12">

    {{-- BARRE DE TITRE & ACTIONS --}}
    <div
        class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-wrap justify-between items-center gap-4">
        <div class="flex items-center gap-4">
            <div
                class="w-14 h-14 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center text-2xl shadow-sm select-none">
                ⚙️
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $equipement->nom_equipement }}</h1>
                    <span
                        class="px-2.5 py-0.5 text-xs font-bold rounded-md bg-blue-50 text-blue-700 border border-blue-100 uppercase tracking-wide">
                        {{ $equipement->famille->libelle_famille ?? 'Non classé' }}
                    </span>
                </div>
                <p class="text-xs text-slate-400 font-bold mt-1 uppercase tracking-wider">
                    Réf. Série : <span
                        class="font-mono text-slate-600 normal-case">{{ $equipement->reference_serie ?? 'N/A' }}</span>
                    <span class="mx-2 text-slate-300">|</span> Marque : <span
                        class="text-slate-600 normal-case">{{ $equipement->marque ?? 'Non définie' }}</span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="history.back()"
                class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 transition shadow-sm">←
                Retour</button>

            @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                <a href="{{ route('equipements.edit', $equipement->id_equipement) }}"
                    class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg font-bold text-sm transition shadow-sm">✏️
                    Modifier</a>
            @endcan

            @can('check-permission', ['Patrimoine & Equipements', 'suppression'])
                <form action="{{ route('equipements.destroy', $equipement->id_equipement) }}" method="POST"
                    onsubmit="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer cet équipement ?');">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-bold text-sm transition shadow-sm">🗑️
                        Supprimer</button>
                </form>
            @endcan
        </div>
    </div>

    {{-- CORP DE LA FICHE TECHNIQUE --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- COLONNE GAUCHE & CENTRE : CARACTÉRISTIQUES ET SOUS-ÉLÉMENTS --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- 1. INFORMATIONS GENERALES --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 border-b pb-2">📋
                    Caractéristiques Générales</h3>

                @if($equipement->id_parent)
                    <div class="mb-6 bg-slate-50 border border-slate-200 p-4 rounded-xl flex items-center gap-3">
                        <span class="text-xl select-none">🔗</span>
                        <div>
                            <p class="text-[10px] uppercase font-black text-slate-400 tracking-wider">Sous-élément technique
                                subordonné à :</p>
                            <a href="{{ route('equipements.show', $equipement->id_parent) }}"
                                class="text-sm font-extrabold text-blue-600 hover:text-blue-800 hover:underline block mt-0.5">
                                {{ $equipement->equipementParent->nom_equipement ?? 'Équipement parent' }}
                            </a>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-6 text-sm">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Date de mise en service</p>
                        <p class="font-bold text-slate-700 mt-1">
                            {{ $equipement->date_achat ? \Carbon\Carbon::parse($equipement->date_achat)->format('d/m/Y') : 'Non renseignée' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Localisation / Pièce</p>
                        <p class="font-bold text-slate-700 mt-1">
                            @if($equipement->local)
                                🏢 {{ $equipement->local->nom_local }}
                                @if($equipement->local->id_batiment && $equipement->local->id_batiment != null)
                                    <span
                                        class="text-xs text-slate-400 font-medium font-italic">({{ $equipement->local->nom_local }})</span>
                                @endif
                            @elseif($equipement->id_lieu || $equipement->lieuPublic)
                                🌳 {{ $equipement->lieuPublic->nom_lieu ?? 'Espace ouvert' }}
                            @else
                                <span class="text-slate-400 font-medium italic">Non affecté</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Teinte / Repère Visuel</p>
                        <p class="font-bold text-slate-700 mt-1">{{ $equipement->couleur ?? 'Standard' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">État Opérationnel actuel</p>
                        <div class="mt-1">
                            <span
                                class="px-2.5 py-1 text-[10px] font-black uppercase tracking-wider rounded-md {{ in_array(strtolower($equipement->etat_fonctionnement), ['opérationnel', 'operationnel', 'en service']) ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200' }}">
                                {{ $equipement->etat_fonctionnement ?? 'Inconnu' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. LISTE ARBORESCENTE DES SOUS-EQUIPEMENTS --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">🧩 Composants &
                        Sous-Équipements liés ({{ $equipement->sousEquipements->count() }})</h3>
                    @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                        <a href="{{ route('equipements.create', ['id_parent' => $equipement->id_equipement]) }}"
                            class="text-[11px] font-bold bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 shadow-sm transition">
                            + Rattacher un sous-composant
                        </a>
                    @endcan
                </div>
                <div class="divide-y divide-slate-100 max-h-64 overflow-y-auto bg-white">
                    @forelse($equipement->sousEquipements as $sousEq)
                        <div class="p-4 flex justify-between items-center hover:bg-slate-50/60 transition">
                            <div>
                                <p class="text-sm font-bold text-slate-800">{{ $sousEq->nom_equipement }}</p>
                                <p class="text-[11px] text-slate-400 font-medium mt-0.5">
                                    N° Série : {{ $sousEq->reference_serie ?? 'N/A' }} <span class="mx-1.5">•</span> État :
                                    <span
                                        class="font-bold {{ in_array(strtolower($sousEq->etat_fonctionnement), ['opérationnel', 'operationnel', 'en service']) ? 'text-green-600' : 'text-red-600' }}">{{ $sousEq->etat_fonctionnement ?? 'Opérationnel' }}</span>
                                </p>
                            </div>
                            <a href="{{ route('equipements.show', $sousEq->id_equipement) }}"
                                class="text-xs text-blue-600 font-bold hover:text-blue-800 transition bg-slate-50 border px-2.5 py-1 rounded shadow-sm">
                                Consulter →
                            </a>
                        </div>
                    @empty
                        <p class="p-8 text-center text-sm text-slate-400 italic">Aucun sous-composant ou ramification
                            technique rattaché à cet équipement.</p>
                    @endforelse
                </div>
            </div>

            {{-- 3. HISTORIQUE DES INTERVENTIONS GMAO --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">🛠️ Historique Maintenance &
                        Pannes</h3>
                    <a href="{{ route('interventions.create', ['id_equipement' => $equipement->id_equipement]) }}"
                        class="text-[11px] font-bold bg-slate-900 text-white px-3 py-1.5 rounded-lg hover:bg-slate-800 shadow-sm transition">
                        🚨 Ouvrir un ticket d'incident
                    </a>
                </div>
                <div class="divide-y divide-slate-100 max-h-64 overflow-y-auto bg-white">
                    @forelse($equipement->interventions as $int)
                        <div class="p-4 flex justify-between items-center hover:bg-slate-50/60 transition">
                            <div>
                                <p class="text-sm font-bold text-slate-800">{{ $int->type_intervention }}</p>
                                <p class="text-xs text-slate-400 font-medium mt-0.5">Ouvert le
                                    {{ \Carbon\Carbon::parse($int->date_ouverture)->format('d/m/Y') }}
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span
                                    class="px-2 py-0.5 text-[10px] font-black uppercase tracking-wider rounded {{ $int->statut_global == 'Terminé' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $int->statut_global }}
                                </span>
                                <a href="{{ route('interventions.show', $int->id_int) }}"
                                    class="text-xs font-bold text-blue-600 hover:underline">Fiche →</a>
                            </div>
                        </div>
                    @empty
                        <p class="p-8 text-center text-sm text-slate-400 italic">Aucune opération de maintenance préventive
                            ou corrective enregistrée.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- COLONNE DROITE : CONTROLES, FINANCES ET PIÈCES JOINTES --}}
        <div class="space-y-6">

            {{-- CONTROLES REGLEMENTAIRES --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 border-b pb-2">🛡️ Contrôles
                    Réglementaires</h3>
                @forelse($equipement->controles as $c)
                <div class="mb-4 last:mb-0 p-3 bg-slate-50 border border-slate-100 rounded-lg">
                    <p class="text-xs font-bold text-slate-800 leading-snug">{{ $c->designation }}</p>
                    <p class="text-[11px] text-slate-400 font-semibold uppercase mt-0.5">Périodicité :
                        {{ $c->frequence_mois }} mois
                    </p>
                    <div class="mt-2 text-[11px] font-bold text-slate-600">
                        📅 Dernier examen :
                        {{ $c->pivot->date_controle ? \Carbon\Carbon::parse($c->pivot->date_controle)->format('d/m/Y') : 'À réaliser' }}
                    </div>
                </div>
                @endforeach
            </div>

            {{-- SUIVI COMPTABLE & FINANCIER --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 border-b pb-2">💼 Gestion
                    Financière & Garantie</h3>
                <div class="space-y-4 text-sm">
                    @php
                        $achat = $equipement->date_achat ? \Carbon\Carbon::parse($equipement->date_achat) : null;
                        $garantieMois = (int) ($equipement->duree_garantie_mois ?? 0);
                        $finGarantie = ($achat && $garantieMois > 0) ? $achat->copy()->addMonths($garantieMois) : null;
                        $isExpired = $finGarantie ? now()->greaterThan($finGarantie) : true;
                    @endphp

                    <div>
                        <span class="text-xs text-slate-400 font-bold uppercase tracking-wide block">Couverture Garantie
                            :</span>
                        <p class="font-bold text-slate-800 mt-0.5">
                            {{ $garantieMois > 0 ? $garantieMois . ' mois' : 'Aucune renseignée' }}
                        </p>
                        @if($finGarantie)
                            <p class="text-xs mt-1 font-bold {{ $isExpired ? 'text-red-600' : 'text-green-600' }}">
                                {{ $isExpired ? '❌ Expirée le ' : '✅ Valide jusqu\'au ' }}
                                {{ $finGarantie->format('d/m/Y') }}
                            </p>
                        @endif
                    </div>

                    @if($equipement->id_immo)
                        <div class="pt-4 border-t border-slate-100">
                            <span class="text-xs text-slate-400 font-bold uppercase tracking-wide block">Valeur
                                d'acquisition :</span>
                            <span class="font-black text-slate-900 text-lg block mt-0.5">
                                {{ number_format($equipement->immobilisation->valeur_achat ?? 0, 2, ',', ' ') }} €
                            </span>
                            <p class="text-[11px] font-mono text-slate-400 mt-1">N° Immatriculation Inv :
                                {{ $equipement->immobilisation->num_inventaire ?? 'N/A' }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- NOTICES ET PIECES JOINTES (LIÉES A LA VARIABLE CORRIGÉE $documents) --}}
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b pb-2">📂 Documentations &
                    Manuels</h3>

                <ul class="space-y-2">
                    {{-- 🎯 MODIFICATION ICI : Utilisation de la variable $documents passée par le contrôleur --}}
                    @forelse($documents as $doc)
                        <li class="flex items-center justify-between p-3 bg-slate-50 border border-slate-100 rounded-xl">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="text-xl select-none">
                                    {{ in_array(strtolower($doc->type_doc ?? ''), ['pdf']) ? '📄' : '🖼️' }}
                                </span>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-700 truncate">{{ $doc->nom_fichier }}</p>
                                    <p class="text-[10px] text-slate-400 font-medium mt-0.5">
                                        {{ number_format($doc->taille_ko, 0, ',', ' ') }} Ko
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1">
                                <a href="{{ asset('storage/' . $doc->chemin_stockage) }}" target="_blank"
                                    class="text-[10px] text-blue-600 font-bold bg-white border border-slate-200 px-2 py-1 rounded hover:bg-slate-50 transition">Voir</a>
                                @can('check-permission', ['Patrimoine & Equipements', 'suppression'])
                                    <form action="{{ route('documents.global.destroy', $doc->id_document) }}" method="POST"
                                        class="inline" onsubmit="return confirm('Supprimer ce document ?');">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="text-[10px] text-red-600 font-bold bg-white border border-slate-200 p-1 rounded hover:bg-red-50 transition">🗑️</button>
                                    </form>
                                @endcan
                            </div>
                        </li>
                    @empty
                        <p class="text-xs text-slate-400 italic text-center py-4">Aucune notice technique disponible.</p>
                    @endforelse
                </ul>

                @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                    <form action="{{ route('equipements.documents.store', $equipement->id_equipement) }}" method="POST"
                        enctype="multipart/form-data"
                        class="bg-slate-50/50 p-4 rounded-xl border border-slate-200 border-dashed">
                        @csrf
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wide mb-1">Téléverser
                            un document</label>
                        <div class="flex items-center gap-2 mt-2">
                            <input type="file" name="fichier" required
                                class="block w-full text-[11px] text-slate-400 file:mr-2 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-[11px] file:font-bold file:bg-slate-900 file:text-white hover:file:bg-slate-800 cursor-pointer">
                            <button type="submit"
                                class="px-3 py-1 bg-indigo-600 text-white font-bold rounded hover:bg-indigo-700 transition text-[11px]">📤</button>
                        </div>
                    </form>
                @endcan
            </div>

        </div>
    </div>
</div>
@endsection