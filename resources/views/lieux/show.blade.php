@extends('layouts.app')

@section('header_title', 'Fiche Espace Public - ' . $lieu->nom_lieu)

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        <div
            class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-wrap justify-between items-center gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <span class="text-3xl">🌳</span>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $lieu->nom_lieu }}</h1>
                    <span
                        class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-50 text-green-700 border border-green-100">
                        {{ $lieu->typologie_lieu ?? 'Espace Extérieur' }}
                    </span>
                    @if($lieu->categorie_erp)
                        <span
                            class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-100">
                            ERP Cat. {{ $lieu->categorie_erp }}
                        </span>
                    @endif
                </div>
                <p class="text-sm text-slate-500 mt-1.5 flex items-center gap-1.5">
                    📍 Secteur Cadastral : <span class="text-slate-700 font-medium">{{ $lieu->section_cadastrale }} -
                        N°{{ $lieu->num_parcelle }} (Lieu-dit : {{ $lieu->nom_lieu_dit }})</span>
                    | 📏 {{ $lieu->surface_m2 ? $lieu->surface_m2 . ' m²' : 'Surface inconnue' }}
                </p>
                @if($lieu->horaire_ouverture)
                    <p class="text-xs text-slate-400 mt-1">🕒 Ouvert de
                        {{ \Carbon\Carbon::parse($lieu->horaire_ouverture)->format('H:i') }} à
                        {{ \Carbon\Carbon::parse($lieu->horaire_fermeture)->format('H:i') }}
                    </p>
                @endif

            </div>


            <div class="flex gap-2">
                <button onclick="history.back()"
                    class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 transition">
                    ← Retour
                </button>
                @can('check-permission', ['Patrimoine & Equipements', 'suppression'])
                    <form action="{{ route('lieux.destroy', $lieu->id_lieu) }}" method="POST"
                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce lieu ?');">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-50 border border-red-200 text-red-600 text-sm font-semibold rounded-lg hover:bg-red-100 transition">
                            🗑️ Supprimer
                        </button>
                    </form>
                @endcan
                @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                    <a href="{{ route('lieux.edit', $lieu->id_lieu) }}"
                        class="px-4 py-2 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition">
                        Modifier
                    </a>
                @endcan
            </div>
        </div>

        @if(session('error'))
            <div class="p-4 bg-red-50 text-red-700 rounded-lg border border-red-200 text-sm font-semibold">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 flex items-center gap-4">
                        <div class="p-3 bg-indigo-50 text-indigo-600 rounded-lg">💼</div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Immobilisation</p>
                            <p class="text-sm font-semibold text-slate-900">
                                {{ $lieu->num_inventaire ?? 'Non inventorié' }}
                            </p>
                            <p class="text-xs text-slate-500">{{ $lieu->libelle_comptable ?? 'Aucun libellé associé' }}</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 flex items-center gap-4">
                        <div class="p-3 bg-amber-50 text-amber-600 rounded-lg">📜</div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Décision Réglementaire
                            </p>
                            <p class="text-sm font-semibold text-slate-900">
                                {{ $lieu->numero_decision ?? 'Aucune décision' }}
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ $lieu->date_decision ? \Carbon\Carbon::parse($lieu->date_decision)->format('d/m/Y') : 'Date non définie' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">🚪 Locaux Isolés
                            ({{ $locaux->count() }})</h3>
                        @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                            <a href="{{ route('locaux.create', ['id_lieu' => $lieu->id_lieu]) }}"
                                class="text-xs text-blue-600 font-semibold hover:underline">➕ Ajouter un local</a>
                        @endcan
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse($locaux as $loc)
                            <div class="p-3 hover:bg-slate-50 flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-semibold">{{ $loc->nom_local }}</p>
                                    <p class="text-xs text-slate-500">{{ $loc->libelle_usage ?? 'Usage non défini' }}</p>
                                </div>
                                <a href="{{ route('locaux.show', $loc->id_local) }}" class="text-xs text-blue-600">Voir →</a>
                            </div>
                        @empty
                            <p class="p-4 text-xs text-slate-400 italic">Aucun local bâti dans cet espace.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">⚙️ Équipements Extérieurs
                            ({{ $equipements->count() }})</h3>
                        @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                            <a href="{{ route('equipements.create', ['id_lieu' => $lieu->id_lieu]) }}"
                                class="text-xs text-blue-600 font-semibold hover:underline">➕ Ajouter un équipement</a>
                        @endcan
                    </div>
                    <div class="divide-y divide-slate-100 max-h-48 overflow-y-auto">
                        @forelse($equipements as $equip)
                            <div class="p-3 flex justify-between items-center hover:bg-slate-50">
                                <span class="text-sm font-semibold">{{ $equip->nom_equipement }}</span>
                                <span
                                    class="text-xs px-2 py-1 bg-slate-100 rounded text-slate-600">{{ $equip->etat_fonctionnement ?? 'Opérationnel' }}</span>
                            </div>
                        @empty
                            <p class="p-4 text-xs text-slate-400 italic">Aucun équipement de type mobilier urbain ou jeu
                                répertorié.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200">
                        <h3 class="text-sm font-bold text-slate-800 tracking-tight flex items-center gap-2">
                            📋 Contrôles Réglementaires
                        </h3>
                    </div>
                    <div class="p-4">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="text-slate-400 font-bold border-b border-slate-100 pb-2">
                                    <th class="pb-2">Désignation</th>
                                    <th class="pb-2">Fréquence</th>
                                    <th class="pb-2 text-right">Obligatoire</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($controles as $ctrl)
                                    <tr>
                                        <td class="py-2.5 font-semibold text-slate-800">{{ $ctrl->designation }}</td>
                                        <td class="py-2.5 text-slate-500">{{ $ctrl->frequence_mois }} mois</td>
                                        <td class="py-2.5 text-right font-medium">
                                            @if($ctrl->est_legalement_obligatoire)
                                                <span
                                                    class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-700">OUI</span>
                                            @else
                                                <span
                                                    class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600">NON</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-4 text-center text-slate-400 italic">Aucun contrôle
                                            réglementaire requis pour ce lieu.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if(
                        $emplacements->count() > 0 || str_contains(strtolower($lieu->nom_lieu), 'cimetiere') ||
                        str_contains(strtolower($lieu->nom_lieu), 'cimetière')
                    )
                    <div
                        class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden border-l-4 border-l-purple-500">
                        <div class="p-4 bg-purple-50/50 border-b border-purple-100">
                            <h3 class="text-sm font-bold text-purple-800 flex items-center gap-2">⚰️ Emplacements Funéraires
                                ({{ $emplacements->count() }})</h3>
                        </div>
                        <div class="p-4 text-sm text-slate-600">
                            <p>Ce lieu dispose d'une configuration funéraire. Consultez le module dédié au cimetière pour gérer
                                les concessions et les défunts.</p>
                        </div>
                    </div>
                @endif

            </div>

            <div class="space-y-6">

                <div
                    class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden border-l-4 border-l-green-500">
                    <div class="p-4 bg-green-50/50 border-b border-green-100">
                        <h3 class="text-sm font-bold text-green-800 flex items-center gap-2">🌱 Patrimoine Végétal
                            ({{ $vegetaux->count() }})</h3>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse($vegetaux as $veg)
                            <div class="p-3 flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-semibold">{{ $veg->type_vegetal }}</p>
                                    <p class="text-xs text-slate-500">Espèce : {{ $veg->espece_vegetal ?? 'Non précisée' }}</p>
                                </div>
                                <span class="text-xs font-bold text-green-700 bg-green-100 px-2 py-1 rounded-full">Qté:
                                    {{ $veg->quantite ?? 1 }}</span>
                            </div>
                        @empty
                            <p class="p-4 text-xs text-slate-400 italic">Aucun arbre ou massif végétal recensé individuellement.
                            </p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200">
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">📅 Plan d'Entretien Espaces
                            Verts</h3>
                    </div>
                    <div class="p-4">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="text-slate-400 font-bold border-b border-slate-100 pb-2">
                                    <th class="pb-2">Tâche d'entretien</th>
                                    <th class="pb-2 text-right">Fréquence Standard</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($plans_entretien as $plan)
                                    <tr>
                                        <td class="py-2.5 font-semibold text-slate-700">{{ $plan->libelle_tache }}</td>
                                        <td class="py-2.5 text-right text-slate-500">
                                            {{ $plan->frequence_standard ?? 'Variable' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="py-4 text-center text-slate-400 italic">Aucun plan d'entretien
                                            régulier configuré.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection