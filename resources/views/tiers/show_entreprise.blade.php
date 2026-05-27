@extends('layouts.app')

@section('title', 'Dossier Entreprise : ' . ($entreprise->morale->raison_sociale ?? 'Inconnue'))

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">

        <div class="flex justify-between items-center">
            <a href="{{ route('tiers.entreprises') }}"
                class="text-slate-500 hover:text-slate-800 text-sm flex items-center font-medium transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Retour à l'annuaire des entreprises
            </a>

            <div class="flex gap-2">
                <a href="{{ route('tiers.edit_entreprise', $entreprise->id_tiers) }}"
                    class="px-4 py-2 bg-amber-100 text-amber-700 border border-amber-200 font-semibold text-sm rounded-lg hover:bg-amber-200 transition">
                    ✏️ Modifier
                </a>
                <form action="{{ route('tiers.destroy', $entreprise->id_tiers) }}" method="POST"
                    onsubmit="return confirm('Voulez-vous vraiment supprimer cette entreprise de la base de données ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 bg-red-100 text-red-700 border border-red-200 font-semibold text-sm rounded-lg hover:bg-red-200 transition">
                        🗑️ Supprimer
                    </button>
                </form>
            </div>
        </div>

        <div
            class="bg-white rounded-xl border border-slate-200 shadow-sm p-8 flex flex-wrap gap-8 justify-between items-start">
            <div class="flex items-center gap-5">
                <div
                    class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-2xl font-bold shadow-inner uppercase">
                    {{ substr($entreprise->morale->raison_sociale ?? 'E', 0, 2) }}
                </div>
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                        {{ $entreprise->morale->raison_sociale ?? 'Entreprise Inconnue' }}
                    </h1>
                    <p class="text-slate-500 font-medium mt-1 flex items-center gap-2">
                        Personne Morale #{{ $entreprise->id_tiers }}
                        @if($entreprise->morale->alias_tiers)
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-[10px] uppercase font-bold rounded">
                                Alias : {{ $entreprise->morale->alias_tiers }}
                            </span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="bg-slate-50 p-5 rounded-lg border border-slate-100 min-w-[280px]">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 border-b border-slate-200 pb-1">
                    Identité Légale
                </h3>
                <div class="space-y-3 mb-5">
                    <div class="flex flex-col text-slate-700 text-sm font-medium">
                        <span class="text-[10px] text-slate-400 uppercase">SIRET</span>
                        <span class="font-mono">{{ $entreprise->morale->siret ?? 'Non renseigné' }}</span>
                    </div>
                    <div class="flex flex-col text-slate-700 text-sm font-medium">
                        <span class="text-[10px] text-slate-400 uppercase">TVA Intracommunautaire</span>
                        <span class="font-mono">{{ $entreprise->morale->numero_tva_intra ?? 'Non renseigné' }}</span>
                    </div>
                    @if($entreprise->morale->num_compte_client)
                        <div class="flex flex-col text-slate-700 text-sm font-medium">
                            <span class="text-[10px] text-slate-400 uppercase">N° Compte Client</span>
                            <span class="font-mono">{{ $entreprise->morale->num_compte_client }}</span>
                        </div>
                    @endif
                </div>

                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 border-b border-slate-200 pb-1">
                    Contact Privilégié
                </h3>
                <div class="space-y-2">
                    @if($entreprise->morale->nom_contact)
                        <div class="flex items-center text-slate-700 text-sm font-medium">
                            <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            {{ $entreprise->morale->nom_contact }}
                        </div>
                    @endif

                    @if($entreprise->tel_tiers)
                        <div class="flex items-center text-slate-700 text-sm font-medium">
                            <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                </path>
                            </svg>
                            {{ $entreprise->tel_tiers }}
                        </div>
                    @endif

                    @if($entreprise->email_tiers)
                        <div class="flex items-center text-slate-700 text-sm font-medium">
                            <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                            <a href="mailto:{{ $entreprise->email_tiers }}"
                                class="text-blue-600 hover:underline">{{ $entreprise->email_tiers }}</a>
                        </div>
                    @endif

                    @if(!$entreprise->tel_tiers && !$entreprise->email_tiers && !$entreprise->morale->nom_contact)
                        <p class="text-sm text-slate-400 italic">Aucune coordonnée renseignée.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-8 py-5 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                <h2 class="text-lg font-bold text-slate-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                        </path>
                    </svg>
                    Comptes Bancaires
                </h2>
                <a href="{{ route('tiers.comptes.create', $entreprise->id_tiers) }}"
                    class="px-3 py-1.5 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-lg hover:bg-emerald-200 transition shadow-sm">
                    + Ajouter un compte
                </a>
            </div>

            <div class="p-6">
                @if(isset($entreprise->comptesBancaires) && $entreprise->comptesBancaires->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($entreprise->comptesBancaires as $compte)
                            <div
                                class="p-4 bg-slate-50 border border-slate-200 rounded-lg shadow-sm relative overflow-hidden group">
                                <div class="relative z-10">
                                    <p class="text-xs font-bold uppercase text-slate-500 mb-1">Titulaire</p>
                                    <p class="text-sm font-semibold text-slate-800 mb-3">{{ $compte->titulaire_compte }}</p>

                                    <div class="bg-white p-2 rounded border border-slate-100 mb-2">
                                        <p class="text-[10px] text-slate-400 uppercase">IBAN</p>
                                        <p class="font-mono text-sm text-slate-900 tracking-wide">{{ $compte->iban }}</p>
                                    </div>

                                    <div class="flex justify-between items-center mt-2">
                                        <div>
                                            <p class="text-[10px] text-slate-400 uppercase">BIC</p>
                                            <p class="font-mono text-sm text-slate-700">{{ $compte->bic }}</p>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            @if($compte->documents && $compte->documents->count() > 0)
                                                <div class="text-xs">
                                                    @foreach($compte->documents as $doc)
                                                        <a href="{{ asset('storage/' . $doc->chemin_stockage) }}" target="_blank"
                                                            class="inline-flex items-center px-2 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded hover:bg-emerald-100 transition"
                                                            title="{{ $doc->nom_fichier }}">
                                                            📎 Voir RIB
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif

                                            <form action="{{ route('tiers.comptes.destroy', $compte->id_compte) }}" method="POST"
                                                class="inline-block" onsubmit="return confirm('Supprimer ce compte bancaire ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-slate-400 hover:text-red-500 transition"
                                                    title="Supprimer le compte">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center p-6 border-2 border-dashed border-slate-200 rounded-lg bg-slate-50">
                        <p class="text-sm text-slate-500 font-medium">Aucun compte bancaire enregistré pour cette entreprise.
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-8 py-5 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                <h2 class="text-lg font-bold text-slate-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13">
                        </path>
                    </svg>
                    Documents rattachés (KBIS, Contrats, etc.)
                </h2>
                <a href="{{ route('tiers.documents.create', $entreprise->id_tiers) }}"
                    class="px-3 py-1.5 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-lg hover:bg-emerald-200 transition shadow-sm">
                    + Téléverser un document
                </a>
            </div>

            @if(isset($entreprise->documents) && $entreprise->documents->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider">
                                <th class="px-6 py-4 font-semibold">Fichier</th>
                                <th class="px-6 py-4 font-semibold">Type</th>
                                <th class="px-6 py-4 font-semibold">Date d'ajout</th>
                                <th class="px-6 py-4 font-semibold text-right">Taille</th>
                                <th class="px-6 py-4 font-semibold text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($entreprise->documents as $doc)
                                <tr class="hover:bg-slate-50 transition-colors text-sm">
                                    <td class="px-6 py-4 font-medium text-slate-800 flex items-center gap-2">
                                        📄 {{ $doc->nom_fichier }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">
                                        <span
                                            class="px-2 py-1 bg-slate-100 text-slate-600 text-[10px] uppercase font-bold rounded">{{ $doc->type_doc }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-500">
                                        {{ \Carbon\Carbon::parse($doc->date_upload)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-500 text-right">
                                        {{ $doc->taille_ko }} Ko
                                    </td>
                                    <td class="px-6 py-4 text-right flex justify-end items-center gap-4">
                                        <a href="{{ asset('storage/' . $doc->chemin_stockage) }}" target="_blank"
                                            class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase tracking-wider">
                                            Voir ↓
                                        </a>

                                        <form action="{{ route('documents.global.destroy', $doc->id_document) }}" method="POST"
                                            onsubmit="return confirm('Voulez-vous vraiment supprimer ce document ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-500 hover:text-red-700 font-bold text-xs uppercase tracking-wider">
                                                Supprimer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-8 py-10 text-center border-t border-slate-100">
                    <p class="text-slate-500 font-medium">Aucun document n'est rattaché à ce dossier entreprise.</p>
                </div>
            @endif
        </div>
    </div>
@endsection