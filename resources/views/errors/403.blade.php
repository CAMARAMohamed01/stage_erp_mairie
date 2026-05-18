@extends('layouts.app')

@section('title', 'Accès Refusé')

@section('content')
    <div class="max-w-md mx-auto my-16 text-center">
        <div
            class="inline-flex items-center justify-center w-16 h-16 bg-red-50 text-red-600 rounded-full mb-6 border border-red-100 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                class="w-8 h-8">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.249-8.25-3.286Zm0 13.036h.008v.008H12v-.008Z" />
            </svg>
        </div>

        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Action non autorisée</h1>
        <p class="text-slate-500 mt-2 text-sm leading-relaxed">
            Désolé, votre compte d'agent communal ne dispose pas des privilèges requis dans la matrice des habilitations
            pour effectuer cette action.
        </p>

        <div class="mt-4 p-3 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-500 italic">
            En cas de besoin, contactez l'Administrateur pour modifier vos droits sur le module concerné.
        </div>

        <div class="mt-8">
            <button onclick="history.back()"
                class="inline-flex items-center justify-center px-5 py-2.5 bg-slate-900 text-white font-semibold text-sm rounded-lg hover:bg-slate-800 transition shadow-sm">
                ← Revenir à la page précédente
            </button>
        </div>
    </div>
@endsection