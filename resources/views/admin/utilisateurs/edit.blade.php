@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md my-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Modifier la fiche de l'agent</h2>

        <form action="{{ route('utilisateurs.update', $user->id_user) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

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

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Initiales</label>
                    <input type="text" name="initiales" value="{{ $user->initiales }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Rôle Application</label>
                    <select name="role_appli" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="Agent" {{ $user->role_appli === 'Agent' ? 'selected' : '' }}>Agent de terrain
                        </option>
                        <option value="Responsable" {{ $user->role_appli === 'Responsable' ? 'selected' : '' }}>Responsable
                            Technique</option>
                        <option value="Administrateur" {{ $user->role_appli === 'Administrateur' ? 'selected' : '' }}>
                            Administrateur</option>
                        <option value="Élu" {{ $user->role_appli === 'Élu' ? 'selected' : '' }}>Élu de la commune</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Adresse Email Professionnelle</label>
                <input type="email" name="emailpro" value="{{ $user->emailpro }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Affectation au Service</label>
                <select name="id_service"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">-- Aucun service --</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id_service }}" {{ $user->id_service == $service->id_service ? 'selected' : '' }}>{{ $service->nom_service }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="bg-slate-50 p-4 rounded-md border border-slate-200 mt-6 space-y-3">
                <h3 class="text-sm font-medium text-slate-700">Modifier le mot de passe (Laisser vide pour ne pas changer)
                </h3>
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

            <div class="flex justify-end pt-4 space-x-2">
                <a href="{{ route('utilisateurs.index') }}"
                    class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">Annuler</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Enregistrer les
                    modifications</button>
            </div>
        </form>
    </div>
@endsection