<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - ERP Mairie</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 h-screen flex items-center justify-center">

    <div class="max-w-md w-full bg-white rounded-xl shadow-lg overflow-hidden border border-slate-200">

        <div class="bg-slate-900 p-6 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-800 text-blue-500 mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                    </path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-white tracking-wider">ERP MAIRIE</h2>
            <p class="text-slate-400 text-sm mt-1">Espace de connexion</p>
        </div>

        <div class="p-8">
            <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
                @csrf

                @error('emailpro')
                    <div class="bg-red-50 text-red-600 p-3 rounded-lg text-sm border border-red-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Adresse Email</label>
                    <input type="email" name="emailpro" value="{{ old('emailpro') }}" required autofocus
                        class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors bg-slate-50"
                        placeholder="agent@mairie.fr">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Mot de passe</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors bg-slate-50"
                        placeholder="••••••••">
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-700 transition shadow-md transform hover:-translate-y-0.5">
                    Se connecter
                </button>
            </form>
        </div>

    </div>

</body>

</html>