@extends('layouts.app')

@section('title', 'Détails du Contrat #' . ($contrat->numero_contrat ?? 'Sans numéro'))

@section('content')
<div class="max-w-6xl mx-auto space-y-6 pb-12">

    <div
        class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="text-3xl select-none">📄</span>
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
            <p class="text-slate-500 font-medium ml-11 text-xs uppercase tracking-wide">{{ $contrat->type_contrat }}</p>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('contrats.index') }}"
                class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 transition text-center flex-1 sm:flex-initial">
                ← Retour
            </a>

            @if(auth()->user()->can('check-permission', ['Finances & Achats', 'ecriture']))
            <a href="{{ route('contrats.edit', $contrat->id_contrat) }}"
                class="px-4 py-2 bg-amber-100 text-amber-700 border border-amber-200 text-sm font-semibold rounded-lg hover:bg-amber-200 transition text-center flex-1 sm:flex-initial">
                ✏️ Modifier
            </a>
            @endif

            @if(auth()->user()->can('check-permission', ['Finances & Achats', 'suppression']))
            <form action="{{ route('contrats.destroy', $contrat->id_contrat) }}" method="POST"
                onsubmit="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer définitivement ce contrat ? Cette action supprimera également toutes les liaisons rattachées.');"
                class="flex-1 sm:flex-initial">
                @csrf @method('DELETE')
                <button type="submit"
                    class="w-full px-4 py-2 bg-red-100 text-red-700 border border-red-200 text-sm font-semibold rounded-lg hover:bg-red-200 transition text-center">
                    🗑️ Supprimer
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2">📋 Conditions
                    Générales</h3>
                <div class="text-sm text-slate-700 mb-6 bg-slate-50 p-4 rounded-lg border border-slate-100">
                    <span class="font-bold text-slate-900 block mb-1">Objet du contrat :</span>
                    <p class="text-slate-600 leading-relaxed">{{ $contrat->objet_contrat ?? 'Non spécifié' }}</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-xs font-medium text-slate-600">
                    <div>
                        <p class="text-slate-400 uppercase tracking-wider text-[10px] mb-0.5">Période de validité</p>
                        <p class="text-slate-800 font-semibold text-sm">
                            Du <span
                                class="font-mono">{{ $contrat->date_debut_contrat ? $contrat->date_debut_contrat->format('d/m/Y') : '-' }}</span>
                            au <span
                                class="font-mono {{ $isExpired ? 'text-red-600 font-bold' : '' }}">{{ $contrat->date_fin_contrat ? $contrat->date_fin_contrat->format('d/m/Y') : 'Indéterminé' }}</span>
                        </p>
                    </div>
                    <div>
                        <p class="text-slate-400 uppercase tracking-wider text-[10px] mb-0.5">Date de signature
                            administrative</p>
                        <p class="text-slate-800 text-sm font-semibold font-mono">
                            {{ $contrat->date_signature_contrat ? $contrat->date_signature_contrat->format('d/m/Y') : 'En attente' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-slate-400 uppercase tracking-wider text-[10px] mb-0.5">Prochaine date d'échéance
                        </p>
                        <p class="text-slate-800 text-sm font-semibold font-monoA">
                            {{ $contrat->date_echeance ? $contrat->date_echeance->format('d/m/Y') : '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-slate-400 uppercase tracking-wider text-[10px] mb-0.5">Titulaire du marché
                            (Tiers)</p>
                        <p class="text-blue-700 text-sm font-black flex items-center gap-1">
                            👤
                            {{ $contrat->raison_sociale ?? ($contrat->nom_tiers ? $contrat->nom_tiers . ' ' . $contrat->prenom_tiers : 'Titulaire inconnu') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-slate-400 uppercase tracking-wider text-[10px] mb-0.5">Durée contractuelle &
                            Préavis</p>
                        <p class="text-slate-800 text-sm font-semibold">
                            Engagement : {{ $contrat->duree_mois ? $contrat->duree_mois . ' mois' : 'Non spécifié' }}
                            <br>
                            Préavis de rupture :
                            {{ $contrat->preavis_resiliation_mois ? $contrat->preavis_resiliation_mois . ' mois' : 'Aucun' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-slate-400 uppercase tracking-wider text-[10px] mb-0.5">Clauses de renouvellement
                        </p>
                        <p class="text-slate-800 text-sm font-semibold italic">
                            {{ $contrat->modalite_renouvellement ?? 'Non précisé' }}</p>
                    </div>
                </div>
            </div>

            @if(str_contains(strtolower($contrat->type_contrat), 'location'))
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-200">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">🚚 Inventaire du matériel loué
                        / mis à disposition</h3>
                </div>

                <div class="divide-y divide-slate-100 bg-white">
                    @forelse($equipementsLies as $equip)
                    @php
                    $decisionPivot = $equip->pivot->id_decision ? collect($decisions)->firstWhere('id_decision',
                    $equip->pivot->id_decision) : null;
                    @endphp
                    <div
                        class="p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 text-sm hover:bg-slate-50">
                        <div>
                            <p class="font-bold text-slate-800">
                                {{ $equip->nom_equipement ?? 'Équipement inconnu' }}
                                <span
                                    class="text-blue-600 font-mono text-xs">(x{{ $equip->pivot->quantite_louee ?? 0 }})</span>
                            </p>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                Réf Acte : <span
                                    class="font-mono text-slate-600 font-bold">#{{ $decisionPivot->numero_decision ?? 'N/A' }}</span>
                                | État initial : <span
                                    class="text-slate-600 italic font-medium">{{ $equip->pivot->etat_depart ?? 'Non spécifié' }}</span>
                            </p>
                        </div>
                        <div class="text-left sm:text-right text-xs whitespace-nowrap">
                            <span
                                class="px-2 py-0.5 font-bold rounded bg-blue-50 text-blue-700 border border-blue-100 text-[10px] uppercase">
                                {{ $equip->pivot->statut_ligne ?? 'En cours' }}
                            </span>
                            <p class="text-[10px] text-slate-400 mt-1 font-mono">
                                Du
                                {{ $equip->pivot->date_debut_utilisation ? \Carbon\Carbon::parse($equip->pivot->date_debut_utilisation)->format('d/m/Y') : 'N/A' }}
                                au
                                {{ $equip->pivot->date_fin_utilisation ? \Carbon\Carbon::parse($equip->pivot->date_fin_utilisation)->format('d/m/Y') : 'Indéterminé' }}
                            </p>
                        </div>
                    </div>
                    @empty
                    <p class="p-8 text-center text-sm text-slate-400 italic bg-white">Aucun équipement rattaché à ce
                        contrat de location.</p>
                    @endforelse
                </div>

                @if(auth()->user()->can('check-permission', ['Finances & Achats', 'ecriture']))
                <div class="p-4 bg-slate-50 border-t border-slate-100 text-xs font-semibold text-slate-700">
                    <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider block mb-2.5">➕ Affecter
                        un matériel au parc locatif</span>
                    <form action="{{ route('contrats.location.store', $contrat->id_contrat) }}" method="POST"
                        class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <select name="id_equipement" required
                                class="w-full text-xs border border-slate-300 rounded-lg p-2 bg-white outline-none focus:ring-2 focus:ring-blue-500/20">
                                <option value="">-- Sélectionner le matériel --</option>
                                @foreach($equipementsDisponibles as $eq)
                                <option value="{{ $eq->id_equipement }}">{{ $eq->nom_equipement }}</option>
                                @endforeach
                            </select>

                            <select name="id_decision" required
                                class="w-full text-xs border border-slate-300 rounded-lg p-2 bg-white outline-none focus:ring-2 focus:ring-blue-500/20">
                                <option value="">-- Décision Administrative --</option>
                                @foreach($decisions as $dec)
                                <option value="{{ $dec->id_decision }}">{{ $dec->numero_decision }} -
                                    {{ Str::limit($dec->intitule_decision, 25) }}</option>
                                @endforeach
                            </select>

                            <div class="grid grid-cols-3 gap-2">
                                <input type="number" name="quantite_louee" required min="1" placeholder="Qté"
                                    class="col-span-1 text-xs border border-slate-300 rounded-lg p-2 focus:outline-none bg-white">
                                <input type="text" name="etat_depart" placeholder="État départ"
                                    class="col-span-2 text-xs border border-slate-300 rounded-lg p-2 focus:outline-none bg-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-center pt-1">
                            <input type="date" name="date_debut_utilisation" required
                                class="w-full text-xs border border-slate-300 rounded-lg p-2 bg-white outline-none">
                            <input type="date" name="date_fin_utilisation"
                                class="w-full text-xs border border-slate-300 rounded-lg p-2 bg-white outline-none">
                            <button type="submit"
                                class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs py-2 rounded-lg shadow transition whitespace-nowrap">
                                Valider la mise à disposition
                            </button>
                        </div>
                    </form>
                </div>
                @endif
            </div>
            @endif

            @if(str_contains(strtolower($contrat->type_contrat), 'maintenance') ||
            str_contains(strtolower($contrat->type_contrat), 'entretien'))

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-200">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">🔒 Parc d'équipements sous
                        contrat de maintenance</h3>
                </div>
                <div class="p-2 divide-y divide-slate-100 bg-white text-sm">
                    @forelse($equipementsLies as $eq)
                    <div class="p-2 flex justify-between items-center">
                        <span class="font-bold text-slate-800">⚙️ {{ $eq->nom_equipement }}</span>
                        <span class="text-xs font-mono text-slate-400 bg-slate-50 border px-2 py-0.5 rounded">ID Actif:
                            #{{ $eq->id_equipement }}</span>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 italic text-center py-4">Aucun équipement de la mairie n'est
                        explicitement lié à cette maintenance.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">🛠️ Bons d'Interventions et
                        Dépannages déclenchés</h3>
                    <span
                        class="text-xs font-bold bg-blue-100 text-blue-700 px-2 py-0.5 rounded font-mono">{{ isset($interventionsTriggered) ? $interventionsTriggered->count() : 0 }}</span>
                </div>
                <div class="divide-y divide-slate-100 bg-white">
                    @if(isset($interventionsTriggered) && $interventionsTriggered->isNotEmpty())
                    @foreach($interventionsTriggered as $int)
                    <div class="p-4 flex justify-between items-center text-sm hover:bg-slate-50 transition">
                        <div>
                            <a href="{{ route('interventions.show', $int->id_int) }}"
                                class="font-bold text-slate-900 hover:text-blue-600 hover:underline block">
                                🛠️ {{ $int->type_intervention }}
                            </a>
                            <p class="text-xs text-slate-400 mt-0.5 truncate max-w-md font-normal">
                                {{ $int->description }}</p>
                        </div>
                        <div class="text-right text-xs flex-shrink-0 ml-4">
                            <span
                                class="px-2 py-0.5 rounded-full font-bold bg-slate-100 border text-slate-600 text-[10px] uppercase">{{ $int->statut_global }}</span>
                            <p class="text-[10px] text-slate-400 font-mono mt-1">Ouvert le :
                                {{ \Carbon\Carbon::parse($int->date_ouverture)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <p class="p-6 text-center text-xs text-slate-400 italic">Aucune panne ou révision n'a encore été
                        ouverte sur ce contrat de maintenance.</p>
                    @endif
                </div>
            </div>
            @endif

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2">🏢 Périmètre
                    d'application (Patrimoine concerné)</h3>

                @if($equipementsLies->isEmpty() && $locauxLies->isEmpty() && $lieuxLies->isEmpty())
                <p class="text-sm text-slate-400 italic text-center py-4 bg-slate-50 rounded border border-dashed">Aucun
                    équipement, local ou lieu n'est géographiquement rattaché à ce contrat pour le moment.</p>
                @else
                <div class="space-y-2 text-xs">
                    @foreach($locauxLies as $local)
                    <div
                        class="flex items-center justify-between p-2.5 bg-slate-50 border border-slate-100 rounded-lg hover:bg-slate-100/60 transition">
                        <div class="flex items-center">
                            <span
                                class="text-[9px] bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded font-bold mr-2.5">LOCAL</span>
                            <span class="font-bold text-slate-800">{{ $local->nom_local }}</span>
                        </div>
                        <a href="{{ route('locaux.show', $local->id_local) }}"
                            class="text-blue-600 font-bold hover:underline">Consulter →</a>
                    </div>
                    @endforeach

                    @foreach($lieuxLies as $lieu)
                    <div
                        class="flex items-center justify-between p-2.5 bg-slate-50 border border-slate-100 rounded-lg hover:bg-slate-100/60 transition">
                        <div class="flex items-center">
                            <span
                                class="text-[9px] bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded font-bold mr-2.5">LIEU
                                PUBLIC</span>
                            <span class="font-bold text-slate-800">{{ $lieu->nom_lieu }}</span>
                        </div>
                        <a href="{{ route('lieux.show', $lieu->id_lieu) }}"
                            class="text-blue-600 font-bold hover:underline">Consulter →</a>
                    </div>
                    @endforeach

                    @foreach($equipementsLies as $equip)
                    <div
                        class="flex items-center justify-between p-2.5 bg-slate-50 border border-slate-100 rounded-lg hover:bg-slate-100/60 transition">
                        <div class="flex items-center">
                            <span
                                class="text-[9px] bg-amber-100 text-amber-700 px-2 py-0.5 rounded font-bold mr-2.5">ACTIF
                                MATÉRIEL</span>
                            <span class="font-bold text-slate-800">{{ $equip->nom_equipement }}</span>
                        </div>
                        <a href="{{ route('equipements.show', $equip->id_equipement) }}"
                            class="text-blue-600 font-bold hover:underline">Consulter →</a>
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

                <div class="space-y-4 text-xs">
                    <div class="p-4 bg-blue-50 border border-blue-100 rounded-xl text-center">
                        <p class="text-[10px] text-blue-600 font-bold uppercase tracking-wider mb-0.5">Coût de
                            l'Engagement Annuel</p>
                        <p class="text-2xl font-black text-blue-900">
                            {{ $contrat->prix_annuel ? number_format($contrat->prix_annuel, 2, ',', ' ') . ' €' : 'Non provisionné' }}
                        </p>
                        @if($contrat->prix_mois)
                        <p class="text-[10px] text-blue-600 font-medium mt-1">Lissage mensuel :
                            {{ number_format($contrat->prix_mois, 2, ',', ' ') }} €</p>
                        @endif
                    </div>

                    <div class="space-y-2.5 font-medium text-slate-600 pt-1">
                        <div class="flex justify-between border-b pb-2 text-slate-700">
                            <span class="text-slate-400">Fréquence de facturation</span>
                            <span class="font-bold">{{ $contrat->frequence_facturation ?? 'Périodique' }}</span>
                        </div>
                        <div class="flex justify-between border-b pb-2 text-slate-700">
                            <span class="text-slate-400">Mode de règlement</span>
                            <span class="font-bold">{{ $contrat->mode_reglement ?? 'Mandat Administratif' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Révision de prix indexée</span>
                            <span
                                class="px-2 py-0.5 font-bold rounded text-[10px] uppercase border {{ $contrat->revision_prix_prevue ? 'bg-amber-50 border-amber-200 text-amber-700' : 'bg-slate-50 text-slate-400' }}">
                                {{ $contrat->revision_prix_prevue ? 'Prévue' : 'Fixe' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2">📊 Imputation
                    Analytique</h3>

                <ul class="space-y-3.5 text-xs font-mono text-slate-700">
                    <li>
                        <span
                            class="text-slate-400 block font-sans text-[10px] font-bold uppercase tracking-wider mb-0.5">Code
                            d'imputation budgétaire</span>
                        <span
                            class="font-bold text-slate-900 text-sm bg-slate-50 border border-slate-100 px-2 py-1 rounded block">{{ $contrat->code_imputation ?? 'Non défini' }}</span>
                    </li>
                    <li>
                        <span
                            class="text-slate-400 block font-sans text-[10px] font-bold uppercase tracking-wider mb-0.5">Numéro
                            de Lot</span>
                        <span class="font-bold text-slate-800">{{ $contrat->lot ?? 'N/A' }}</span>
                    </li>
                    <li>
                        <span
                            class="text-slate-400 block font-sans text-[10px] font-bold uppercase tracking-wider mb-0.5">Code
                            Analytique Mairie</span>
                        <span
                            class="font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded border">{{ $contrat->code_analytique ?? 'Général' }}</span>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</div>
@endsection