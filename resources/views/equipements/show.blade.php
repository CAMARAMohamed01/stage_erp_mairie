@extends('layouts.app')

@section('header_title', 'Fiche Équipement - ' . $equipement->nom_equipement)

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        <div
            class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-wrap justify-between items-center gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <span class="text-3xl">⚙️</span>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $equipement->nom_equipement }}</h1>
                    <span
                        class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-100">
                        {{ $equipement->famille->libelle_famille ?? 'Non classé' }}
                    </span>
                </div>
                <p class="text-sm text-slate-500 mt-1.5">
                    Réf. Série : <span class="font-mono text-slate-700">{{ $equipement->reference_serie ?? 'N/A' }}</span>
                    | Marque : <span class="font-medium text-slate-700">{{ $equipement->marque ?? 'Non définie' }}</span>
                </p>
            </div>
            <div class="flex gap-2">
                <button onclick="history.back()"
                    class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 transition">←
                    Retour</button>
                <a href="{{ route('equipements.edit', $equipement->id_equipement) }}"
                    class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg font-bold text-sm transition">✏️
                    Modifier</a>
                <form action="{{ route('equipements.destroy', $equipement->id_equipement) }}" method="POST"
                    onsubmit="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer cet équipement ?');">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-bold text-sm transition">🗑️
                        Supprimer</button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2">📋 Informations
                        Générales</h3>
                    <div class="grid grid-cols-2 gap-6 text-sm">
                        <div>
                            <p class="text-slate-500">Date d'achat</p>
                            <p class="font-semibold text-slate-800">
                                {{ $equipement->date_achat ? \Carbon\Carbon::parse($equipement->date_achat)->format('d/m/Y') : 'Non renseignée' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-slate-500">Localisation</p>
                            <p class="font-semibold text-slate-800">
                                {{ $equipement->local->nom_local ?? ($equipement->lieuPublic->nom_lieu ?? 'Non affecté') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-slate-500">Couleur</p>
                            <p class="font-semibold text-slate-800">{{ $equipement->couleur ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-slate-500">État actuel</p>
                            <span
                                class="px-2 py-1 text-[10px] font-bold rounded-full {{ $equipement->etat_fonctionnement == 'Opérationnel' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $equipement->etat_fonctionnement }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">🛠️ Historique des
                            Interventions</h3>
                        <a href="{{ route('interventions.create', ['id_equipement' => $equipement->id_equipement]) }}"
                            class="text-xs bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-700">+ Signaler une
                            panne</a>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse($equipement->interventions as $int)
                            <div class="p-4 flex justify-between items-center hover:bg-slate-50">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">{{ $int->type_intervention }}</p>
                                    <p class="text-xs text-slate-400">Ouverte le
                                        {{ \Carbon\Carbon::parse($int->date_ouverture)->format('d/m/Y') }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span
                                        class="px-2 py-1 text-[10px] font-bold rounded-full {{ $int->statut_global == 'Terminé' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $int->statut_global }}
                                    </span>
                                    <a href="{{ route('interventions.show', $int->id_int) }}"
                                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">Détails →</a>
                                </div>
                            </div>
                        @empty
                            <p class="p-6 text-center text-sm text-slate-400 italic">Aucune intervention enregistrée.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="space-y-6">

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2">🛡️ Contrôles
                        Réglementaires</h3>
                    @forelse($equipement->controles as $c)
                        <div class="mb-4 last:mb-0">
                            <p class="text-sm font-semibold text-slate-800">{{ $c->designation }}</p>
                            <p class="text-xs text-slate-500">Fréquence : {{ $c->frequence_mois }} mois</p>
                            <div class="mt-1">
                                <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded">
                                    Dernier :
                                    {{ $c->pivot->date_controle ? \Carbon\Carbon::parse($c->pivot->date_controle)->format('d/m/Y') : 'Aucun' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic">Aucun contrôle requis.</p>
                    @endforelse
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2">💼 Gestion
                        Financière</h3>
                    <div class="space-y-4 text-sm">
                        @php
                            $achat = $equipement->date_achat ? \Carbon\Carbon::parse($equipement->date_achat) : null;
                            $garantieMois = (int) ($equipement->duree_garantie_mois ?? 0);
                            $finGarantie = ($achat && $garantieMois > 0) ? $achat->copy()->addMonths($garantieMois) : null;
                            $isExpired = $finGarantie ? now()->greaterThan($finGarantie) : true;
                        @endphp

                        <div>
                            <span class="text-slate-500 block">Garantie :</span>
                            <p class="font-semibold text-slate-800">
                                {{ $garantieMois > 0 ? $garantieMois . ' mois' : 'N/A' }}
                            </p>
                            @if($finGarantie)
                                <p class="text-xs mt-1 {{ $isExpired ? 'text-red-600 font-bold' : 'text-green-600' }}">
                                    {{ $isExpired ? 'Expirée le ' : 'Valide jusqu\'au ' }} {{ $finGarantie->format('d/m/Y') }}
                                </p>
                            @endif
                        </div>

                        @if($equipement->id_immo)
                            <div class="pt-4 border-t border-slate-100">
                                <span class="text-slate-500 block">Valeur d'acquisition :</span>
                                <span class="font-bold text-slate-900 text-lg">
                                    {{ number_format($equipement->immobilisation->valeur_achat ?? 0, 2, ',', ' ') }} €
                                </span>
                                <p class="text-xs text-slate-400 mt-1">N° Inv:
                                    {{ $equipement->immobilisation->num_inventaire ?? 'N/A' }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2">📄 Documents
                    </h3>
                    @forelse($equipement->documents ?? [] as $doc)
                        <div class="text-sm text-blue-600 hover:underline cursor-pointer py-1">
                            {{ $doc->nom_fichier }}
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic">Aucun document attaché.</p>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
@endsection