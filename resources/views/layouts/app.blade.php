<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ERP Mairie')</title>
    <script src="https://cdn.tailwindcss.com"></script>

    @yield('styles')
    <style>
    /* Petite transition pour la flèche des menus */
    .arrow-icon {
        transition: transform 0.2s ease-in-out;
    }

    .rotate-90 {
        transform: rotate(90deg);
    }
    </style>
</head>

<body class="bg-slate-50 font-sans antialiased flex h-screen overflow-hidden">

    <aside class="w-72 bg-slate-900 text-slate-300 flex flex-col hidden md:flex shadow-xl z-20">
        <div class="h-16 flex items-center px-6 border-b border-slate-800 font-bold text-xl tracking-wider text-white">
            <svg class="w-6 h-6 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                </path>
            </svg>
            GESTION DINGY 360
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto custom-scrollbar">

            <a href="{{ route('technique.dashboard') }}"
                class="flex items-center px-4 py-3 mb-4 rounded-lg transition-colors font-medium {{ request()->routeIs('technique.dashboard') ? 'bg-blue-600 text-white shadow-md' : 'hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('technique.dashboard') ? 'text-white' : 'text-slate-400' }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                    </path>
                </svg>
                Tableau de bord
            </a>
            <a href="{{ route('cartographie.index') }}"
                class="flex items-center px-4 py-3 mb-4 rounded-lg transition-colors font-medium {{ request()->routeIs('cartographie.index') ? 'bg-blue-600 text-white shadow-md' : 'hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                    </path>
                </svg>
                Atlas (Cartographie)
            </a>

            <div>
                <button onclick="toggleMenu('menu-interventions')"
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg transition-colors font-medium hover:bg-slate-800 hover:text-white {{ request()->routeIs('actions.*', 'interventions.*', 'projets.*') ? 'text-white' : '' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Suivi & Travaux
                    </div>
                    <svg id="arrow-menu-interventions"
                        class="w-4 h-4 arrow-icon {{ request()->routeIs('actions.*', 'interventions.*', 'projets.*') ? 'rotate-90' : '' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                <div id="menu-interventions"
                    class="flex flex-col pl-12 pr-4 py-1 space-y-1 {{ request()->routeIs('actions.*', 'interventions.*', 'projets.*') ? '' : 'hidden' }}">
                    <a href="{{ route('actions.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('actions.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">actions
                        Citoyens</a>
                    <a href="{{ route('interventions.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('interventions.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Bons
                        d'interventions</a>
                    <a href="{{ route('projets.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('projets.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Projets</a>
                </div>
            </div>
            <div>
                <button onclick="toggleMenu('menu-decisions')"
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg transition-colors font-medium hover:bg-slate-800 hover:text-white {{ request()->routeIs('decisions-admin.*', 'decisions-commission.*') ? 'text-white' : '' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Actes & Décisions
                    </div>
                    <svg id="arrow-menu-decisions"
                        class="w-4 h-4 arrow-icon {{ request()->routeIs('decisions-admin.*', 'decisions-commission.*') ? 'rotate-90' : '' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                <div id="menu-decisions"
                    class="flex flex-col pl-12 pr-4 py-1 space-y-1 {{ request()->routeIs('decisions-admin.*', 'decisions-commission.*') ? '' : 'hidden' }}">
                    <a href="{{ route('decisions-admin.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('decisions-admin.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Registre
                        des Actes</a>

                    <a href="{{ route('decisions-commission.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('decisions-commission.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Arbitrages
                        Commissions</a>
                </div>
            </div>
            <div>
                <button onclick="toggleMenu('menu-batiments')"
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg transition-colors font-medium hover:bg-slate-800 hover:text-white {{ request()->routeIs('batiments.*', 'lieux.*', 'locaux.*', 'equipements.*', 'compteurs.*', 'supports-acces.*', 'types-erp.*', 'controles.*') ? 'bg-slate-800 text-white' : 'text-slate-400' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                        Bâtiments & Locaux
                    </div>
                    <svg id="arrow-menu-batiments"
                        class="w-4 h-4 arrow-icon transition-transform {{ request()->routeIs('batiments.*', 'lieux.*', 'locaux.*', 'equipements.*', 'compteurs.*', 'supports-acces.*', 'types-erp.*', 'controles.*') ? 'rotate-90' : '' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

                <div id="menu-batiments"
                    class="flex flex-col pl-12 pr-4 py-1 space-y-1 {{ request()->routeIs('batiments.*', 'lieux.*', 'locaux.*', 'equipements.*', 'compteurs.*', 'supports-acces.*', 'types-erp.*', 'controles.*') ? '' : 'hidden' }}">
                    <a href="{{ route('batiments.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('batiments.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Bâtiments</a>
                    <a href="{{ route('lieux.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('lieux.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Lieux
                        publics</a>
                    <a href="{{ route('types-erp.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('types-erp.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Types
                        ERP</a>
                    <a href="{{ route('controles.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('controles.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Contrôles
                        réglementaires</a>
                    <a href="{{ route('locaux.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('locaux.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Locaux
                        & Pièces</a>
                    <a href="{{ route('equipements.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('equipements.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Équipements</a>
                    <a href="{{ route('compteurs.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('compteurs.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Compteurs</a>
                    <a href="{{ route('supports-acces.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('supports-acces.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Supports
                        & Clés</a>
                </div>
            </div>
            <div>
                <button onclick="toggleMenu('menu-cimetiere')"
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg transition-colors font-medium hover:bg-slate-800 hover:text-white {{ request()->routeIs('emplacements.*', 'concessions.*') ? 'text-white' : '' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                            </path>
                        </svg>
                        Espace Funéraire
                    </div>
                    <svg id="arrow-menu-cimetiere"
                        class="w-4 h-4 arrow-icon {{ request()->routeIs('emplacements.*', 'concessions.*') ? 'rotate-90' : '' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                <div id="menu-cimetiere"
                    class="flex flex-col pl-12 pr-4 py-1 space-y-1 {{ request()->routeIs('emplacements.*', 'concessions.*') ? '' : 'hidden' }}">
                    <a href="{{ route('emplacements.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('emplacements.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Cimetières
                        & Emplacements</a>
                    <a href="{{ route('concessions.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('concessions.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Concessions</a>
                </div>
            </div>
            <div>
                <button onclick="toggleMenu('menu-admin')"
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg transition-colors font-medium hover:bg-slate-800 hover:text-white {{ request()->routeIs('tiers.*', 'contrats.*', 'immobilisations.*', 'dossiers-financiers.*', 'enveloppes-budgetaires.*', 'operations-comptables.*', 'chapitres.*', 'articles-compta.*') ? 'text-white' : '' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        Finances & Tiers
                    </div>
                    <svg id="arrow-menu-admin"
                        class="w-4 h-4 arrow-icon {{ request()->routeIs('tiers.*', 'contrats.*', 'immobilisations.*', 'dossiers-financiers.*', 'enveloppes-budgetaires.*', 'operations-comptables.*', 'chapitres.*', 'articles-compta.*') ? 'rotate-90' : '' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                <div id="menu-admin"
                    class="flex flex-col pl-12 pr-4 py-1 space-y-1 {{ request()->routeIs('tiers.*', 'contrats.*', 'immobilisations.*', 'dossiers-financiers.*', 'enveloppes-budgetaires.*', 'operations-comptables.*', 'chapitres.*', 'articles-compta.*') ? '' : 'hidden' }}">

                    <a href="{{ route('tiers.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('tiers.*') && !request()->routeIs('tiers.entreprises') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Annuaire
                        Citoyens</a>

                    <a href="{{ route('tiers.entreprises') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('entreprises.*') || request()->routeIs('tiers.entreprises') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Annuaire
                        Entreprises</a>

                    <a href="{{ route('contrats.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('contrats.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Contrats
                        & Engagements</a>

                    <a href="{{ route('immobilisations.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('immobilisations.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Inventaire
                        & Immobilisations</a>

                    <a href="{{ route('chapitres.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('chapitres.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Nomenclature
                        Chapitres</a>

                    <a href="{{ route('articles-compta.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('articles-compta.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Plan
                        de Comptes</a>

                    <a href="{{ route('enveloppes-budgetaires.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('enveloppes-budgetaires.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Enveloppes
                        Budgétaires</a>

                    <a href="{{ route('operations-comptables.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('operations-comptables.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Opérations
                        Comptables</a>

                    <a href="{{ route('dossiers-financiers.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('dossiers-financiers.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Dossiers
                        Financiers</a>
                </div>
            </div>
            <div>
                <button onclick="toggleMenu('menu-territoire')"
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg transition-colors font-medium hover:bg-slate-800 hover:text-white {{ request()->routeIs('lieux-dits.*', 'secteurs.*', 'zones.*', 'parcelles.*') ? 'text-white' : '' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 4ll-6-3">
                            </path>
                        </svg>
                        Territoire & Cadastre
                    </div>
                    <svg id="arrow-menu-territoire"
                        class="w-4 h-4 arrow-icon {{ request()->routeIs('lieux-dits.*', 'secteurs.*', 'zones.*', 'parcelles.*') ? 'rotate-90' : '' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                <div id="menu-territoire"
                    class="flex flex-col pl-12 pr-4 py-1 space-y-1 {{ request()->routeIs('lieux-dits.*', 'secteurs.*', 'zones.*', 'parcelles.*') ? '' : 'hidden' }}">
                    <a href="{{ route('lieux-dits.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('lieux-dits.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Lieux-dits</a>
                    <a href="{{ route('secteurs.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('secteurs.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Secteurs</a>
                    <a href="{{ route('zones.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('zones.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Zones</a>
                    <a href="{{ route('parcelles.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('parcelles.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Parcelles
                        Cadastrales</a>
                </div>
            </div>

            <div>
                <button onclick="toggleMenu('menu-urbanisme')"
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg transition-colors font-medium hover:bg-slate-800 hover:text-white {{ request()->routeIs('dossiers-urba.*') ? 'text-white' : '' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Urbanisme
                    </div>
                    <svg id="arrow-menu-urbanisme"
                        class="w-4 h-4 arrow-icon {{ request()->routeIs('dossiers-urba.*') ? 'rotate-90' : '' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                <div id="menu-urbanisme"
                    class="flex flex-col pl-12 pr-4 py-1 space-y-1 {{ request()->routeIs('dossiers-urba.*') ? '' : 'hidden' }}">
                    <a href="{{ route('dossiers-urba.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('dossiers-urba.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Dossiers
                        d'Urbanisme</a>
                </div>
            </div>

            <div>
                <button onclick="toggleMenu('menu-voirie')"
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg transition-colors font-medium hover:bg-slate-800 hover:text-white {{ request()->routeIs('voies.*', 'troncons.*', 'ouvrages.*', 'communes.*') ? 'text-white' : '' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Voirie & Réseaux
                    </div>
                    <svg id="arrow-menu-voirie"
                        class="w-4 h-4 arrow-icon {{ request()->routeIs('voies.*', 'troncons.*', 'ouvrages.*', 'communes.*') ? 'rotate-90' : '' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                <div id="menu-voirie"
                    class="flex flex-col pl-12 pr-4 py-1 space-y-1 {{ request()->routeIs('voies.*', 'troncons.*', 'ouvrages.*', 'communes.*') ? '' : 'hidden' }}">

                    <a href="{{ route('voies.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('voies.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">
                        Voies (Axes principaux)
                    </a>

                    <a href="{{ route('troncons.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('troncons.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">
                        Tronçons & Chemins
                    </a>

                    <a href="{{ route('ouvrages.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('ouvrages.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">
                        Ouvrages d'art
                    </a>

                    <a href="{{ route('communes.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('communes.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">
                        Communes partenaires
                    </a>
                </div>
            </div>
            @if(Auth::user()->role_appli === 'Administrateur')
            <div>
                <button onclick="toggleMenu('menu-agents')"
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg transition-colors font-medium hover:bg-slate-800 hover:text-white {{ request()->routeIs('utilisateurs.*', 'admin.habilitations.*') ? 'text-white' : '' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                        Gestion des Droits
                    </div>
                    <svg id="arrow-menu-agents"
                        class="w-4 h-4 arrow-icon {{ request()->routeIs('utilisateurs.*', 'admin.habilitations.*') ? 'rotate-90' : '' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                <div id="menu-agents"
                    class="flex flex-col pl-12 pr-4 py-1 space-y-1 {{ request()->routeIs('utilisateurs.*', 'admin.habilitations.*') ? '' : 'hidden' }}">
                    <a href="{{ route('utilisateurs.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('utilisateurs.index') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Liste
                        des agents</a>
                    <a href="{{ route('utilisateurs.create') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('utilisateurs.create') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Créer
                        un agent</a>
                    <a href="{{ route('admin.habilitations.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('admin.habilitations.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Droits
                        & Accès (Habilitations)</a>
                </div>
            </div>
            @endif

        </nav>

        <div class="p-4 border-t border-slate-800 bg-slate-900/50 flex justify-between items-center">
            <a href="{{ route('profil.show') }}" class="flex items-center overflow-hidden group flex-1"
                title="Voir mon profil">
                <div
                    class="w-9 h-9 flex-shrink-0 bg-blue-600 rounded-full text-white flex items-center justify-center font-bold mr-3 uppercase shadow-inner group-hover:bg-blue-500 transition-colors">
                    {{ substr(Auth::user()->prenom_user ?? 'U', 0, 1) }}
                </div>
                <div class="truncate">
                    <p class="font-bold text-sm text-slate-200 truncate group-hover:text-white transition-colors">
                        {{ Auth::user()->prenom_user ?? 'User' }} {{ Auth::user()->nom_user ?? '' }}
                    </p>
                    <p class="text-[10px] uppercase tracking-wider text-blue-400 font-semibold truncate">
                        {{ Auth::user()->role_appli ?? 'Agent' }} ⚙️
                    </p>
                </div>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="ml-2 flex-shrink-0">
                @csrf
                <button type="submit"
                    class="p-2 text-slate-500 hover:text-red-400 hover:bg-slate-800 rounded-lg transition"
                    title="Se déconnecter">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-hidden">

        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8 shadow-sm z-10">
            <h1 class="text-2xl font-semibold text-slate-800 tracking-tight">@yield('header_title', 'Vue d\'ensemble')
            </h1>

            <div class="flex items-center space-x-4">
                <a href="{{ route('profil.show') }}"
                    class="flex items-center text-sm font-medium text-slate-600 hover:text-blue-600 transition px-3 py-1.5 rounded-lg hover:bg-slate-50 {{ request()->routeIs('profil.*') ? 'text-blue-600 bg-blue-50/50' : '' }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Mon Compte
                </a>

                <button
                    class="bg-slate-50 border border-slate-100 p-2 rounded-full text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                        </path>
                    </svg>
                    <span
                        class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                </button>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-8 bg-slate-50/50">
            @yield('content')
        </div>

    </main>

    <script>
    function toggleMenu(menuId) {
        const menu = document.getElementById(menuId);
        const arrow = document.getElementById('arrow-' + menuId);

        menu.classList.toggle('hidden');
        arrow.classList.toggle('rotate-90');
    }
    </script>
    @yield('scripts')
</body>

</html>