<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ERP Mairie')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 font-sans antialiased flex h-screen overflow-hidden">

    <aside class="w-64 bg-slate-900 text-white flex flex-col hidden md:flex">
        <div class="h-16 flex items-center px-6 border-b border-slate-800 font-bold text-xl tracking-wider">
            <span class="text-blue-500 mr-2">⚙️</span> ERP MAIRIE
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <a href="{{ route('technique.dashboard') }}"
                class="flex items-center px-4 py-3 bg-blue-600 text-white rounded-lg"><span class="mr-3">📊</span>
                Tableau de bord</a>
            <a href="{{ route('signalements.index') }}"
                class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 rounded-lg"><span
                    class="mr-3">🚨</span> Signalements</a>
            <a href="{{ route('interventions.index') }}"
                class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 rounded-lg"><span
                    class="mr-3">🚧</span> Interventions</a>
            <a href="{{ route('equipements.index') }}"
                class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 rounded-lg"><span
                    class="mr-3">🧰</span> Équipements</a>
        </nav>
        <div class="p-4 border-t border-slate-800 text-sm text-slate-400">
            Agent : Pierre (Technique)
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-hidden">

        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8 shadow-sm z-10">
            <h1 class="text-2xl font-semibold text-slate-800">@yield('header_title', 'Vue d\'ensemble')</h1>
            <div class="flex items-center space-x-4">
                <button class="bg-slate-100 p-2 rounded-full text-slate-500 hover:bg-slate-200">🔔</button>
                <div class="w-8 h-8 bg-blue-500 rounded-full text-white flex items-center justify-center font-bold">P
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-8">
            @yield('content')
        </div>

    </main>
</body>

</html>