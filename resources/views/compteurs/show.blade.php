@extends('layouts.app')

@section('header_title', 'Détails du Compteur')

@section('content')
    <div class="max-w-5xl mx-auto pb-12 space-y-6">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <div>
                <div class="flex items-center gap-3">
                    <div class="text-3xl">
                        @if($compteur->type_reseau == 'Électricité') ⚡
                        @elseif($compteur->type_reseau == 'Eau Potable') 💧
                        @elseif($compteur->type_reseau == 'Gaz') 🔥
                        @else ⚙️ @endif
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $compteur->point_comptage }}</h1>
                        <p class="text-sm font-mono text-slate-500 mt-1">Série :
                            {{ $compteur->numero_compteur ?? 'Non défini' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('compteurs.index') }}"
                    class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 transition">
                    ← Retour
                </a>

                <a href="{{ route('compteurs.releves.index', $compteur->id_compteur) }}"
                    class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition">
                    📊 Historique
                </a>
                <a href="{{ route('compteurs.releves.export.pdf', $compteur->id_compteur) }}"
                    class="px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition flex items-center gap-2 shadow-sm">
                    📄 Exporter en PDF
                </a>

                @if(auth()->user()->can('check-permission', ['Patrimoine & Équipements', 'ecriture']))
                    <a href="{{ route('compteurs.edit', $compteur->id_compteur) }}"
                        class="px-4 py-2 bg-amber-100 text-amber-700 border border-amber-200 text-sm font-semibold rounded-lg hover:bg-amber-200 transition">
                        ✏️ Modifier
                    </a>
                @endif

                @if(auth()->user()->can('check-permission', ['Patrimoine & Équipements', 'suppression']))
                    <form action="{{ route('compteurs.destroy', $compteur->id_compteur) }}" method="POST" class="inline"
                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce compteur ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-100 text-red-700 border border-red-200 text-sm font-semibold rounded-lg hover:bg-red-200 transition">
                            🗑️ Supprimer
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">⚙️ Caractéristiques
                    Techniques</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-medium">Type de réseau :</span>
                        <span class="font-semibold text-slate-800">{{ $compteur->type_reseau }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-medium">Unité de mesure :</span>
                        <span class="text-slate-800">{{ $compteur->unite_mesure ?? 'Non définie' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-medium">Date de pose :</span>
                        <span
                            class="text-slate-800">{{ $compteur->date_pose ? \Carbon\Carbon::parse($compteur->date_pose)->format('d/m/Y') : 'Inconnue' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 font-medium">Statut :</span>
                        @if($compteur->date_arret && \Carbon\Carbon::parse($compteur->date_arret)->isPast())
                            <span class="text-red-700 bg-red-50 font-bold px-2 py-0.5 rounded text-xs">DÉPOSÉ (le
                                {{ \Carbon\Carbon::parse($compteur->date_arret)->format('d/m/Y') }})</span>
                        @else
                            <span class="text-green-700 bg-green-50 font-bold px-2 py-0.5 rounded text-xs">EN SERVICE</span>
                        @endif
                    </div>
                    @if($compteur->dessert_tout_le_batiment)
                        <div
                            class="mt-2 p-2 bg-blue-50 border border-blue-100 rounded text-blue-800 text-xs font-bold text-center">
                            Dessert l'intégralité du bâtiment
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">📍 Emplacement</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-medium">Bâtiment :</span>
                        <span
                            class="font-bold text-slate-800">{{ $compteur->local->batiment->nom_bat ?? 'Non défini' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-medium">Local :</span>
                        <span class="text-slate-800">{{ $compteur->local->nom_local ?? 'Non défini' }}</span>
                    </div>
                    <div class="flex justify-between flex-col mt-2">
                        <span class="text-slate-500 font-medium mb-1">Localisation exacte (Vanne / Disjoncteur) :</span>
                        <span class="bg-slate-50 p-2 rounded text-slate-700 border border-slate-100">
                            {{ $compteur->localisation_vanne_arret ?? 'Aucune précision' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm md:col-span-2">
                <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">📜 Administration</h2>
                <div class="space-y-3 text-sm grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <span class="text-slate-500 font-medium mb-1 block">Contrat lié :</span>
                        @if($compteur->contrat)
                            <div class="bg-slate-50 p-2 rounded border border-slate-100">
                                <p class="font-bold text-blue-700">N° {{ $compteur->contrat->numero_contrat }}</p>
                                <p class="text-xs text-slate-600">Fournisseur :
                                    {{ $compteur->contrat->tiers->raison_sociale ?? 'Non défini' }}
                                </p>
                            </div>
                        @else
                            <span class="italic text-amber-600">Aucun contrat rattaché</span>
                        @endif
                    </div>

                    <div>
                        @if($compteur->id_compteur_principal)
                            <span class="text-slate-500 font-medium block">Sous-compteur de :</span>
                            <p class="font-semibold text-slate-800 mt-1">➡️
                                {{ $compteur->compteurPrincipal->point_comptage ?? 'Inconnu' }}
                            </p>
                        @elseif($compteur->sousCompteurs->count() > 0)
                            <span class="text-slate-500 font-medium block">Alimente les sous-compteurs :</span>
                            <ul class="list-disc list-inside text-slate-700 mt-1">
                                @foreach($compteur->sousCompteurs as $sousCompteur)
                                    <li>{{ $sousCompteur->point_comptage }}</li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-slate-500 italic block">Aucune hiérarchie (Compteur unique)</span>
                        @endif
                    </div>
                </div>
            </div>

        </div> @if($compteur->observations)
            <div class="bg-amber-50 p-6 rounded-xl border border-amber-100 shadow-sm w-full">
                <h2 class="text-sm font-bold text-amber-800 mb-2">Observations</h2>
                <p class="text-sm text-amber-900">{{ $compteur->observations }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">📂 Documents Techniques
                    </h2>

                    <ul class="space-y-3 mb-6">
                        @forelse($compteur->documents as $doc)
                            <li class="flex items-center justify-between p-3 bg-slate-50 border border-slate-100 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl">
                                        {{ in_array(strtolower($doc->type_doc), ['pdf']) ? '📄' : (in_array(strtolower($doc->type_doc), ['jpg', 'png', 'jpeg']) ? '🖼️' : '📁') }}
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">{{ $doc->nom_fichier }}</p>
                                        <p class="text-xs text-slate-500">
                                            {{ \Carbon\Carbon::parse($doc->date_upload)->format('d/m/Y') }} •
                                            {{ number_format($doc->taille_ko, 0, ',', ' ') }} Ko
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <a href="{{ asset('storage/' . $doc->chemin_stockage) }}" target="_blank"
                                        class="text-blue-600 hover:text-blue-800 text-xs font-bold bg-blue-50 px-3 py-1 rounded border border-blue-100 transition">
                                        Ouvrir
                                    </a>

                                    @if(auth()->user()->can('check-permission', ['Patrimoine & Equipements', 'suppression']))
                                        <form action="{{ route('compteurs.documents.destroy', $doc->id_document) }}" method="POST"
                                            class="inline"
                                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce document ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-800 text-xs font-bold bg-red-50 px-3 py-1 rounded border border-red-100 transition">
                                                ❌
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </li>
                        @empty
                            <li class="text-sm text-slate-400 italic text-center py-4">Aucun document rattaché à ce compteur.
                            </li>
                        @endforelse
                    </ul>
                </div>

                @if(auth()->user()->can('check-permission', ['Patrimoine & Equipements', 'ecriture']))
                    <form action="{{ route('compteurs.documents.store', $compteur->id_compteur) }}" method="POST"
                        enctype="multipart/form-data"
                        class="bg-slate-50 p-4 rounded-lg border border-slate-200 border-dashed mt-auto">
                        @csrf
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Ajouter un fichier (PDF, JPG,
                            PNG)</label>
                        <div class="flex items-center gap-2">
                            <input type="file" name="fichier" required
                                class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-slate-900 file:text-white hover:file:bg-slate-800 cursor-pointer">
                            <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition text-sm whitespace-nowrap">
                                📤 Envoyer
                            </button>
                        </div>
                    </form>
                @endif
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">🛠️ Interventions
                    Techniques</h2>

                <div class="divide-y divide-slate-100">
                    @forelse($compteur->interventions as $intervention)
                        <div class="py-3">
                            <div class="flex justify-between items-start mb-1">
                                <span class="font-bold text-sm text-slate-800">{{ $intervention->type_intervention }}</span>
                                <span
                                    class="text-[10px] font-bold uppercase px-2 py-0.5 rounded {{ $intervention->statut_global == 'Clôturée' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $intervention->statut_global }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-600 line-clamp-2">{{ $intervention->description }}</p>
                            <p class="text-[10px] text-slate-400 mt-2 font-mono">
                                Ouverte le : {{ \Carbon\Carbon::parse($intervention->date_ouverture)->format('d/m/Y') }}
                            </p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400 italic text-center py-6">Aucune intervention technique enregistrée pour
                            ce compteur.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
@endsection