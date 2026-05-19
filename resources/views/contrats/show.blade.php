@extends('layouts.app')

@section('title', 'Détails du Contrat #' . $contrat->numero_contrat)

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">

        <div
            class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-wrap justify-between items-start gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <span class="text-3xl">📄</span>
                    <h1 class="text-2xl font-bold text-slate-800">{{ $contrat->numero_contrat ?? 'Contrat sans numéro' }}
                    </h1>
                    @php
                        $isExpired = $contrat->date_fin_contrat ? now()->greaterThan($contrat->date_fin_contrat) : false;
                    @endphp
                    <span
                        class="px-2.5 py-1 text-[10px] font-bold rounded-full uppercase tracking-wider {{ $isExpired ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                        {{ $isExpired ? 'Expiré' : 'Actif' }}
                    </span>
                </div>
                <p class="text-slate-500 font-medium ml-11">{{ $contrat->type_contrat }}</p>
            </div>
            <div class="flex gap-2">
                <button onclick="history.back()"
                    class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50">←
                    Retour</button>
                <a href="#"
                    class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg font-bold text-sm transition">✏️
                    Modifier</a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2">📋 Conditions
                        Générales</h3>
                    <p class="text-sm text-slate-700 mb-6 bg-slate-50 p-4 rounded-lg border border-slate-100">
                        <span class="font-bold text-slate-900 block mb-1">Objet du contrat :</span>
                        {{ $contrat->objet_contrat ?? 'Non spécifié' }}
                    </p>

                    <div class="grid grid-cols-2 gap-6 text-sm">
                        <div>
                            <p class="text-slate-500 mb-1">Période de validité</p>
                            <p class="font-semibold text-slate-800">
                                Du {{ $contrat->date_debut_contrat ? $contrat->date_debut_contrat->format('d/m/Y') : '-' }}
                                au <span
                                    class="{{ $isExpired ? 'text-red-600' : '' }}">{{ $contrat->date_fin_contrat ? $contrat->date_fin_contrat->format('d/m/Y') : '-' }}</span>
                            </p>
                        </div>
                        <div>
                            <p class="text-slate-500 mb-1">Titulaire (Prestataire)</p>
                            <p class="font-bold text-blue-700">{{ $contrat->tiers->nom_affiche ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-slate-500 mb-1">Durée et Préavis</p>
                            <p class="font-semibold text-slate-800">
                                Durée : {{ $contrat->duree_mois ? $contrat->duree_mois . ' mois' : '-' }} <br>
                                Préavis :
                                {{ $contrat->preavis_resiliation_mois ? $contrat->preavis_resiliation_mois . ' mois' : 'Aucun' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-slate-500 mb-1">Renouvellement</p>
                            <p class="font-semibold text-slate-800">{{ $contrat->modalite_renouvellement ?? 'Non précisé' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2">🏢 Périmètre
                        d'application (Patrimoine concerné)</h3>

                    @if($equipementsLies->isEmpty() && $locauxLies->isEmpty())
                        <p class="text-sm text-slate-400 italic text-center py-4 bg-slate-50 rounded border border-dashed">Aucun
                            équipement ou local n'est directement rattaché à ce contrat pour le moment.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($locauxLies as $local)
                                <div
                                    class="flex items-center justify-between p-3 bg-slate-50 border border-slate-100 rounded-lg hover:bg-slate-100">
                                    <div>
                                        <span
                                            class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded font-bold mr-2">LOCAL</span>
                                        <span class="font-semibold text-sm text-slate-800">{{ $local->nom_local }}</span>
                                    </div>
                                    <a href="{{ route('locaux.show', $local->id_local) }}"
                                        class="text-xs text-blue-600 font-bold hover:underline">Voir →</a>
                                </div>
                            @endforeach

                            @foreach($equipementsLies as $equip)
                                <div
                                    class="flex items-center justify-between p-3 bg-slate-50 border border-slate-100 rounded-lg hover:bg-slate-100">
                                    <div>
                                        <span
                                            class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded font-bold mr-2">ÉQUIPEMENT</span>
                                        <span class="font-semibold text-sm text-slate-800">{{ $equip->nom_equipement }}</span>
                                    </div>
                                    <a href="{{ route('equipements.show', $equip->id_equipement) }}"
                                        class="text-xs text-blue-600 font-bold hover:underline">Voir →</a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

            <div class="space-y-6">

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2">💶 Engagement
                        Financier</h3>

                    <div class="space-y-4 text-sm">
                        <div class="p-4 bg-blue-50 border border-blue-100 rounded-lg text-center">
                            <p class="text-xs text-blue-600 font-bold uppercase mb-1">Coût Annuel</p>
                            <p class="text-2xl font-extrabold text-blue-900">
                                {{ $contrat->prix_annuel ? number_format($contrat->prix_annuel, 2, ',', ' ') . ' €' : 'Non défini' }}
                            </p>
                            <p class="text-xs text-blue-600 mt-1">Mensuel :
                                {{ $contrat->prix_mois ? number_format($contrat->prix_mois, 2, ',', ' ') . ' €' : '-' }}
                            </p>
                        </div>

                        <div class="space-y-2 pt-2">
                            <div class="flex justify-between border-b border-slate-100 pb-2">
                                <span class="text-slate-500">Fréquence facturation</span>
                                <span
                                    class="font-semibold text-slate-800">{{ $contrat->frequence_facturation ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between border-b border-slate-100 pb-2">
                                <span class="text-slate-500">Mode de règlement</span>
                                <span class="font-semibold text-slate-800">{{ $contrat->mode_reglement ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Révision des prix</span>
                                <span
                                    class="font-bold {{ $contrat->revision_prix_prevue ? 'text-amber-600' : 'text-slate-400' }}">{{ $contrat->revision_prix_prevue ? 'Oui' : 'Non' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2">📊 Imputation
                        Analytique</h3>
                    <ul class="space-y-3 text-sm">
                        <li><span class="text-slate-500 block text-xs">Code Imputation</span> <span
                                class="font-mono text-slate-800 font-bold">{{ $contrat->code_imputation ?? 'N/A' }}</span>
                        </li>
                        <li><span class="text-slate-500 block text-xs">Lot</span> <span
                                class="font-mono text-slate-800">{{ $contrat->lot ?? 'N/A' }}</span></li>
                        <li><span class="text-slate-500 block text-xs">Analytique Mairie</span> <span
                                class="font-mono text-slate-800 bg-slate-100 px-2 py-0.5 rounded">{{ $contrat->code_analytique ?? 'N/A' }}</span>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
@endsection