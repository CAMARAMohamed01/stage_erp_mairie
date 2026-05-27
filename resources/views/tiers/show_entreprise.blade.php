@extends('layouts.app')

@section('title', 'Dossier Prestataire : ' . ($entreprise->morale->raison_sociale ?? 'Inconnu'))

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">

        <div
            class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-wrap justify-between items-start gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <span class="text-3xl">🏢</span>
                    <h1 class="text-2xl font-bold text-slate-800">
                        {{ $entreprise->morale->raison_sociale ?? 'Raison sociale non renseignée' }}
                    </h1>
                    @if($entreprise->morale->alias_tiers)
                        <span
                            class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-slate-100 text-slate-600 uppercase tracking-wider border border-slate-200">
                            {{ $entreprise->morale->alias_tiers }}
                        </span>
                    @endif
                </div>
                <p class="text-slate-500 font-medium ml-11 text-sm">
                    ID Tiers : #{{ $entreprise->id_tiers }} |
                    N° Compte Client : <span
                        class="font-mono text-slate-700">{{ $entreprise->morale->num_compte_client ?? 'N/A' }}</span>
                </p>
            </div>

            <div class="flex items-center gap-2 text-sm">
                <a href="{{ route('tiers.entreprises') }}"
                    class="px-4 py-2 border border-slate-300 text-slate-700 font-semibold rounded-lg hover:bg-slate-50 transition">
                    ← Retour
                </a>

                <a href="{{ route('tiers.edit_entreprise', $entreprise->id_tiers) }}"
                    class="px-4 py-2 bg-emerald-100 text-emerald-700 border border-emerald-200 font-semibold rounded-lg hover:bg-emerald-200 transition">
                    ✏️ Modifier
                </a>

                <form action="{{ route('tiers.destroy', $entreprise->id_tiers) }}" method="POST"
                    onsubmit="return confirm('Attention, la suppression de ce prestataire est définitive. Continuer ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 bg-red-100 text-red-700 border border-red-200 font-semibold rounded-lg hover:bg-red-200 transition">
                        🗑️ Supprimer
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-blue-800 uppercase tracking-wider border-b border-emerald-100 pb-2 mb-4">
                    ⚖️ Informations Légales
                </h3>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between border-b border-slate-50 pb-2">
                        <span class="text-slate-500">Raison Sociale</span>
                        <span class="font-semibold text-slate-800">{{ $entreprise->morale->raison_sociale ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-50 pb-2">
                        <span class="text-slate-500">N° SIRET</span>
                        <span
                            class="font-mono font-semibold text-slate-800">{{ $entreprise->morale->siret ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">TVA Intracommunautaire</span>
                        <span
                            class="font-mono font-semibold text-slate-800">{{ $entreprise->morale->numero_tva_intra ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-blue-800 uppercase tracking-wider border-b border-blue-100 pb-2 mb-4">
                    📞 Informations de Contact
                </h3>

                <div class="space-y-3 text-sm">
                    <div class="flex items-center gap-3 border-b border-slate-50 pb-2">
                        <span class="text-slate-400">👤</span>
                        <div class="flex-1">
                            <p class="text-xs text-slate-500">Contact Principal</p>
                            <p class="font-semibold text-slate-800">
                                {{ $entreprise->morale->nom_contact ?? 'Aucun contact défini' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 border-b border-slate-50 pb-2">
                        <span class="text-slate-400">📞</span>
                        <div class="flex-1">
                            <p class="text-xs text-slate-500">Téléphone</p>
                            <p class="font-semibold text-slate-800">{{ $entreprise->tel_tiers ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-slate-400">✉️</span>
                        <div class="flex-1">
                            <p class="text-xs text-slate-500">Email</p>
                            @if($entreprise->email_tiers)
                                <a href="mailto:{{ $entreprise->email_tiers }}"
                                    class="font-semibold text-blue-600 hover:underline">{{ $entreprise->email_tiers }}</a>
                            @else
                                <p class="font-semibold text-slate-800">N/A</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex justify-between items-center mb-4 border-b pb-2">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">
                    💶 Comptes Bancaires & Coordonnées
                </h3>
                <button
                    class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-bold rounded hover:bg-slate-200 transition">
                    + Ajouter un compte
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($entreprise->comptesBancaires as $compte)
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg shadow-sm relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-16 h-16 bg-slate-200 rounded-bl-full opacity-50 -z-0"></div>

                        <div class="relative z-10">
                            <p class="text-xs font-bold uppercase text-slate-500 mb-1">Titulaire du compte</p>
                            <p class="text-sm font-semibold text-slate-800 mb-3">{{ $compte->titulaire_compte }}</p>

                            <div class="bg-white p-2 rounded border border-slate-100 mb-2">
                                <p class="text-[10px] text-slate-400 uppercase">IBAN</p>
                                <p class="font-mono text-sm text-slate-900 tracking-wide">{{ $compte->iban }}</p>
                            </div>

                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-[10px] text-slate-400 uppercase">BIC</p>
                                    <p class="font-mono text-sm text-slate-700">{{ $compte->bic }}</p>
                                </div>

                                @if($compte->documents && $compte->documents->count() > 0)
                                    <div class="text-xs">
                                        @foreach($compte->documents as $doc)
                                            <a href="#"
                                                class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-700 border border-blue-100 rounded hover:bg-blue-100 transition"
                                                title="{{ $doc->nom_fichier }}">
                                                📎 Voir RIB
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-full p-6 text-center text-slate-400 italic bg-slate-50 rounded-lg border border-dashed border-slate-300">
                        Aucun compte bancaire enregistré pour cette entreprise.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
@endsection