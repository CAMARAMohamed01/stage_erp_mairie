@extends('layouts.app')

@section('title', 'Tableau de bord - Technique')
@section('header_title', 'Vue d\'ensemble opérationnelle')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        <div onclick="window.location='{{ route('actions.index') }}'"
            class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm flex items-center justify-between hover:shadow-md hover:border-amber-300 transition cursor-pointer select-none group">
            <div>
                <div
                    class="text-slate-500 text-sm font-bold uppercase tracking-wider mb-1 group-hover:text-amber-600 transition">
                    Actions</div>
                <div class="text-3xl font-extrabold text-slate-800">
                    {{ count($nouveauxactions) }}
                    <span class="text-sm font-medium text-slate-400 ml-1 font-sans">nouveaux</span>
                </div>
            </div>
            <div
                class="w-12 h-12 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center group-hover:scale-110 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                    </path>
                </svg>
            </div>
        </div>

        <div onclick="window.location='{{ route('interventions.index') }}'"
            class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm flex items-center justify-between hover:shadow-md hover:border-blue-300 transition cursor-pointer select-none group">
            <div>
                <div
                    class="text-slate-500 text-sm font-bold uppercase tracking-wider mb-1 group-hover:text-blue-600 transition">
                    Chantiers</div>
                <div class="text-3xl font-extrabold text-slate-800">
                    {{ count($interventionsEnCours) }}
                    <span class="text-sm font-medium text-slate-400 ml-1 font-sans">en cours</span>
                </div>
            </div>
            <div
                class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center group-hover:scale-110 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                    </path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
        </div>

        <div onclick="window.location='{{ route('equipements.index') }}'"
            class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm flex items-center justify-between hover:shadow-md hover:border-red-300 transition cursor-pointer select-none group">
            <div>
                <div
                    class="text-slate-500 text-sm font-bold uppercase tracking-wider mb-1 group-hover:text-red-600 transition">
                    Équipements</div>
                <div class="text-3xl font-extrabold text-red-600">
                    {{ count($equipementsEnPanne) }}
                    <span class="text-sm font-medium text-slate-400 ml-1 font-sans">HS</span>
                </div>
            </div>
            <div
                class="w-12 h-12 bg-red-100 text-red-600 rounded-full flex items-center justify-center group-hover:scale-110 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
            </div>
        </div>

        <div
            class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm flex items-center justify-between hover:shadow-md transition select-none">
            <div>
                <div class="text-slate-500 text-sm font-bold uppercase tracking-wider mb-1">Contrôles</div>
                <div class="text-3xl font-extrabold text-slate-800">
                    {{ count($controles) }}
                    <span class="text-sm font-medium text-slate-400 ml-1 font-sans">à venir</span>
                </div>
            </div>
            <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                    </path>
                </svg>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex justify-between items-center">
                <h2 class="font-bold text-slate-800 text-base flex items-center">
                    <span class="w-2 h-2 rounded-full bg-amber-500 mr-2"></span>
                    🚨 Dernières actions
                </h2>
                <div class="flex items-center gap-3">
                    <a href="{{ route('actions.create') }}"
                        class="text-xs bg-blue-600 text-white px-3 py-1.5 rounded-lg font-bold hover:bg-blue-700 transition shadow-sm">
                        + Saisir
                    </a>
                    <a href="{{ route('actions.index') }}"
                        class="text-xs font-bold text-slate-500 hover:text-blue-600 transition">Voir tout →</a>
                </div>
            </div>
            <div class="flex-1 p-0">
                <ul class="divide-y divide-slate-100 text-sm">
                    @forelse($nouveauxactions as $action)
                        <li class="hover:bg-slate-50 transition-colors cursor-pointer"
                            onclick="window.location='{{ route('actions.show', $action->id_action) }}'">
                            <div class="px-6 py-4">
                                <div class="font-bold text-slate-800 truncate mb-1">{{ $action->description }}</div>
                                <div class="text-slate-500 flex justify-between items-center text-xs">
                                    <span class="flex items-center font-medium">
                                        <svg class="w-3.5 h-3.5 mr-1 text-slate-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                            </path>
                                        </svg>
                                        {{ $action->categorie->libelle ?? 'Non catégorisé' }}
                                    </span>
                                    <x-badge type="priorite" :value="$action->priorite" />
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-6 py-12 text-slate-400 text-center flex flex-col items-center justify-center">
                            <svg class="w-8 h-8 mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <p class="font-medium">Aucune nouvelle action à traiter.</p>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="bg-blue-50 px-6 py-4 border-b border-blue-100 flex justify-between items-center">
                <h2 class="font-bold text-blue-900 text-base flex items-center">
                    <span class="w-2 h-2 rounded-full bg-blue-500 mr-2 animate-pulse"></span>
                    🚧 Interventions en cours
                </h2>
                <a href="{{ route('interventions.index') }}"
                    class="text-xs font-bold text-blue-600 hover:text-blue-800 transition">Voir tout →</a>
            </div>
            <div class="flex-1 p-0">
                <ul class="divide-y divide-slate-100 text-sm">
                    @forelse($interventionsEnCours as $int)
                        <li class="hover:bg-blue-50/30 transition-colors cursor-pointer"
                            onclick="window.location='{{ route('interventions.show', $int->id_int) }}'">
                            <div class="px-6 py-4">
                                <div class="flex justify-between items-start mb-1">
                                    <span class="font-bold text-slate-800">{{ $int->type_intervention }}</span>
                                    <span
                                        class="text-xs font-mono font-bold bg-blue-100 text-blue-700 px-2 py-0.5 rounded">#{{ $int->id_int }}</span>
                                </div>
                                <div class="flex justify-between items-center mt-2 text-slate-500 text-xs font-medium">
                                    <span>Ouverte le {{ \Carbon\Carbon::parse($int->date_ouverture)->format('d/m/Y') }}</span>
                                    <span class="text-orange-500 font-semibold">{{ $int->statut_global }}</span>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-6 py-12 text-slate-400 text-center flex flex-col items-center justify-center">
                            <svg class="w-8 h-8 mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                </path>
                            </svg>
                            <p class="font-medium">Aucune intervention active en cours.</p>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="bg-red-50 px-6 py-4 border-b border-red-100 flex justify-between items-center">
                <h2 class="font-bold text-red-900 text-base flex items-center">
                    <span class="w-2 h-2 rounded-full bg-red-500 mr-2"></span>
                    Équipements Hors Service
                </h2>
                <a href="{{ route('equipements.index') }}"
                    class="text-xs font-bold text-red-600 hover:text-red-800 transition">Voir l'inventaire →</a>
            </div>
            <div class="p-0">
                <table class="w-full text-left text-sm">
                    <tbody class="divide-y divide-slate-100">
                        @forelse($equipementsEnPanne as $equipement)
                            <tr class="hover:bg-slate-50 transition-colors cursor-pointer"
                                onclick="window.location='{{ route('equipements.show', $equipement->id_equipement) }}'">
                                <td class="px-6 py-4 font-bold text-slate-800">{{ $equipement->nom_equipement }}</td>
                                <td class="px-6 py-4 text-slate-500 text-right text-xs font-semibold">
                                    {{ $equipement->marque ?? 'Réf. N/A' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-8 text-slate-400 text-center italic font-medium">Tous les
                                    équipements sont fonctionnels.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-6">
            <div
                class="bg-gradient-to-r from-slate-900 to-slate-800 text-white p-6 rounded-xl border border-slate-700 shadow-md flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold tracking-tight">Moteur de Planification Préventive</h3>
                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">Analyse les fréquences des contrôles
                        réglementaires pour automatiser la création des interventions d'entretien.</p>
                </div>
                <a href="{{ route('admin.preventif.generer') }}"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-lg shadow-sm transition flex-shrink-0 flex items-center gap-2">
                    🔄 Lancer le moteur
                </a>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                    <h2 class="font-bold text-slate-800 text-base flex items-center">
                        <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        Contrôles Réglementaires
                    </h2>
                </div>
                <div class="p-0">
                    <table class="w-full text-left text-sm">
                        <tbody class="divide-y divide-slate-100">
                            @forelse($controles as $controle)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-slate-700 text-xs">{{ $controle->designation }}</td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <span
                                            class="bg-slate-100 text-slate-600 border px-2 py-1 rounded font-mono font-bold text-[11px]">
                                            Tous les {{ $controle->frequence_mois }} mois
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-8 text-slate-400 text-center italic font-medium">Aucune
                                        échéance paramétrée.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection