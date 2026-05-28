@extends('layouts.app')

@section('header_title', 'Détail du Support d\'Accès')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6 pb-12">

        <div
            class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-wrap justify-between items-center gap-4">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-xl shadow-inner border border-slate-200">
                    🔑
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 font-mono">{{ $support->numero_serie }}</h1>
                    <p class="text-sm text-slate-500 font-medium">Type : {{ $support->type_support ?? 'Non précisé' }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('supports-acces.index') }}"
                    class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 transition">←
                    Liste</a>

                @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                    <a href="{{ route('supports-acces.edit', $support->id_support) }}"
                        class="px-4 py-2 bg-amber-50 text-amber-700 border border-amber-200 text-sm font-semibold rounded-lg hover:bg-amber-100 transition">✏️
                        Modifier</a>
                @endcan

                @can('check-permission', ['Patrimoine & Equipements', 'suppression'])
                    <form action="{{ route('supports-acces.destroy', $support->id_support) }}" method="POST"
                        onsubmit="return confirm('Confirmer la suppression définitive ? Toutes les affectations et autorisations d\'ouverture seront supprimées.');">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-50 text-red-700 border border-red-200 text-sm font-semibold rounded-lg hover:bg-red-100 transition">🗑️
                            Supprimer</button>
                    </form>
                @endcan
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="md:col-span-2 space-y-6">

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2">👤 Affectation
                        actuelle</h3>

                    @if($affectationActuelle)
                        <div
                            class="flex flex-wrap justify-between items-center bg-slate-50 p-4 rounded-lg border border-slate-100 gap-4">
                            <div>
                                <p class="text-base font-bold text-slate-900">{{ $affectationActuelle->prenom_user }}
                                    {{ $affectationActuelle->nom_user }}
                                </p>
                                <p class="text-xs text-slate-500 mt-1">Remis le :
                                    {{ \Carbon\Carbon::parse($affectationActuelle->pivot->date_remise)->format('d/m/Y') }}
                                </p>
                                @if($affectationActuelle->pivot->commentaire)
                                    <p class="text-xs text-slate-400 italic mt-1">Note :
                                        {{ $affectationActuelle->pivot->commentaire }}
                                    </p>
                                @endif
                                <div class="mt-2">
                                    @if($affectationActuelle->pivot->attestation_signee)
                                        <span
                                            class="px-2.5 py-0.5 text-[10px] font-bold rounded bg-green-100 text-green-700 border border-green-200">✓
                                            Décharge Signée</span>
                                    @else
                                        <span
                                            class="px-2.5 py-0.5 text-[10px] font-bold rounded bg-amber-100 text-amber-700 border border-amber-200">⚠
                                            Décharge manquante</span>
                                    @endif
                                </div>
                            </div>

                            @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                                <div>
                                    <form
                                        action="{{ route('supports-acces.restituer', [$support->id_support, $affectationActuelle->id_user]) }}"
                                        method="POST" onsubmit="return confirm('Enregistrer le retour de cette clé au coffre ?');">
                                        @csrf @method('PUT')
                                        <button type="submit"
                                            class="px-3 py-2 bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 text-xs font-bold rounded-lg shadow-sm transition">
                                            🔒 Enregistrer la Restitution
                                        </button>
                                    </form>
                                </div>
                            @endcan
                        </div>
                    @else
                        <div class="bg-slate-50/50 rounded-lg border border-dashed border-slate-200 p-4 text-center">
                            <p class="text-sm text-slate-500 italic mb-4">Ce support d'accès est actuellement disponible au
                                coffre (non affecté).</p>

                            @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                                <form action="{{ route('supports-acces.affecter', $support->id_support) }}" method="POST"
                                    class="max-w-md mx-auto text-left bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-3">
                                    @csrf
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Choisir l'agent municipal
                                            *</label>
                                        <select name="id_user" required
                                            class="w-full text-sm border border-slate-300 rounded-md p-2">
                                            <option value="">-- Sélectionner un agent --</option>
                                            @foreach($tousLesAgents as $agent)
                                                <option value="{{ $agent->id_user }}">{{ $agent->nom_user }} {{ $agent->prenom_user }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Date de remise *</label>
                                            <input type="date" name="date_remise" value="{{ date('Y-m-d') }}" required
                                                class="w-full text-sm border border-slate-300 rounded-md p-1.5">
                                        </div>
                                        <div class="flex items-center pt-5">
                                            <label
                                                class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                                                <input type="checkbox" name="attestation_signee" value="1"
                                                    class="rounded border-gray-300"> Décharge signée
                                            </label>
                                        </div>
                                    </div>
                                    <div>
                                        <input type="text" name="commentaire"
                                            placeholder="Commentaire optionnel (ex: Remplacement...)"
                                            class="w-full text-sm border border-slate-300 rounded-md p-2">
                                    </div>
                                    <button type="submit"
                                        class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs py-2 rounded-md transition shadow-sm">
                                        🔑 Confier le support d'accès
                                    </button>
                                </form>
                            @endcan
                        </div>
                    @endif
                </div>

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2">📋 Historique
                        des attributions</h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="border-b border-slate-200 text-slate-400 font-bold uppercase tracking-wider">
                                    <th class="pb-3">Agent</th>
                                    <th class="pb-3">Date de remise</th>
                                    <th class="pb-3">Date de restitution</th>
                                    <th class="pb-3">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-600">
                                @forelse($support->utilisateurs as $user)
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="py-3 font-semibold text-slate-800">{{ $user->prenom_user }}
                                            {{ $user->nom_user }}
                                        </td>
                                        <td class="py-3">{{ \Carbon\Carbon::parse($user->pivot->date_remise)->format('d/m/Y') }}
                                        </td>
                                        <td class="py-3">
                                            {{ $user->pivot->date_restitution ? \Carbon\Carbon::parse($user->pivot->date_restitution)->format('d/m/Y') : '—' }}
                                        </td>
                                        <td class="py-3">
                                            @if($user->pivot->date_restitution)
                                                <span class="text-slate-400 italic">Restitué</span>
                                            @else
                                                <span class="text-green-600 font-bold">Possession active</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-4 text-center text-slate-400 italic">Aucune affectation passée
                                            pour ce support.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div>
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-6">
                    <h3
                        class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b pb-2 flex items-center justify-between">
                        <span>🚪 Droits d'ouvertures</span>
                        <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full text-xs">
                            {{ $batimentsAutorises->count() + $locauxAutorises->count() + $equipementsAutorises->count() }}
                        </span>
                    </h3>

                    @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                        <form action="{{ route('supports-acces.ouvertures.store', $support->id_support) }}" method="POST"
                            class="bg-slate-50 p-3 rounded-lg border border-slate-200 space-y-2">
                            @csrf
                            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">➕ Autoriser un accès</p>

                            <select name="type_cible" id="type_cible" onchange="changerListeOuverture()" required
                                class="w-full text-xs border border-slate-300 rounded p-1.5 bg-white">
                                <option value="batiment">🏢 Un bâtiment entier</option>
                                <option value="local">🚪 Un local / bureau spécifique</option>
                                <option value="equipement">⚙️ Un équipement sécurisé</option>
                            </select>

                            <select name="target_id" id="select_batiment"
                                class="w-full text-xs border border-slate-300 rounded p-1.5 bg-white target-select">
                                @foreach($tousLesBatiments as $b) <option value="{{ $b->id_batiment }}">{{ $b->nom_bat }}
                                </option> @endforeach
                            </select>

                            <select name="target_id" id="select_local"
                                class="w-full text-xs border border-slate-300 rounded p-1.5 bg-white target-select hidden"
                                disabled>
                                @foreach($tousLesLocaux as $l) <option value="{{ $l->id_local }}">{{ $l->nom_local }}</option>
                                @endforeach
                            </select>

                            <select name="target_id" id="select_equipement"
                                class="w-full text-xs border border-slate-300 rounded p-1.5 bg-white target-select hidden"
                                disabled>
                                @foreach($tousLesEquipements as $e) <option value="{{ $e->id_equipement }}">
                                    {{ $e->nom_equipement }}
                                </option> @endforeach
                            </select>

                            <button type="submit"
                                class="w-full bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs py-1.5 rounded transition">
                                Lier cet accès
                            </button>
                        </form>
                    @endcan

                    <div class="space-y-4 max-h-[400px] overflow-y-auto pr-1">

                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-1 flex justify-between">
                                <span>🏢 Bâtiments</span> <span>({{ $batimentsAutorises->count() }})</span>
                            </p>
                            <ul class="space-y-1">
                                @foreach($batimentsAutorises as $bat)
                                    <li
                                        class="p-2 bg-slate-50 text-xs font-medium text-slate-700 flex justify-between items-center rounded border border-slate-100">
                                        <span>🏢 {{ $bat->nom_bat }}</span>
                                        @can('check-permission', ['Patrimoine & Equipements', 'suppression'])
                                            <form
                                                action="{{ route('supports-acces.ouvertures.destroy', [$support->id_support, 'batiment', $bat->id_batiment]) }}"
                                                method="POST">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-sm"
                                                    title="Retirer l'accès">×</button>
                                            </form>
                                        @endcan
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-1 flex justify-between">
                                <span>🚪 Locaux & Bureaux</span> <span>({{ $locauxAutorises->count() }})</span>
                            </p>
                            <ul class="space-y-1">
                                @foreach($locauxAutorises as $loc)
                                    <li
                                        class="p-2 bg-slate-50 text-xs font-medium text-slate-700 flex justify-between items-center rounded border border-slate-100">
                                        <span>🚪 {{ $loc->nom_local }}</span>
                                        @can('check-permission', ['Patrimoine & Equipements', 'suppression'])
                                            <form
                                                action="{{ route('supports-acces.ouvertures.destroy', [$support->id_support, 'local', $loc->id_local]) }}"
                                                method="POST">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-sm"
                                                    title="Retirer l'accès">×</button>
                                            </form>
                                        @endcan
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-1 flex justify-between">
                                <span>⚙️ Équipements sécurisés</span> <span>({{ $equipementsAutorises->count() }})</span>
                            </p>
                            <ul class="space-y-1">
                                @foreach($equipementsAutorises as $eq)
                                    <li
                                        class="p-2 bg-slate-50 text-xs font-medium text-slate-700 flex justify-between items-center rounded border border-slate-100">
                                        <span>⚙️ {{ $eq->nom_equipement }}</span>
                                        @can('check-permission', ['Patrimoine & Equipements', 'suppression'])
                                            <form
                                                action="{{ route('supports-acces.ouvertures.destroy', [$support->id_support, 'equipement', $eq->id_equipement]) }}"
                                                method="POST">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-sm"
                                                    title="Retirer l'accès">×</button>
                                            </form>
                                        @endcan
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function changerListeOuverture() {
            const type = document.getElementById('type_cible').value;

            // On cache et désactive tous les sélecteurs de cible
            document.querySelectorAll('.target-select').forEach(el => {
                el.classList.add('hidden');
                el.disabled = true;
            });

            // On affiche et active uniquement celui correspondant au type choisi
            const activeSelect = document.getElementById('select_' + type);
            if (activeSelect) {
                activeSelect.classList.remove('hidden');
                activeSelect.disabled = false;
            }
        }
    </script>
@endsection