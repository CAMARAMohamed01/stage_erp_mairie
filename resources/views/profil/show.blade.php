@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md my-6">
        <div class="border-b pb-4 mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Mon Profil Utilisateur</h2>
            <p class="text-sm text-gray-500">Gérez vos informations de compte et vos accès applicatifs.</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('profil.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-md mb-4 text-sm">
                <div>
                    <span class="font-semibold text-gray-600">Service affecté :</span>
                    <span class="text-gray-800">{{ $user->nom_service ?? 'Non affecté' }}</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-600">Rôle système :</span>
                    <span
                        class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">{{ $user->role_appli }}</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nom</label>
                    <input type="text" name="nom_user" value="{{ $user->nom_user }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Prénom</label>
                    <input type="text" name="prenom_user" value="{{ $user->prenom_user }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Email professionnel</label>
                <input type="email" name="emailpro" value="{{ $user->emailpro }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="bg-yellow-50 p-4 rounded-md border border-yellow-200 mt-6 space-y-3">
                <h3 class="text-sm font-medium text-yellow-800">Changer de mot de passe (optionnel)</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-gray-600">Nouveau mot de passe</label>
                        <input type="password" name="password"
                            class="mt-1 block w-full rounded-md border-gray-300 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600">Confirmer le mot de passe</label>
                        <input type="password" name="password_confirmation"
                            class="mt-1 block w-full rounded-md border-gray-300 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 font-medium">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
@endsection