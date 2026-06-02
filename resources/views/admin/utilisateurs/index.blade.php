@extends('layouts.app')

@section('header_title', 'Liste des agents municipaux')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex justify-between items-center bg-slate-50/50">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Registre des agents</h2>
                <p class="text-sm text-slate-500">Liste des utilisateurs et affectations aux services de la mairie.</p>
            </div>
            <a href="{{ route('utilisateurs.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm transition flex items-center">
                <span class="mr-2">➕</span> Créer un agent
            </a>
        </div>

        @if(session('success'))
            <div class="m-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <!-- Remplacer les entêtes de colonnes -->
                    <tr
                        class="bg-slate-50 text-slate-600 text-xs font-bold uppercase tracking-wider border-b border-slate-200">
                        <th class="px-6 py-4">Nom / Prénom</th>
                        <th class="px-6 py-4">Initiales</th>
                        <th class="px-6 py-4">Email Pro</th>
                        <th class="px-6 py-4">Service Affecté</th>
                        <th class="px-6 py-4">Rôle</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>

                    <!-- Remplacer le contenu du foreach -->
                    @foreach($utilisateurs as $user)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-6 py-4 font-semibold text-slate-900">{{ $user->nom_user }} {{ $user->prenom_user }}
                            </td>
                            <td class="px-6 py-4"><span
                                    class="bg-slate-100 text-slate-700 px-2 py-1 rounded text-xs font-mono font-bold">{{ $user->initiales ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-slate-500">{{ $user->emailpro ?? 'Non renseigné' }}</td>
                            <td class="px-6 py-4"><span
                                    class="font-medium text-slate-600">{{ $user->nom_service ?? 'Aucun service' }}</span></td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                    {{ $user->role_appli }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2 flex justify-end items-center">
                                <!-- Bouton Modifier -->
                                <a href="{{ route('utilisateurs.edit', $user->id_user) }}"
                                    class="text-blue-600 hover:text-blue-900 text-sm font-medium" title="Modifier l'agent">
                                    📝 Modifier
                                </a>

                                <!-- Bouton Supprimer (Masqué si c'est l'utilisateur lui-même connecté) -->
                                @if($user->id_user !== auth()->id())
                                    <form action="{{ route('utilisateurs.destroy', $user->id_user) }}" method="POST"
                                        onsubmit="return confirm('Êtes-vous sûr de vouloir retirer cet agent du système ? cette action est irréversible.');"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium ml-2">
                                            🗑️ Supprimer
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>

                    @endforeach
                    </tbody>
            </table>
        </div>
    </div>
@endsection