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
            ERP MAIRIE
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
                <span class="w-5 h-5 mr-3 ">🌍</span>
                Cartographie Globale
            </a>

            <div class="px-4 pb-2 pt-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Patrimoine &
                Interventions</div>
            <div>
                <button onclick="toggleMenu('menu-territoire')"
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg transition-colors font-medium hover:bg-slate-800 hover:text-white {{ request()->routeIs('secteurs.*', 'zones.*', 'parcelles.*') ? 'text-white' : '' }}">
                    <div class="flex items-center">
                        <span class="text-xl mr-3 opacity-80">🗺️</span> Territoire & Cadastre
                    </div>
                    <svg id="arrow-menu-territoire"
                        class="w-4 h-4 arrow-icon {{ request()->routeIs('secteurs.*', 'zones.*', 'parcelles.*') ? 'rotate-90' : '' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                <div id="menu-territoire"
                    class="flex flex-col pl-12 pr-4 py-1 space-y-1 {{ request()->routeIs('secteurs.*', 'zones.*', 'parcelles.*') ? '' : 'hidden' }}">
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
                <button onclick="toggleMenu('menu-voirie')"
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg transition-colors font-medium hover:bg-slate-800 hover:text-white {{ request()->routeIs('voies.*', 'ouvrages.*', 'communes.*') ? 'text-white' : '' }}">
                    <div class="flex items-center">
                        <span class="text-xl mr-3 opacity-80">🛣️</span> Voirie & Réseaux
                    </div>
                    <svg id="arrow-menu-voirie"
                        class="w-4 h-4 arrow-icon {{ request()->routeIs('voies.*', 'ouvrages.*', 'communes.*') ? 'rotate-90' : '' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                <div id="menu-voirie"
                    class="flex flex-col pl-12 pr-4 py-1 space-y-1 {{ request()->routeIs('voies.*', 'ouvrages.*', 'communes.*') ? '' : 'hidden' }}">
                    <a href="{{ route('voies.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('voies.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Voies
                        & Tronçons</a>
                    <a href="{{ route('ouvrages.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('ouvrages.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Ouvrages
                        d'art</a>
                    <a href="{{ route('communes.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('communes.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Communes
                        partenaires</a>
                </div>
            </div>

            <div>
                <button onclick="toggleMenu('menu-batiments')"
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg transition-colors font-medium hover:bg-slate-800 hover:text-white {{ request()->routeIs('batiments.*', 'lieux.*', 'locaux.*', 'equipements.*', 'compteurs.*') ? 'text-white' : '' }}">
                    <div class="flex items-center">
                        <span class="text-xl mr-3 opacity-80">🏢</span> Bâtiments & Locaux
                    </div>
                    <svg id="arrow-menu-batiments"
                        class="w-4 h-4 arrow-icon {{ request()->routeIs('batiments.*', 'lieux.*', 'locaux.*', 'equipements.*', 'compteurs.*') ? 'rotate-90' : '' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                <div id="menu-batiments"
                    class="flex flex-col pl-12 pr-4 py-1 space-y-1 {{ request()->routeIs('batiments.*', 'lieux.*', 'locaux.*', 'equipements.*', 'compteurs.*') ? '' : 'hidden' }}">
                    <a href="{{ route('batiments.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('batiments.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Bâtiments</a>
                    <a href="{{ route('lieux.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('lieux.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Lieux
                        publics</a>
                    <a href="{{ route('locaux.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('locaux.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Locaux
                        & Pièces</a>
                    <a href="{{ route('equipements.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('equipements.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Équipements</a>
                    <a href="{{ route('compteurs.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('compteurs.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Compteurs</a>
                </div>
            </div>

            <div>
                <button onclick="toggleMenu('menu-cimetiere')"
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg transition-colors font-medium hover:bg-slate-800 hover:text-white {{ request()->routeIs('emplacements.*', 'concessions.*') ? 'text-white' : '' }}">
                    <div class="flex items-center">
                        <span class="text-xl mr-3 opacity-80">🕊️</span> Espaces Funéraires
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
                <button onclick="toggleMenu('menu-interventions')"
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg transition-colors font-medium hover:bg-slate-800 hover:text-white {{ request()->routeIs('actions.*', 'interventions.*') ? 'text-white' : '' }}">
                    <div class="flex items-center">
                        <span class="text-xl mr-3 opacity-80">🛠️</span> Suivi & Travaux
                    </div>
                    <svg id="arrow-menu-interventions"
                        class="w-4 h-4 arrow-icon {{ request()->routeIs('actions.*', 'interventions.*') ? 'rotate-90' : '' }}"
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

            <div class="px-4 pb-2 pt-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Administration
            </div>

            <div>
                <button onclick="toggleMenu('menu-admin')"
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg transition-colors font-medium hover:bg-slate-800 hover:text-white {{ request()->routeIs('tiers.*', 'contrats.*', 'dossiers-financiers.*') ? 'text-white' : '' }}">
                    <div class="flex items-center">
                        <span class="text-xl mr-3 opacity-80">📂</span> Finances & Tiers
                    </div>
                    <svg id="arrow-menu-admin"
                        class="w-4 h-4 arrow-icon {{ request()->routeIs('tiers.*', 'contrats.*', 'dossiers-financiers.*') ? 'rotate-90' : '' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                <div id="menu-admin"
                    class="flex flex-col pl-12 pr-4 py-1 space-y-1 {{ request()->routeIs('tiers.*', 'contrats.*', 'dossiers-financiers.*') ? '' : 'hidden' }}">
                    <a href="{{ route('tiers.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('tiers.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Annuaire
                        Citoyens</a>
                    <a href="{{ route('tiers.entreprises') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('entreprises.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Annuaire
                        Entreprises</a>
                    <a href="{{ route('contrats.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('contrats.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Contrats
                        & Engagements</a>
                    <a href="{{ route('dossiers-financiers.index') }}"
                        class="text-sm py-2 transition-colors {{ request()->routeIs('dossiers-financiers.*') ? 'text-white font-bold' : 'text-slate-400 hover:text-white' }}">Dossiers
                        Financiers</a>
                </div>
            </div>

            @if(Auth::user()->role_appli === 'Administrateur')
                <div class="pt-2">
                    <a href="{{ route('admin.habilitations.index') }}"
                        class="flex items-center px-4 py-3 rounded-lg transition-colors font-medium {{ request()->routeIs('admin.habilitations.*') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <span class="text-xl mr-3 opacity-80">🔐</span>
                        Paramètres d'accès
                    </a>
                </div>
            @endif

        </nav>

        <div class="p-4 border-t border-slate-800 bg-slate-900/50 flex justify-between items-center">
            <div class="flex items-center overflow-hidden">
                <div
                    class="w-9 h-9 flex-shrink-0 bg-blue-600 rounded-full text-white flex items-center justify-center font-bold mr-3 uppercase shadow-inner">
                    {{ substr(Auth::user()->prenom_user ?? 'U', 0, 1) }}
                </div>
                <div class="truncate">
                    <p class="font-bold text-sm text-slate-200 truncate">
                        {{ Auth::user()->prenom_user ?? 'Non' }} {{ Auth::user()->nom_user ?? 'connecté' }}
                    </p>
                    <p class="text-[10px] uppercase tracking-wider text-blue-400 font-semibold truncate">
                        {{ Auth::user()->role_appli ?? 'Inconnu' }}
                    </p>
                </div>
            </div>

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
            // Récupérer le menu et la flèche
            const menu = document.getElementById(menuId);
            const arrow = document.getElementById('arrow-' + menuId);

            // Basculer la visibilité et la rotation
            menu.classList.toggle('hidden');
            arrow.classList.toggle('rotate-90');
        }
    </script>
    @yield('scripts')
</body>

</html>