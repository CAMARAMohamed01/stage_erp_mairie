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

                @if(auth()->user()->can('check-permission', ['Patrimoine', 'ecriture']))
                    <a href="{{ route('compteurs.edit', $compteur->id_compteur) }}"
                        class="px-4 py-2 bg-amber-100 text-amber-700 border border-amber-200 text-sm font-semibold rounded-lg hover:bg-amber-200 transition">
                        ✏️ Modifier
                    </a>
                @endif

                @if(auth()->user()->can('check-permission', ['Patrimoine', 'suppression']))
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

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">📜 Administration</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between flex-col">
                        <span class="text-slate-500 font-medium mb-1">Contrat lié :</span>
                        @if($compteur->contrat)
                            <div class="bg-slate-50 p-2 rounded border border-slate-100">
                                <p class="font-bold text-blue-700">N° {{ $compteur->contrat->numero_contrat }}</p>
                                <p class="text-xs text-slate-600">Fournisseur :
                                    {{ $compteur->contrat->tiers->raison_sociale ?? 'Non défini' }}
                                </p>
                            </div>
                        @else
                            <span class="italic text-amber-600">Aucun contrat de fourniture rattaché</span>
                        @endif
                    </div>

                    @if($compteur->id_compteur_principal)
                        <div class="mt-4">
                            <span class="text-slate-500 font-medium">Sous-compteur de :</span>
                            <p class="font-semibold text-slate-800 mt-1">➡️
                                {{ $compteur->compteurPrincipal->point_comptage ?? 'Inconnu' }}
                            </p>
                        </div>
                    @elseif($compteur->sousCompteurs->count() > 0)
                        <div class="mt-4">
                            <span class="text-slate-500 font-medium">Ce compteur alimente les sous-compteurs suivants :</span>
                            <ul class="list-disc list-inside text-slate-700 mt-1">
                                @foreach($compteur->sousCompteurs as $sousCompteur)
                                    <li>{{ $sousCompteur->point_comptage }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>

            @if($compteur->observations)
                <div class="bg-amber-50 p-6 rounded-xl border border-amber-100 shadow-sm">
                    <h2 class="text-sm font-bold text-amber-800 mb-2">Observations</h2>
                    <p class="text-sm text-amber-900">{{ $compteur->observations }}</p>
                </div>
            @endif

        </div>
    </div>
@endsection