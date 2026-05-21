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
            <div class="flex items-center gap-2">
                <a href="{{ route('contrats.index') }}"
                    class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 transition">←
                    Retour</a>

                @if(auth()->user()->can('check-permission', ['Finances & Achats', 'ecriture']))
                    <a href="{{ route('contrats.edit', $contrat->id_contrat) }}"
                        class="px-4 py-2 bg-amber-100 text-amber-700 border border-amber-200 text-sm font-semibold rounded-lg hover:bg-amber-200 transition">✏️
                        Modifier</a>
                @endif

                @if(auth()->user()->can('check-permission', ['Finances & Achats', 'suppression']))
                    <form action="{{ route('contrats.destroy', $contrat->id_contrat) }}" method="POST"
                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce contrat ? Cette action supprimera également les liaisons avec les équipements.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-100 text-red-700 border border-red-200 text-sm font-semibold rounded-lg hover:bg-red-200 transition">🗑️
                            Supprimer</button>
                    </form>
                @endif
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
                            <p class="font-bold text-blue-700">
                                {{ $contrat->raison_sociale ?? ($contrat->nom_tiers ? $contrat->nom_tiers . ' ' . $contrat->prenom_tiers : 'Titulaire inconnu') }}
                            </p>
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
                @if(str_contains(strtolower($contrat->type_contrat), 'location'))
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mt-6">
                        <div class="p-4 bg-slate-50 border-b border-slate-200">
                            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">🚚 Inventaire du matériel loué
                                / mis à disposition</h3>
                        </div>

                        <div class="divide-y divide-slate-100 bg-white">
                            @forelse($locations ?? [] as $loc)
                                <div class="p-4 flex flex-wrap justify-between items-center text-sm hover:bg-slate-50">
                                    <div>
                                        <p class="font-semibold text-slate-800">
                                            {{ $loc->nom_equipement ?? 'Équipement inconnu' }}
                                            (x{{ $loc->quantite_louee ?? 0 }})
                                        </p>
                                        <p class="text-xs text-slate-400">
                                            Réf Décision : <span
                                                class="font-mono text-slate-600 font-bold">{{ $loc->numero_decision ?? 'N/A' }}</span>
                                            | État départ : <span
                                                class="text-slate-600 italic">{{ $loc->etat_depart ?? 'Non spécifié' }}</span>
                                        </p>
                                    </div>
                                    <div class="text-right text-xs">
                                        <span class="px-2 py-0.5 font-bold rounded bg-blue-50 text-blue-700 border border-blue-100">
                                            {{ $loc->statut_ligne ?? 'En cours' }}
                                        </span>
                                        <p class="text-[10px] text-slate-400 mt-1">Du
                                            {{ $loc->date_debut_utilisation ? \Carbon\Carbon::parse($loc->date_debut_utilisation)->format('d/m/Y') : 'N/A' }}
                                            au
                                            {{ $loc->date_fin_utilisation ? \Carbon\Carbon::parse($loc->date_fin_utilisation)->format('d/m/Y') : 'Indéterminé' }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <p class="p-6 text-center text-sm text-slate-400 italic">
                                    Aucun équipement n'est actuellement rattaché à ce contrat de location.
                                </p>
                            @endforelse
                        </div>

                        @if(auth()->user()->can('check-permission', ['Finances & Achats', 'ecriture']))
                            <div class="p-4 bg-slate-50 border-t border-slate-100">
                                <span class="text-xs font-bold uppercase text-slate-400 tracking-wider block mb-3">➕ Affecter un
                                    équipement à ce contrat</span>
                                <form action="{{ route('contrats.location.store', $contrat->id_contrat) }}" method="POST"
                                    class="space-y-3">
                                    @csrf
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        <div>
                                            <select name="id_equipement" required
                                                class="w-full text-xs border border-slate-300 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-blue-500">
                                                <option value="">-- Choisir le matériel --</option>
                                                @foreach($equipementsDisponibles as $eq)
                                                    <option value="{{ $eq->id_equipement }}">{{ $eq->nom_equipement }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <select name="id_decision" required
                                                class="w-full text-xs border border-slate-300 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-blue-500">
                                                <option value="">-- Décision Administrative --</option>
                                                @foreach($decisions as $dec)
                                                    <option value="{{ $dec->id_decision }}">{{ $dec->numero_decision }} -
                                                        {{ Str::limit($dec->intitule_decision, 30) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="grid grid-cols-3 gap-2">
                                            <input type="number" name="quantite_louee" required min="1" placeholder="Qté"
                                                class="col-span-1 text-xs border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                            <input type="text" name="etat_depart" placeholder="État départ"
                                                class="col-span-2 text-xs border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <input type="date" name="date_debut_utilisation" required
                                                class="w-full text-xs border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <input type="date" name="date_fin_utilisation"
                                                class="w-full text-xs border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div class="flex justify-end">
                                            <button type="submit"
                                                class="w-full bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs px-4 py-2 rounded-lg shadow transition">
                                                Valider la mise à disposition
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                @endif
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