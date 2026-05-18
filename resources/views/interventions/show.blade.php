@extends('layouts.app')

@section('title', 'Fiche Intervention #' . $intervention->id_int)

@section('content')
    <div class="max-w-5xl mx-auto">
        <div class="mb-4">
            <a href="{{ route('interventions.index') }}"
                class="text-slate-500 hover:text-slate-800 text-sm flex items-center">
                ← Retour à la liste
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <span class="text-blue-600 font-bold text-sm uppercase tracking-wider">Bon de travaux</span>
                            <h1 class="text-3xl font-extrabold text-slate-900">{{ $intervention->type_intervention }}</h1>
                        </div>

                        {{-- Correction de l'espace/retour à la ligne sur le rôle Responsable technique --}}
                        @if(
                                Auth::user()->role_appli === 'Administrateur' || Auth::user()->role_appli === 'Responsable
                                                technique'
                            )
                            <div class="flex items-center gap-2">
                                <a href="{{ route('interventions.edit', $intervention->id_int) }}"
                                    class="text-xs bg-amber-100 text-amber-700 hover:bg-amber-200 px-3 py-1.5 rounded-lg font-bold transition">
                                    ✏️ Modifier
                                </a>

                                <form action="{{ route('interventions.destroy', $intervention->id_int) }}" method="POST"
                                    onsubmit="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer cette intervention ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-xs bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1.5 rounded-lg font-bold transition">
                                        🗑️ Supprimer
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                    <div class="prose max-w-none text-slate-600 mb-8">
                        <h3 class="text-slate-900 font-semibold">Description du travail à effectuer :</h3>
                        <p class="leading-relaxed">{{ $intervention->description }}</p>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 pt-6 border-t border-slate-100">
                        <div>
                            <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Ouverture</p>
                            <p class="text-slate-800 font-medium mt-1">
                                {{ \Carbon\Carbon::parse($intervention->date_ouverture)->format('d/m/Y') }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Catégorie</p>
                            <p class="text-slate-800 font-medium mt-1">{{ $intervention->categorie->libelle ?? 'N/A' }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Code budget</p>
                            @if($intervention->code_budget)
                                <span
                                    class="inline-block bg-slate-100 text-slate-800 font-bold px-2 py-0.5 rounded border border-slate-200 mt-1">
                                    {{ strtoupper($intervention->code_budget) }}
                                </span>
                            @else
                                <p class="text-slate-400 italic text-sm mt-1">N/A</p>
                            @endif
                        </div>

                        <div>
                            <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Statut global</p>
                            <span
                                class="inline-block mt-1 px-2.5 py-1 text-xs font-bold rounded-full 
                                    {{ $intervention->statut_global === 'Terminée' ? 'bg-green-100 text-green-800' : ($intervention->statut_global === 'En cours' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                {{ $intervention->statut_global }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h2 class="text-lg font-bold text-slate-800 mb-6 border-b pb-2">Historique des interventions sur le
                        terrain</h2>

                    @if($intervention->suiviActions && $intervention->suiviActions->count() > 0)
                        <div class="space-y-6">
                            @foreach($intervention->suiviActions as $action)
                                <div class="flex gap-4 pb-6 border-l-2 border-slate-100 ml-3 pl-6 relative">
                                    <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-blue-500 border-2 border-white">
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start">
                                            <p class="text-sm font-bold text-slate-900">Passage du
                                                {{ \Carbon\Carbon::parse($action->date_action_suivi)->format('d/m/Y') }}
                                            </p>
                                            <span class="text-xs bg-slate-100 px-2 py-1 rounded text-slate-500">
                                                {{ $action->temps_passe_heures }}h passées
                                            </span>
                                        </div>
                                        <p class="text-slate-600 text-sm mt-2 leading-relaxed">{{ $action->description_etape }}</p>
                                        <p class="text-xs mt-2 font-semibold text-blue-600">Statut après action :
                                            {{ $action->statut_apres_action }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-slate-400 italic">Aucun compte-rendu de terrain pour le moment.</p>
                    @endif
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h2 class="text-lg font-bold text-slate-800 mb-4 border-b pb-2">Suivi des étapes</h2>
                    <div class="space-y-4">
                        <div class="flex gap-4">
                            <div class="w-2 h-2 rounded-full bg-blue-500 mt-2"></div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">Intervention générée</p>
                                <p class="text-xs text-slate-500">
                                    {{ \Carbon\Carbon::parse($intervention->date_ouverture)->format('d/m/Y') }} - Système
                                </p>
                            </div>
                        </div>
                        @if($intervention->statut_global === 'Terminée' && $intervention->date_cloture)
                            <div class="flex gap-4">
                                <div class="w-2 h-2 rounded-full bg-green-500 mt-2"></div>
                                <div>
                                    <p class="text-sm font-bold text-slate-800">Intervention clôturée</p>
                                    <p class="text-xs text-slate-500">
                                        {{ \Carbon\Carbon::parse($intervention->date_cloture)->format('d/m/Y') }} - Service
                                        technique
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h3 class="font-bold text-slate-900 mb-4 border-b pb-2">Actions disponibles</h3>

                    @if($intervention->statut_global !== 'Terminée')
                        @if(
                                Auth::user()->role_appli === 'Responsable technique' || Auth::user()->role_appli === 'Technicien' ||
                                Auth::user()->role_appli === 'Administrateur'
                            )
                            <a href="{{ route('interventions.cloturer.form', $intervention->id_int) }}"
                                class="w-full block text-center bg-green-600 text-white font-bold py-3 rounded-lg hover:bg-green-700 transition shadow-md mb-3 text-sm">
                                ✓ Saisir un compte-rendu de terrain
                            </a>
                        @endif
                    @endif

                    <a href="{{ route('interventions.pdf', $intervention->id_int) }}"
                        class="w-full bg-white border border-slate-300 text-slate-700 py-2 rounded-lg hover:bg-slate-50 transition text-sm text-center block font-medium">
                        🖨️ Imprimer le bon (PDF)
                    </a>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h3 class="font-bold text-slate-900 mb-4 border-b pb-2">Localisation immobilière</h3>

                    <div class="space-y-4">
                        <div>
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Local /
                                Emplacement intérieur</span>
                            @if($intervention->local)
                                <p class="text-sm font-semibold text-slate-800 mt-1">🏢 {{ $intervention->local->nom_local }}
                                </p>
                                <span class="text-xs text-slate-500">Niveau : {{ $intervention->local->niveau ?? 'RDC' }} |
                                    Usage : {{ $intervention->local->typeUsage->libelle_usage ?? 'Générique' }}</span>
                            @else
                                <p class="text-xs text-slate-400 italic mt-1">Aucun local intérieur spécifique rattaché.</p>
                            @endif
                        </div>

                        <div class="pt-3 border-t border-slate-100">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Bâtiment
                                communal</span>
                            @if($intervention->batiment)
                                <p class="text-sm font-semibold text-slate-800 mt-1">🏛️ {{ $intervention->batiment->nom_bat }}
                                </p>
                            @elseif($intervention->local && $intervention->local->batiment)
                                {{-- Si l'intervention est liée à un local, on remonte intelligemment au bâtiment parent --}}
                                <p class="text-sm font-semibold text-slate-800 mt-1">🏛️
                                    {{ $intervention->local->batiment->nom_bat }}
                                </p>
                                <span class="text-xs text-slate-400 italic">(Déduit via le local)</span>
                            @else
                                <p class="text-xs text-slate-400 italic mt-1">Aucun bâtiment spécifique lié.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h3 class="font-bold text-slate-900 mb-4 border-b pb-2">Espace Public associé</h3>

                    @if($intervention->lieuxPublicis && $intervention->lieuxPublicis->count() > 0)
                        <ul class="space-y-3">
                            @foreach($intervention->lieuxPublicis as $lieu)
                                <li class="bg-slate-50 p-3 rounded-lg border border-slate-100 text-sm">
                                    <p class="font-bold text-slate-800">🌳 {{ $lieu->nom_lieu }}</p>
                                    <span class="text-xs text-slate-500">Typologie :
                                        {{ $lieu->typologie_lieu ?? 'Espace public' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-xs text-slate-400 italic">Aucun espace ou lieu public extérieur lié.</p>
                    @endif
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h3 class="font-bold text-slate-900 mb-4 border-b pb-2">Équipement(s) technique(s)</h3>

                    @if($intervention->equipements && $intervention->equipements->count() > 0)
                        <ul class="space-y-3">
                            @foreach($intervention->equipements as $equip)
                                <li class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                                    <a href="{{ route('equipements.show', $equip->id_equipement) }}"
                                        class="text-blue-600 font-bold hover:underline block text-sm">
                                        ⚙️ {{ $equip->nom_equipement }}
                                    </a>
                                    <span class="text-xs text-slate-500">Réf :
                                        {{ $equip->reference_serie ?? 'Non renseignée' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-xs text-slate-400 italic">Aucun équipement de l'inventaire lié.</p>
                    @endif
                </div>

                <div class="bg-slate-100 rounded-xl p-6 border border-slate-200">
                    <h3 class="text-sm font-bold text-slate-500 uppercase mb-3">Traçabilité & Origine</h3>
                    @if($intervention->id_sig)
                        <p class="text-sm text-slate-600 mb-2">Origine : Signalement #{{ $intervention->id_sig }}</p>
                        <a href="{{ route('signalements.show', $intervention->id_sig) }}"
                            class="text-blue-600 text-xs font-bold hover:underline">Voir le signalement d'origine →</a>
                    @else
                        <p class="text-xs text-slate-400 italic">Créé manuellement sans signalement d'origine.</p>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection