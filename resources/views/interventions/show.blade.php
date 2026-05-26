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
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mt-6">
                    <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">📦 Fournitures & Matériels
                            consommés</h3>
                        @php
                            $coutMaterielTotal = $intervention->achatsMateriels->sum(function ($m) {
                                return $m->quantite * $m->prix_unitaire_ht;
                            });
                        @endphp
                        <span class="text-xs font-bold bg-blue-100 text-blue-800 px-2.5 py-1 rounded-full">
                            Total matériel : {{ number_format($coutMaterielTotal, 2, ',', ' ') }} € HT
                        </span>
                    </div>

                    <div class="divide-y divide-slate-100 bg-white">
                        @forelse($intervention->achatsMateriels ?? [] as $mat)
                            <div class="p-4 flex justify-between items-center text-sm">
                                <div>
                                    <p class="font-semibold text-slate-800">{{ $mat->nom_materiel }}</p>
                                    <p class="text-xs text-slate-400">
                                        Quantité : <span class="font-medium text-slate-600">{{ $mat->quantite }}
                                            {{ $mat->unite_mesure }}</span>
                                        | PU : <span
                                            class="font-medium text-slate-600">{{ number_format($mat->prix_unitaire_ht, 2, ',', ' ') }}
                                            €</span>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="font-bold text-slate-900">
                                        {{ number_format($mat->quantite * $mat->prix_unitaire_ht, 2, ',', ' ') }} € HT
                                    </span>
                                    <p class="text-[10px] text-slate-400">Le
                                        {{ \Carbon\Carbon::parse($mat->date_achat)->format('d/m/Y') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="p-6 text-center text-sm text-slate-400 italic">Aucune fourniture enregistrée sur cette
                                intervention.</p>
                        @endforelse
                    </div>

                    @if(auth()->user()->can('check-permission', ['Interventions', 'ecriture']))
                        <div class="p-4 bg-slate-50/60 border-t border-slate-100">
                            <span class="text-xs font-bold uppercase text-slate-400 tracking-wider block mb-3">➕ Ajouter une
                                fourniture / un achat</span>
                            <form action="{{ route('interventions.materiel.store', $intervention->id_int) }}" method="POST"
                                class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                @csrf
                                <div class="sm:col-span-2">
                                    <input type="text" name="nom_materiel" required
                                        placeholder="Désignation (Ex: Vanne PVC, Câble 3G2.5...)"
                                        class="w-full text-xs border border-slate-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <div class="flex gap-1">
                                        <input type="number" step="0.01" name="quantite" required placeholder="Qté"
                                            class="w-2/3 text-xs border border-slate-300 rounded-lg px-2 py-2 outline-none focus:ring-2 focus:ring-blue-500">
                                        <input type="text" name="unite_mesure" placeholder="Unité" value="U"
                                            class="w-1/3 text-xs border border-slate-300 rounded-lg px-1 py-2 text-center bg-white outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>
                                <div>
                                    <input type="number" step="0.01" name="prix_unitaire_ht" required
                                        placeholder="Prix U. HT (€)"
                                        class="w-full text-xs border border-slate-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <input type="hidden" name="date_add" value="{{ now()->format('Y-m-d') }}">
                                <div class="sm:col-span-4 flex justify-between items-center mt-1">
                                    <div class="flex items-center gap-2">
                                        <label class="text-[11px] text-slate-500 font-medium">Date d'utilisation/achat :</label>
                                        <input type="date" name="date_achat" value="{{ now()->format('Y-m-d') }}" required
                                            class="text-xs border border-slate-300 rounded px-2 py-0.5 bg-white text-slate-600">
                                    </div>
                                    <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2 rounded-lg shadow transition">
                                        Ajouter la ligne
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
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



                <div class="bg-white rounded-xl p-6 border border-slate-200 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-800 uppercase mb-4 flex items-center">
                        <span class="mr-2">👷</span> Équipe assignée
                    </h3>
                    @if($intervention->agents && $intervention->agents->count() > 0)
                        <ul class="space-y-3">
                            @foreach($intervention->agents as $agent)
                                <li class="flex items-center gap-3 p-2 bg-slate-50 rounded-lg border border-slate-100">
                                    <div
                                        class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs">
                                        {{ substr($agent->prenom_user, 0, 1) }}{{ substr($agent->nom_user, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800">{{ $agent->prenom_user }} {{ $agent->nom_user }}
                                        </p>
                                        <p class="text-xs text-slate-500">{{ $agent->role_appli ?? 'Agent technique' }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center p-4 bg-slate-50 rounded-lg border border-dashed border-slate-300">
                            <p class="text-sm text-slate-500 italic">Aucun agent assigné pour le moment.</p>
                        </div>
                    @endif
                </div>
                <div class="bg-white rounded-xl p-6 border border-slate-200 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-800 uppercase mb-3 flex items-center">
                        <span class="mr-2">🏢</span> Prestataire Externe
                    </h3>
                    @if($intervention->tiers)
                        <div class="p-3 bg-indigo-50 border border-indigo-100 rounded-lg">
                            <p class="text-sm font-bold text-indigo-900">
                                {{ $intervention->tiers->nom_tiers }}
                            </p>
                            @if($intervention->tiers->telephone || $intervention->tiers->email)
                                <div class="mt-2 text-xs text-indigo-700 space-y-1 border-t border-indigo-200/60 pt-2">
                                    @if($intervention->tiers->telephone)
                                    <p>📞 {{ $intervention->tiers->telephone }}</p> @endif
                                    @if($intervention->tiers->email)
                                    <p>✉️ {{ $intervention->tiers->email }}</p> @endif
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="p-3 bg-emerald-50 border border-emerald-100 rounded-lg flex items-center gap-2">
                            <span class="text-emerald-600 text-xs">✔️</span>
                            <p class="text-xs font-semibold text-emerald-800">Intervention prise en charge en Régie Interne</p>
                        </div>
                    @endif
                </div>
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">📸 Photos & Bons
                            d'intervention</h2>

                        <ul class="space-y-3 mb-6">
                            @forelse($documents as $doc)
                                <li
                                    class="flex items-center justify-between p-3 bg-slate-50 border border-slate-100 rounded-lg transition hover:bg-slate-100">
                                    <div class="flex items-center gap-3">
                                        <span class="text-2xl">
                                            {{ in_array(strtolower($doc->type_doc), ['pdf']) ? '📄' : (in_array(strtolower($doc->type_doc), ['jpg', 'png', 'jpeg']) ? '🖼️' : '📁') }}
                                        </span>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800 line-clamp-1">{{ $doc->nom_fichier }}
                                            </p>
                                            <p class="text-xs text-slate-500">
                                                {{ \Carbon\Carbon::parse($doc->date_upload)->format('d/m/Y') }} •
                                                {{ number_format($doc->taille_ko, 0, ',', ' ') }} Ko
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <a href="{{ asset('storage/' . $doc->chemin_stockage) }}" target="_blank"
                                            class="text-blue-600 hover:text-blue-800 text-xs font-bold bg-blue-50 px-2 py-1 rounded border border-blue-100">
                                            Voir
                                        </a>
                                        @can('check-permission', ['Patrimoine & Equipements', 'suppression'])
                                            <form action="{{ route('documents.global.destroy', $doc->id_document) }}" method="POST"
                                                class="inline" onsubmit="return confirm('Supprimer cette pièce jointe ?');">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-800 text-xs font-bold bg-red-50 px-2 py-1 rounded border border-red-100">
                                                    ❌
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </li>
                            @empty
                                <li class="text-sm text-slate-400 italic text-center py-4">Aucune photo ou document lié à cette
                                    intervention.</li>
                            @endforelse
                        </ul>
                    </div>

                    @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                        <form action="{{ route('interventions.documents.store', $intervention->id_int) }}" method="POST"
                            enctype="multipart/form-data"
                            class="bg-slate-50 p-4 rounded-lg border border-slate-200 border-dashed mt-auto">
                            @csrf

                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Ajouter une pièce
                                jointe</label>
                            <p class="text-[10px] text-slate-500 mb-3">Formats acceptés : PDF, JPG, PNG, DOC, DOCX. (Max : 5 Mo)
                            </p>

                            <div class="flex items-start gap-2">
                                <div class="w-full">
                                    <input type="file" name="fichier" required
                                        class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:font-semibold file:bg-slate-900 file:text-white hover:file:bg-slate-800 cursor-pointer focus:outline-none">

                                    @error('fichier')
                                        <p class="text-xs text-red-600 font-bold mt-2">⚠️ {{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit"
                                    class="px-3 py-1.5 bg-indigo-600 text-white font-bold rounded-md hover:bg-indigo-700 transition text-xs whitespace-nowrap">
                                    📤 Envoyer
                                </button>
                            </div>
                        </form>
                    @endcan
                </div>
                <div class="bg-slate-100 rounded-xl p-6 border border-slate-200">
                    <h3 class="text-sm font-bold text-slate-500 uppercase mb-3">Traçabilité & Origine</h3>
                    @if($intervention->id_action)
                        <p class="text-sm text-slate-600 mb-2">Origine : action #{{ $intervention->id_action }}</p>
                        <a href="{{ route('actions.show', $intervention->id_action) }}"
                            class="text-blue-600 text-xs font-bold hover:underline">Voir le action d'origine →</a>
                    @else
                        <p class="text-xs text-slate-400 italic">Créé manuellement sans action d'origine.</p>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection