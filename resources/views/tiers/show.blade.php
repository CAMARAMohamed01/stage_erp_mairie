@extends('layouts.app')

@section('title', 'Dossier Citoyen : ' . ($citoyen->physique->nom_tiers ?? 'Inconnu'))

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">

        <div>
            <a href="{{ route('tiers.index') }}"
                class="text-slate-500 hover:text-slate-800 text-sm flex items-center font-medium transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Retour à l'annuaire
            </a>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8 flex justify-between items-start">
            <div class="flex items-center gap-5">
                <div
                    class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-2xl font-bold shadow-inner">
                    {{ substr($citoyen->physique->prenom_tiers ?? 'X', 0, 1) }}{{ substr($citoyen->physique->nom_tiers ?? 'X', 0, 1) }}
                </div>
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                        {{ $citoyen->physique->prenom_tiers ?? '' }}
                        {{ $citoyen->physique->nom_tiers ?? 'Citoyen Inconnu' }}
                    </h1>
                    <p class="text-slate-500 font-medium mt-1">
                        Citoyen répertorié #{{ $citoyen->id_tiers }}
                    </p>
                </div>
            </div>

            <div class="bg-slate-50 p-4 rounded-lg border border-slate-100 min-w-[250px]">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Moyens de contact</h3>
                <div class="space-y-2">
                    @if($citoyen->tel_tiers)
                        <div class="flex items-center text-slate-700 text-sm font-medium">
                            <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                </path>
                            </svg>
                            {{ $citoyen->tel_tiers }}
                        </div>
                    @endif

                    @if($citoyen->email_tiers)
                        <div class="flex items-center text-slate-700 text-sm font-medium">
                            <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                            {{ $citoyen->email_tiers }}
                        </div>
                    @endif

                    @if(!$citoyen->tel_tiers && !$citoyen->email_tiers)
                        <p class="text-sm text-slate-400 italic">Aucune coordonnée renseignée.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-8 py-5 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                <h2 class="text-lg font-bold text-slate-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                        </path>
                    </svg>
                    Historique des requêtes
                </h2>
                <span class="text-sm text-slate-500 font-medium">{{ $citoyen->signalements->count() }} demande(s) au
                    total</span>
            </div>

            @if($citoyen->signalements->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider">
                                <th class="px-6 py-4 font-semibold">Date</th>
                                <th class="px-6 py-4 font-semibold">N°</th>
                                <th class="px-6 py-4 font-semibold">Description de la demande</th>
                                <th class="px-6 py-4 font-semibold">Statut</th>
                                <th class="px-6 py-4 font-semibold text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($citoyen->signalements as $signalement)
                                <tr class="hover:bg-slate-50 transition-colors text-sm">
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-600 font-medium">
                                        {{ \Carbon\Carbon::parse($signalement->date_creation)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 font-bold text-slate-400">
                                        #{{ $signalement->id_sig }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-slate-900 font-medium truncate max-w-xs"
                                            title="{{ $signalement->description }}">
                                            {{ Str::limit($signalement->description, 50) }}
                                        </div>
                                        <div class="text-xs text-slate-500 mt-1">Via {{ $signalement->mode_reception }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <x-badge type="statut" :value="$signalement->statut_signalement" />
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('signalements.show', $signalement->id_sig) }}"
                                            class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase tracking-wider">
                                            Consulter →
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-8 py-12 text-center">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 text-slate-400 mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                            </path>
                        </svg>
                    </div>
                    <p class="text-slate-500 font-medium">Ce citoyen n'a aucun signalement dans son historique pour le moment.
                    </p>
                </div>
            @endif
        </div>

    </div>
@endsection