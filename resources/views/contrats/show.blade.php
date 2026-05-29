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
                @php $isExpired = $contrat->date_fin_contrat ? now()->greaterThan($contrat->date_fin_contrat) : false;
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
                class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 transition text-center flex-1 sm:flex-initial">←
                Retour</a>

            @if(auth()->user()->can('check-permission', ['Finances & Achats', 'ecriture']))
            <a href="{{ route('contrats.edit', $contrat->id_contrat) }}"
                class="px-4 py-2 bg-amber-100 text-amber-700 border border-amber-200 text-sm font-semibold rounded-lg hover:bg-amber-200 transition text-center flex-1 sm:flex-initial">✏️
                Modifier</a>
            @endif

            @if(auth()->user()->can('check-permission', ['Finances & Achats', 'suppression']))
            <form action="{{ route('contrats.destroy', $contrat->id_contrat) }}" method="POST"
                onsubmit="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer définitivement ce contrat ? Cette action nettoiera l\'ensemble de ses affectations patrimoniales.');"
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

    @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold rounded-xl shadow-sm">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold rounded-xl shadow-sm">
        {{ session('error') }}
    </div>
    @endif

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
                        <p class="text-slate-400 uppercase tracking-wider text-[10px] mb-0.5">Titulaire du marché
                            (Tiers)</p>
                        <p class="text-blue-700 text-sm font-black">👤
                            {{ $contrat->raison_sociale ?? ($contrat->nom_tiers ? $contrat->nom_tiers . ' ' . $contrat->prenom_tiers : 'Titulaire inconnu') }}
                        </p>
                    </div>
                </div>
            </div>

            @if(str_contains(strtolower($contrat->type_contrat), 'location'))
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">

                <div
                    class="bg-slate-50 border-b border-slate-200 flex flex-wrap text-xs font-bold uppercase tracking-wider text-slate-500">
                    <button onclick="switchTab('tab-equipement')" id="btn-tab-equipement"
                        class="px-5 py-3.5 border-r bg-white text-blue-600 border-b-2 border-b-blue-600 focus:outline-none">🚚
                        Matériels ({{ $equipementsLies->count() }})</button>
                    <button onclick="switchTab('tab-batiment')" id="btn-tab-batiment"
                        class="px-5 py-3.5 border-r hover:bg-slate-100 text-slate-600 focus:outline-none">🏢 Bâtiments
                        ({{ count($batimentsLies) }})</button>
                    <button onclick="switchTab('tab-local')" id="btn-tab-local"
                        class="px-5 py-3.5 border-r hover:bg-slate-100 text-slate-600 focus:outline-none">🚪 Locaux &
                        Salles ({{ $locauxLies->count() }})</button>
                    <button onclick="switchTab('tab-lieu')" id="btn-tab-lieu"
                        class="px-5 py-3.5 border-r hover:bg-slate-100 text-slate-600 focus:outline-none">🌿 Domaines &
                        Lieux ({{ $lieuxLies->count() }})</button>
                </div>

                <div id="panel-tab-equipement" class="tab-panel bg-white">
                    <div class="divide-y divide-slate-100">
                        @forelse($equipementsLies as $equip)
                        @php $decisionPivot = $equip->pivot->id_decision ?
                        collect($decisions)->firstWhere('id_decision', $equip->pivot->id_decision) : null; @endphp
                        <div class="p-4 flex justify-between items-center text-sm hover:bg-slate-50 transition gap-4">
                            <div class="space-y-1 flex-1">
                                <div class="flex flex-wrap justify-between items-center gap-2">
                                    <p class="font-bold text-slate-800 text-sm">{{ $equip->nom_equipement }} <span
                                            class="text-blue-600">(x{{ $equip->pivot->quantite_louee }})</span></p>
                                    <span
                                        class="px-2 py-0.5 bg-slate-50 text-slate-500 border rounded font-mono font-bold text-xs whitespace-nowrap">
                                        Du
                                        {{ $equip->pivot->date_debut_utilisation ? \Carbon\Carbon::parse($equip->pivot->date_debut_utilisation)->format('d/m/Y') : 'N/A' }}
                                        au
                                        {{ $equip->pivot->date_fin_utilisation ? \Carbon\Carbon::parse($equip->pivot->date_fin_utilisation)->format('d/m/Y') : 'Indéterminé' }}
                                    </span>
                                </div>
                                <p class="text-slate-400 text-xs font-normal">
                                    Acte : <span
                                        class="font-mono text-slate-600 font-bold">#{{ $decisionPivot->numero_decision ?? 'Aucun' }}</span>
                                    | État initial : <span
                                        class="italic font-medium text-slate-600">{{ $equip->pivot->etat_depart ?? 'Non spécifié' }}</span>
                                </p>
                                @if($equip->pivot->observations)
                                <p
                                    class="text-xs text-slate-500 bg-slate-50 px-2 py-1 rounded border border-dashed mt-1">
                                    📝 <span class="italic">{{ $equip->pivot->observations }}</span></p>
                                @endif
                            </div>
                            <div class="flex items-center gap-3 ml-2 pl-2 border-l flex-shrink-0 h-full">
                                <span
                                    class="px-2 py-0.5 font-bold rounded bg-blue-50 text-blue-700 border text-[10px] font-mono">ID:
                                    #{{ $equip->id_equipement }}</span>
                                @can('check-permission', ['Finances & Achats', 'ecriture'])
                                <form
                                    action="{{ route('contrats.materiel.destroy', [$contrat->id_contrat, $equip->id_equipement, $equip->pivot->id_decision ?? 0]) }}"
                                    method="POST" onsubmit="return confirm('Retirer ce matériel du contrat ?');"
                                    class="flex items-center">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="text-red-400 hover:text-red-600 text-sm transition p-1 transform hover:scale-110">🗑️</button>
                                </form>
                                @endcan
                            </div>
                        </div>
                        @empty
                        <p class="p-6 text-center text-xs text-slate-400 italic">Aucun matériel rattaché.</p>
                        @endforelse
                    </div>

                    @if(auth()->user()->can('check-permission', ['Finances & Achats', 'ecriture']))
                    <div class="p-4 bg-slate-50 border-t border-slate-100">
                        <form action="{{ route('contrats.location.store', $contrat->id_contrat) }}" method="POST"
                            class="space-y-3 text-xs font-bold text-slate-700">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div><label class="block mb-1 text-slate-500">Équipement *</label>
                                    <select name="id_equipement" required
                                        class="w-full border rounded-lg p-2 bg-white font-medium text-slate-700">
                                        <option value="">-- Choisir --</option>
                                        @foreach($equipementsDisponibles as $eq) <option
                                            value="{{ $eq->id_equipement }}">{{ $eq->nom_equipement }}
                                            (#{{ $eq->id_equipement }})</option> @endforeach
                                    </select>
                                </div>
                                <div><label class="block mb-1 text-slate-500">Arrêté d'autorisation (Optionnel)</label>
                                    <select name="id_decision"
                                        class="w-full border rounded-lg p-2 bg-white font-medium text-slate-700">
                                        <option value="">-- Aucune décision --</option>
                                        @foreach($decisions as $dec) <option value="{{ $dec->id_decision }}">
                                            {{ $dec->numero_decision }}</option> @endforeach
                                    </select>
                                </div>
                                <div class="grid grid-cols-3 gap-2">
                                    <div class="col-span-1"><label
                                            class="block mb-1 text-slate-500">Quantité</label><input type="number"
                                            name="quantite_louee" required min="1"
                                            class="w-full border rounded-lg p-2 bg-white text-center"></div>
                                    <div class="col-span-2"><label class="block mb-1 text-slate-500">État
                                            départ</label><input type="text" name="etat_depart" placeholder="Ex: Neuf"
                                            class="w-full border rounded-lg p-2 bg-white font-medium"></div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                                <div><label class="block mb-1 text-slate-500">Date de début</label><input type="date"
                                        name="date_debut_utilisation" required
                                        class="w-full border rounded-lg p-2 bg-white font-mono"></div>
                                <div><label class="block mb-1 text-slate-500">Date de fin</label><input type="date"
                                        name="date_fin_utilisation"
                                        class="w-full border rounded-lg p-2 bg-white font-mono"></div>
                                <button type="submit"
                                    class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-2 rounded-lg transition shadow-sm">Affecter
                                    le matériel</button>
                            </div>
                            <div><label class="block mb-1 text-slate-500">Observations / Notes</label><input type="text"
                                    name="observations" placeholder="Notes complémentaires..."
                                    class="w-full border rounded-lg p-2 bg-white font-medium font-sans"></div>
                        </form>
                    </div>
                    @endif
                </div>

                <div id="panel-tab-batiment" class="tab-panel bg-white hidden">
                    <div class="divide-y divide-slate-100">
                        @forelse($batimentsLies as $bat)
                        <div class="p-4 flex justify-between items-center text-xs hover:bg-slate-50 transition gap-4">
                            <div class="space-y-1.5 flex-1">
                                <div class="flex flex-wrap justify-between items-center gap-2">
                                    <span class="font-bold text-sm text-slate-800">🏢 {{ $bat->nom_bat }}</span>
                                    <span
                                        class="px-2 py-0.5 bg-blue-50 text-blue-700 border rounded font-mono font-bold whitespace-nowrap">
                                        Du {{ \Carbon\Carbon::parse($bat->date_debut_utilisation)->format('d/m/Y') }}
                                        au
                                        {{ $bat->date_fin_utilisation ? \Carbon\Carbon::parse($bat->date_fin_utilisation)->format('d/m/Y') : 'Indéterminé' }}
                                    </span>
                                </div>
                                <div
                                    class="text-slate-400 font-medium grid grid-cols-2 gap-2 bg-slate-50/50 p-2 rounded border">
                                    <div>Caution : <span
                                            class="font-bold text-slate-700">{{ $bat->caution_retenue ? number_format($bat->caution_retenue, 2, ',', ' ') . ' €' : 'Aucune' }}</span>
                                    </div>
                                    <div>État Entrée : <span
                                            class="italic text-slate-600 font-semibold">{{ $bat->etat_lieux_entree ?? 'Non renseigné' }}</span>
                                    </div>
                                </div>
                                @if($bat->observations)
                                <p class="text-slate-500 italic mt-0.5 pl-1">📝 « {{ $bat->observations }} »</p>
                                @endif
                            </div>
                            @can('check-permission', ['Finances & Achats', 'ecriture'])
                            <div class="ml-2 pl-4 border-l flex-shrink-0 flex items-center h-full">
                                <form
                                    action="{{ route('contrats.batiment.destroy', [$contrat->id_contrat, $bat->id_batiment]) }}"
                                    method="POST"
                                    onsubmit="return confirm('Désaffecter entièrement ce bâtiment du contrat ?');"
                                    class="flex items-center">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="text-red-400 hover:text-red-600 text-sm transition p-2 transform hover:scale-110">🗑️</button>
                                </form>
                            </div>
                            @endcan
                        </div>
                        @empty
                        <p class="p-6 text-center text-xs text-slate-400 italic">Aucun bâtiment complet loué.</p>
                        @endforelse
                    </div>

                    @if(auth()->user()->can('check-permission', ['Finances & Achats', 'ecriture']))
                    <div class="p-4 bg-slate-50 border-t border-slate-100">
                        <form action="{{ route('contrats.batiment.store', $contrat->id_contrat) }}" method="POST"
                            class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs font-bold text-slate-700">
                            @csrf
                            <div>
                                <label class="block mb-1 text-slate-500">Choisir le Bâtiment *</label>
                                <select name="id_batiment" required
                                    class="w-full border rounded-lg p-2 bg-white font-medium">
                                    <option value="">-- Choisir le complexe --</option>
                                    @foreach($batimentsDisponibles as $b_disp) <option
                                        value="{{ $b_disp->id_batiment }}">{{ $b_disp->nom_bat }}</option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block mb-1 text-slate-500">Acte Administratif (Optionnel)</label>
                                <select name="id_decision" class="w-full border rounded-lg p-2 bg-white font-medium">
                                    <option value="">-- Aucun --</option>
                                    @foreach($decisions as $dec) <option value="{{ $dec->id_decision }}">
                                        {{ $dec->numero_decision }}</option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block mb-1 text-slate-500">Caution de garantie (€)</label>
                                <input type="number" step="0.01" name="caution_retenue" placeholder="Ex: 2500"
                                    class="w-full border rounded-lg p-2 bg-white font-medium">
                            </div>
                            <div class="md:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-3 items-end pt-1">
                                <div><label class="block mb-1 text-slate-500">Date d'entrée</label><input type="date"
                                        name="date_debut_utilisation" required
                                        class="w-full border rounded-lg p-2 bg-white font-mono"></div>
                                <div><label class="block mb-1 text-slate-500">Date de sortie</label><input type="date"
                                        name="date_fin_utilisation"
                                        class="w-full border rounded-lg p-2 bg-white font-mono"></div>
                                <button type="submit"
                                    class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-2 rounded-lg transition shadow-sm">Valider
                                    la mise à disposition</button>
                            </div>
                            <div class="md:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div><label class="block mb-1 text-slate-500">État des lieux d'entrée</label><input
                                        type="text" name="etat_lieux_entree" placeholder="Compteurs, clés..."
                                        class="w-full border rounded-lg p-2 bg-white font-medium font-sans"></div>
                                <div><label class="block mb-1 text-slate-500">Observations complémentaires</label><input
                                        type="text" name="observations" placeholder="Précisions..."
                                        class="w-full border rounded-lg p-2 bg-white font-medium font-sans"></div>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>

                <div id="panel-tab-local" class="tab-panel bg-white hidden">
                    <div class="divide-y divide-slate-100">
                        @forelse($locauxLies as $loc)
                        <div class="p-4 flex justify-between items-center text-xs hover:bg-slate-50 transition gap-4">
                            <div class="flex-1">
                                <div class="flex flex-wrap justify-between items-center gap-2">
                                    <p class="font-bold text-slate-800 text-sm">🚪 {{ $loc->nom_local }}</p>
                                    <span
                                        class="px-2 py-0.5 bg-slate-100 font-mono text-[10px] rounded text-slate-500 font-bold whitespace-nowrap">
                                        Du
                                        {{ \Carbon\Carbon::parse($loc->pivot->date_debut_utilisation)->format('d/m/Y') }}
                                        au
                                        {{ $loc->pivot->date_fin_utilisation ? \Carbon\Carbon::parse($loc->pivot->date_fin_utilisation)->format('d/m/Y') : 'Indéterminé' }}
                                    </span>
                                </div>
                                <p class="text-slate-400 mt-0.5 font-normal text-xs">Caution : <span
                                        class="font-bold text-slate-700">{{ $loc->pivot->caution_retenue ? number_format($loc->pivot->caution_retenue, 2, ',', ' ') . ' €' : 'Aucune' }}</span>
                                    | État : <span
                                        class="italic text-slate-600 font-semibold">{{ $loc->pivot->etat_lieux_entree ?? 'Non fait' }}</span>
                                </p>
                            </div>
                            @can('check-permission', ['Finances & Achats', 'ecriture'])
                            <div class="ml-2 pl-4 border-l flex-shrink-0 flex items-center h-full">
                                <form
                                    action="{{ route('contrats.local.destroy', [$contrat->id_contrat, $loc->id_local, $loc->pivot->id_decision ?? 0]) }}"
                                    method="POST" onsubmit="return confirm('Retirer ce local du contrat ?');"
                                    class="flex items-center">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="text-red-400 hover:text-red-600 text-sm transition p-1 transform hover:scale-110">🗑️</button>
                                </form>
                            </div>
                            @endcan
                        </div>
                        @empty
                        <p class="p-6 text-center text-xs text-slate-400 italic">Aucune salle affectée.</p>
                        @endforelse
                    </div>
                    @if(auth()->user()->can('check-permission', ['Finances & Achats', 'ecriture']))
                    <div class="p-4 bg-slate-50 border-t border-slate-100">
                        <form action="{{ route('contrats.local.store', $contrat->id_contrat) }}" method="POST"
                            class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs font-bold text-slate-700">
                            @csrf
                            <div>
                                <label class="block mb-1 text-slate-500">Salle / Pièce *</label>
                                <select name="id_local" required
                                    class="w-full border rounded-lg p-2 bg-white font-medium">
                                    <option value="">-- Sélection --</option>
                                    @foreach($locauxDisponibles as $l_disp) <option value="{{ $l_disp->id_local }}">
                                        {{ $l_disp->nom_local }}</option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block mb-1 text-slate-500">Décision d'attribution (Optionnel)</label>
                                <select name="id_decision" class="w-full border rounded-lg p-2 bg-white font-medium">
                                    <option value="">-- Aucune décision --</option>
                                    @foreach($decisions as $dec) <option value="{{ $dec->id_decision }}">
                                        {{ $dec->numero_decision }}</option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block mb-1 text-slate-500">Caution (€)</label>
                                <input type="number" step="0.01" name="caution_retenue" placeholder="Ex: 500"
                                    class="w-full border rounded-lg p-2 bg-white font-medium">
                            </div>
                            <div class="md:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-3 items-end pt-1">
                                <div><label class="block mb-1 text-slate-500">Date d'entrée</label><input type="date"
                                        name="date_debut_utilisation" required
                                        class="w-full border rounded-lg p-2 bg-white font-mono"></div>
                                <div><label class="block mb-1 text-slate-500">Date de sortie</label><input type="date"
                                        name="date_fin_utilisation"
                                        class="w-full border rounded-lg p-2 bg-white font-mono"></div>
                                <button type="submit"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-lg transition shadow-sm">Enregistrer
                                    la location</button>
                            </div>
                            <div class="md:col-span-3">
                                <label class="block mb-1 text-slate-500">Observations d'entrée</label>
                                <input type="text" name="etat_lieux_entree" placeholder="État des lieux d'entrée..."
                                    class="w-full border rounded-lg p-2 bg-white font-medium font-sans">
                            </div>
                        </form>
                    </div>
                    @endif
                </div>

                <div id="panel-tab-lieu" class="tab-panel bg-white hidden">
                    <div class="divide-y divide-slate-100">
                        @forelse($lieuxLies as $lieu)
                        <div class="p-4 flex justify-between items-center text-xs hover:bg-slate-50 transition gap-4">
                            <div class="flex-1">
                                <div class="flex flex-wrap justify-between items-center gap-2">
                                    <p class="font-bold text-slate-800 text-sm">🌿 {{ $lieu->nom_lieu }}</p>
                                    <span
                                        class="px-2 py-0.5 bg-slate-100 font-mono text-[10px] rounded text-slate-500 font-bold whitespace-nowrap">
                                        Du
                                        {{ \Carbon\Carbon::parse($lieu->pivot->date_debut_occupation)->format('d/m/Y') }}
                                        au
                                        {{ $lieu->pivot->date_fin_occupation ? \Carbon\Carbon::parse($lieu->pivot->date_fin_occupation)->format('d/m/Y') : 'Indéterminé' }}
                                    </span>
                                </div>
                                <p class="text-slate-400 mt-0.5 font-normal text-xs">Usage : <span
                                        class="font-semibold text-slate-700">{{ $lieu->pivot->usage_specifique ?? 'Général' }}</span>
                                    | Emprise : <span
                                        class="font-bold text-slate-700">{{ $lieu->pivot->surface_occupee_m2 ?? '—' }}
                                        m²</span></p>
                            </div>
                            @can('check-permission', ['Finances & Achats', 'ecriture'])
                            <div class="ml-2 pl-4 border-l flex-shrink-0 flex items-center h-full">
                                <form
                                    action="{{ route('contrats.lieu.destroy', [$contrat->id_contrat, $lieu->id_lieu, $lieu->pivot->id_decision ?? 0]) }}"
                                    method="POST" onsubmit="return confirm('Retirer cet espace du contrat ?');"
                                    class="flex items-center">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="text-red-400 hover:text-red-600 text-sm transition p-1 transform hover:scale-110">🗑️</button>
                                </form>
                            </div>
                            @endcan
                        </div>
                        @empty
                        <p class="p-6 text-center text-xs text-slate-400 italic">Aucun domaine extérieur conventionné.
                        </p>
                        @endforelse
                    </div>
                    @if(auth()->user()->can('check-permission', ['Finances & Achats', 'ecriture']))
                    <div class="p-4 bg-slate-50 border-t border-slate-100">
                        <form action="{{ route('contrats.lieu.store', $contrat->id_contrat) }}" method="POST"
                            class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs font-bold text-slate-700">
                            @csrf
                            <div>
                                <label class="block mb-1 text-slate-500">Espace / Terrain *</label>
                                <select name="id_lieu" required
                                    class="w-full border rounded-lg p-2 bg-white font-medium">
                                    <option value="">-- Sélection --</option>
                                    @foreach($lieuxDisponibles as $lie_disp) <option value="{{ $lie_disp->id_lieu }}">
                                        {{ $lie_disp->nom_lieu }}</option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block mb-1 text-slate-500">Arrêté de voirie / occupation
                                    (Optionnel)</label>
                                <select name="id_decision" class="w-full border rounded-lg p-2 bg-white font-medium">
                                    <option value="">-- Aucun --</option>
                                    @foreach($decisions as $dec) <option value="{{ $dec->id_decision }}">
                                        {{ $dec->numero_decision }}</option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block mb-1 text-slate-500">Surface affectée (m²)</label>
                                <input type="number" step="0.01" name="surface_occupee_m2" placeholder="Ex: 45.50"
                                    class="w-full border rounded-lg p-2 bg-white font-medium">
                            </div>
                            <div class="md:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-3 items-end pt-1">
                                <div><label class="block mb-1 text-slate-500">Début occupation</label><input type="date"
                                        name="date_debut_occupation" required
                                        class="w-full border rounded-lg p-2 bg-white font-mono"></div>
                                <div><label class="block mb-1 text-slate-500">Fin occupation</label><input type="date"
                                        name="date_fin_occupation"
                                        class="w-full border rounded-lg p-2 bg-white font-mono"></div>
                                <button type="submit"
                                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 rounded-lg transition shadow-sm">Créer
                                    la convention</button>
                            </div>
                            <div class="md:col-span-3">
                                <label class="block mb-1 text-slate-500">Usage spécifique autorisé</label>
                                <input type="text" name="usage_specifique"
                                    placeholder="Ex: Terrasse de café estivale..."
                                    class="w-full border rounded-lg p-2 bg-white font-medium">
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            @if(str_contains(strtolower($contrat->type_contrat), 'maintenance') ||
            str_contains(strtolower($contrat->type_contrat), 'entretien'))
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-200">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">🔒 Actifs couverts par la
                        maintenance</h3>
                </div>
                <div class="p-2 divide-y divide-slate-100 bg-white text-xs">
                    @forelse($equipementsLies as $eq)
                    <div class="p-2 flex justify-between items-center font-medium">
                        <span class="font-bold text-slate-800">⚙️ {{ $eq->nom_equipement }}</span>
                        <span class="font-mono text-slate-400 bg-slate-50 border px-2 py-0.5 rounded">ID :
                            #{{ $eq->id_equipement }}</span>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 italic text-center py-4 bg-white">Aucun actif technique rattaché.
                    </p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">🛠️ Bons d'Interventions et
                        Dépannages rattachés</h3>
                </div>
                <div class="divide-y divide-slate-100 bg-white">
                    @forelse($interventionsTriggered as $int)
                    <div class="p-4 flex justify-between items-center text-xs hover:bg-slate-50 transition font-medium">
                        <div>
                            <a href="{{ route('interventions.show', $int->id_int) }}"
                                class="font-bold text-slate-900 hover:text-blue-600 hover:underline">🛠️
                                {{ $int->type_intervention }}</a>
                            <p class="text-slate-400 mt-0.5 font-normal">{{ Str::limit($int->description, 90) }}</p>
                        </div>
                        <div class="text-right flex-shrink-0 ml-4">
                            <span
                                class="px-2 py-0.5 rounded-full font-bold bg-slate-100 border text-slate-600 text-[10px] uppercase">{{ $int->statut_global }}</span>
                            <p class="text-[10px] text-slate-400 font-mono mt-1">Date :
                                {{ \Carbon\Carbon::parse($int->date_ouverture)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="p-6 text-center text-xs text-slate-400 italic bg-white">Aucun bon de panne sous ce
                        contrat.</p>
                    @endforelse
                </div>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2">💶 Engagement
                    Financier</h3>
                <div class="space-y-4 text-xs font-medium">
                    <div class="p-4 bg-blue-50 border border-blue-100 rounded-xl text-center">
                        <p class="text-[10px] text-blue-600 font-bold uppercase tracking-wider mb-0.5">Enveloppe
                            Annuelle</p>
                        <p class="text-2xl font-black text-blue-900">
                            {{ $contrat->prix_annuel ? number_format($contrat->prix_annuel, 2, ',', ' ') . ' €' : 'Non provisionné' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2">📊 Nomenclature
                    & Code</h3>
                <ul class="space-y-3 font-mono text-xs text-slate-700">
                    <li><span
                            class="font-sans text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Code
                            Imputation</span><span
                            class="font-bold text-slate-900 bg-slate-50 border px-2 py-1 rounded block">{{ $contrat->code_imputation ?? 'Non défini' }}</span>
                    </li>
                    <li><span
                            class="font-sans text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Code
                            Analytique Mairie</span><span
                            class="font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded border">{{ $contrat->code_analytique ?? 'Général' }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function switchTab(tabId) {
    document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.add('hidden'));
    document.getElementById('panel-' + tabId).classList.remove('hidden');

    document.querySelectorAll('[id^="btn-tab-"]').forEach(btn => {
        btn.classList.remove('bg-white', 'text-blue-600', 'border-b-2', 'border-b-blue-600');
        btn.classList.add('hover:bg-slate-100', 'text-slate-600');
    });

    const activeBtn = document.getElementById('btn-' + tabId);
    activeBtn.classList.remove('hover:bg-slate-100', 'text-slate-600');
    activeBtn.classList.add('bg-white', 'text-blue-600', 'border-b-2', 'border-b-blue-600');
}
</script>
@endsection