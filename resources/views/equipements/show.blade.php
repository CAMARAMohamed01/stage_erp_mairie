@extends('layouts.app')

@section('header_title', 'Fiche Équipement')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">

        <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200 flex justify-between items-start">
            <div>
                <h2 class="text-3xl font-bold text-slate-800">{{ $equipement->nom_equipement }}</h2>
                <p class="text-slate-500 mt-1 flex items-center">
                    <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded mr-3">
                        {{ $equipement->famille->libelle_famille ?? 'Famille inconnue' }}
                    </span>
                    Référence : {{ $equipement->reference_serie ?? 'Non renseignée' }}
                </p>
            </div>
            <div>
                @if($equipement->etat_fonctionnement == 'Opérationnel')
                    <span
                        class="bg-green-100 text-green-800 text-sm font-bold px-4 py-2 rounded-full border border-green-200">🟢
                        Opérationnel</span>
                @elseif($equipement->etat_fonctionnement == 'En panne')
                    <span class="bg-red-100 text-red-800 text-sm font-bold px-4 py-2 rounded-full border border-red-200">🔴 En
                        panne</span>
                @else
                    <span
                        class="bg-yellow-100 text-yellow-800 text-sm font-bold px-4 py-2 rounded-full border border-yellow-200">🟠
                        {{ $equipement->etat_fonctionnement }}</span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="md:col-span-2 space-y-6">
                <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200">
                    <h3 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4 flex items-center"><span
                            class="mr-2">📍</span> Localisation</h3>
                    @if($equipement->local)
                        <p class="text-slate-700"><span class="font-semibold">Local :</span> {{ $equipement->local->nom_local }}
                        </p>
                    @elseif($equipement->lieuPublic)
                        <p class="text-slate-700"><span class="font-semibold">Lieu Public :</span>
                            {{ $equipement->lieuPublic->nom_lieu }}</p>
                    @else
                        <p class="text-slate-500 italic">Aucune localisation précise enregistrée.</p>
                    @endif
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200">
                    <h3 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4 flex items-center"><span
                            class="mr-2">🚧</span> Historique des interventions</h3>

                    <div class="text-center py-6 text-slate-500 bg-slate-50 rounded border border-dashed border-slate-300">
                        <p>Aucune intervention enregistrée sur cet équipement pour le moment.</p>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200">
                    <h3 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4 flex items-center"><span
                            class="mr-2">💳</span> Contrat & Garantie</h3>
                    <ul class="space-y-3 text-sm">
                        <li><span class="text-slate-500 block">Marque</span> <span
                                class="font-semibold text-slate-800">{{ $equipement->marque ?? '-' }}</span></li>
                        <li><span class="text-slate-500 block">Couleur</span> <span
                                class="font-semibold text-slate-800">{{ $equipement->couleur ?? '-' }}</span></li>
                        <li><span class="text-slate-500 block">Date d'achat</span> <span
                                class="font-semibold text-slate-800">{{ $equipement->date_achat ? \Carbon\Carbon::parse($equipement->date_achat)->format('d/m/Y') : '-' }}</span>
                        </li>
                        <li><span class="text-slate-500 block">Garantie</span> <span
                                class="font-semibold text-slate-800">{{ $equipement->duree_garantie_mois ? $equipement->duree_garantie_mois . ' mois' : '-' }}</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-slate-200">
                    <h3 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4 flex items-center"><span
                            class="mr-2">🛡️</span> Contrôles</h3>
                    @if($equipement->controles->count() > 0)
                        <ul class="list-disc list-inside text-sm text-slate-700 space-y-3">
                            @foreach($equipement->controles as $controle)
                                <li class="pb-2 border-b border-slate-100 last:border-0">
                                    <span class="font-medium">{{ $controle->designation }}</span>
                                    <span class="text-slate-400 text-xs">({{ $controle->frequence_mois }} mois)</span>

                                    <div class="ml-5 mt-1">
                                        @if($controle->pivot->date_controle)
                                            <span class="text-xs bg-blue-50 text-blue-700 px-2 py-1 rounded">
                                                Dernier contrôle :
                                                {{ \Carbon\Carbon::parse($controle->pivot->date_controle)->format('d/m/Y') }}
                                            </span>
                                        @else
                                            <span class="text-xs bg-yellow-50 text-yellow-700 px-2 py-1 rounded">
                                                Aucun contrôle enregistré
                                            </span>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-slate-500 italic">Aucun contrôle réglementaire requis.</p>
                    @endif
                </div>
            </div>

        </div>

        <div class="mt-4">
            <a href="{{ route('equipements.index') }}" class="text-blue-600 hover:underline">← Retour à l'inventaire</a>
        </div>
    </div>
@endsection