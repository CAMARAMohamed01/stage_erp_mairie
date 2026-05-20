@extends('layouts.app')

@section('header_title', 'Détails de la concession')

@section('content')
    <div class="max-w-5xl mx-auto pb-12 space-y-6">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Fiche Concession</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Contrat n° {{ $concession->contrat->numero_contrat ?? 'Non défini' }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('concessions.index') }}"
                    class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 transition">
                    ← Retour
                </a>

                @if(auth()->user()->can('check-permission', ['État Civil & Cimetières', 'ecriture']))
                    <a href="{{ route('concessions.edit', $concession->id_concession) }}"
                        class="px-4 py-2 bg-amber-100 text-amber-700 border border-amber-200 text-sm font-semibold rounded-lg hover:bg-amber-200 transition">
                        ✏️ Modifier
                    </a>
                @endif

                @if(auth()->user()->can('check-permission', ['État Civil & Cimetières', 'suppression']))
                    <form action="{{ route('concessions.destroy', $concession->id_concession) }}" method="POST" class="inline"
                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement cette concession ? L\'emplacement redeviendra libre.');">
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
                <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">📍 Localisation &
                    Emplacement</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-medium">Cimetière :</span>
                        <span
                            class="font-semibold text-slate-800">{{ $concession->emplacement->lieu->nom_lieu ?? 'Non renseigné' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-medium">Référence :</span>
                        <span
                            class="font-mono bg-slate-100 px-2 py-0.5 rounded text-slate-700">{{ $concession->emplacement->reference_emplacement }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-medium">Type :</span>
                        <span class="text-slate-800">{{ $concession->emplacement->type_emplacement }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-medium">Capacité totale :</span>
                        <span class="text-slate-800">{{ $concession->emplacement->capacite_max }} place(s)</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">📜 Acte Juridique</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-medium">Titulaire (Acquéreur) :</span>
                        <span class="font-bold text-blue-700">
                            {{ $concession->contrat->tiers->raison_sociale ?? ($concession->contrat->tiers->nom_tiers . ' ' . $concession->contrat->tiers->prenom_tiers) }}
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-slate-500 font-medium">Date de début :</span>
                        <span class="text-slate-800">
                            {{ $concession->contrat->date_debut_contrat ? \Carbon\Carbon::parse($concession->contrat->date_debut_contrat)->format('d/m/Y') : 'Non définie' }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 font-medium">Échéance :</span>
                        @if($concession->contrat->date_echeance)
                            @php
                                $isExpired = \Carbon\Carbon::parse($concession->contrat->date_echeance)->isPast();
                            @endphp
                            <span
                                class="font-bold {{ $isExpired ? 'text-red-600 bg-red-50 px-2 py-0.5 rounded' : 'text-slate-800' }}">
                                {{ \Carbon\Carbon::parse($concession->contrat->date_echeance)->format('d/m/Y') }}
                                {!! $isExpired ? ' (ÉCHUE)' : '' !!}
                            </span>
                        @else
                            <span class="italic text-slate-500">Perpétuelle</span>
                        @endif
                    </div>

                    @if($concession->beneficiaires_autorises)
                        <div class="pt-2">
                            <span class="text-slate-500 font-medium block mb-1">Bénéficiaires autorisés :</span>
                            <p class="text-slate-700 bg-slate-50 p-2 rounded border border-slate-100">
                                {{ $concession->beneficiaires_autorises }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="md:col-span-2 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">⚰️ Personnes Inhumées
                    ({{ $concession->defunts->count() }} / {{ $concession->emplacement->capacite_max }})</h2>

                @if($concession->defunts->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($concession->defunts as $defunt)
                            <div class="flex items-center gap-3 bg-slate-50 border border-slate-100 p-3 rounded-lg">
                                <div class="text-2xl"></div>
                                <div>
                                    <p class="font-bold text-slate-800 text-sm">{{ $defunt->nom_tiers }} {{ $defunt->prenom_tiers }}
                                    </p>
                                    @if($defunt->date_naissance)
                                        <p class="text-xs text-slate-500">Né(e) le
                                            {{ \Carbon\Carbon::parse($defunt->date_naissance)->format('d/m/Y') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-slate-500 text-sm italic py-4 text-center bg-slate-50 rounded-lg">Aucun corps n'est
                        actuellement inhumé dans cet emplacement.</p>
                @endif
            </div>

            @if($concession->commentaire_concession)
                <div class="md:col-span-2 bg-amber-50 p-6 rounded-xl border border-amber-100 shadow-sm">
                    <h2 class="text-sm font-bold text-amber-800 mb-2">Observations de la mairie</h2>
                    <p class="text-sm text-amber-900">{{ $concession->commentaire_concession }}</p>
                </div>
            @endif

        </div>
    </div>
@endsection