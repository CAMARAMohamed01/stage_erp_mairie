@extends('layouts.app')

@section('header_title', 'Fiche Pièce - ' . $local->nom_local)

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- --- BLOC D'AFFICHAGE DES MESSAGES DE SUCCÈS OU D'ERREUR --- --}}
        @if (session('error'))
            <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded shadow-sm">
                <p class="font-bold">Erreur</p>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        @if (session('success'))
            <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded shadow-sm">
                <p class="font-bold">Succès</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif
        {{-- ----------------------------------------------------------- --}}

        <div
            class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-wrap justify-between items-center gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <span class="text-3xl">🚪</span>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $local->nom_local }}</h1>
                    <span
                        class="px-2.5 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                        Usage : {{ $local->libelle_usage ?? 'Non défini' }}
                    </span>
                    <span
                        class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $local->statut_occupation === 'Occupé' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-amber-50 text-amber-700 border-amber-200' }} border">
                        {{ $local->statut_occupation ?? 'Statut inconnu' }}
                    </span>
                </div>
                <p class="text-sm text-slate-500 mt-1.5 flex items-center gap-1.5">
                    📍 Situé dans : <span class="text-slate-700 font-medium">
                        {{ $local->nom_bat ?? $local->nom_lieu ?? 'Aucun rattachement principal' }}
                        {{ $local->niveau ? '(Niveau : ' . $local->niveau . ')' : '' }}
                    </span>
                    | 📏 Surface : <span
                        class="text-slate-700 font-medium">{{ $local->surface_m2 ? $local->surface_m2 . ' m²' : 'Non mesurée' }}</span>
                </p>
            </div>

            <div class="flex gap-2">
                <button onclick="history.back()"
                    class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 transition">
                    ← Retour
                </button>

                {{-- Bouton Modifier (Droit : ecriture) --}}
                @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                    <a href="{{ route('locaux.edit', $local->id_local) }}"
                        class="px-4 py-2 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition">
                        Modifier la pièce
                    </a>
                @endcan

                {{-- Bouton Supprimer (Droit : suppression) --}}
                @can('check-permission', ['Patrimoine & Equipements', 'suppression'])
                    <form action="{{ route('locaux.destroy', $local->id_local) }}" method="POST"
                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette pièce ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition">
                            Supprimer
                        </button>
                    </form>
                @endcan
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-slate-800 tracking-tight flex items-center gap-2">
                            ⚙️ Équipements présents ({{ $equipements->count() }})
                        </h3>
                        @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                            <a href="{{ route('equipements.create', ['id_local' => $local->id_local]) }}"
                                class="text-xs text-blue-600 font-semibold hover:underline">
                                ➕ Ajouter un équipement
                            </a>
                        @endcan
                    </div>
                    <div class="divide-y divide-slate-100 max-h-60 overflow-y-auto">
                        @forelse($equipements as $equip)
                            <div class="p-3.5 flex justify-between items-center hover:bg-slate-50 transition">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">{{ $equip->nom_equipement }}</p>
                                    <p class="text-xs text-slate-400">Réf : {{ $equip->reference_serie ?? 'N/A' }} | État :
                                        {{ $equip->etat_fonctionnement ?? 'Opérationnel' }}
                                    </p>
                                </div>
                                <a href="{{ route('equipements.show', $equip->id_equipement) }}"
                                    class="text-xs text-blue-600 font-semibold hover:underline">
                                    Voir →
                                </a>
                            </div>
                        @empty
                            <p class="p-4 text-xs text-slate-400 italic text-center">Aucun équipement inventorié dans cette
                                pièce.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200">
                        <h3 class="text-sm font-bold text-slate-800 tracking-tight flex items-center gap-2">
                            ⚡ Compteurs Réseaux ({{ $compteurs->count() }})
                        </h3>
                    </div>
                    <div class="p-4">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="text-slate-400 font-bold border-b border-slate-100 pb-2">
                                    <th class="pb-2">Point de comptage</th>
                                    <th class="pb-2">Réseau</th>
                                    <th class="pb-2 text-right">N° Compteur</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($compteurs as $compteur)
                                    <tr>
                                        <td class="py-2.5 font-semibold text-slate-800">{{ $compteur->point_comptage }}</td>
                                        <td class="py-2.5">
                                            <span
                                                class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded">{{ $compteur->type_reseau }}</span>
                                        </td>
                                        <td class="py-2.5 text-right text-slate-600 font-mono">
                                            {{ $compteur->numero_compteur ?? 'N/A' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-4 text-center text-slate-400 italic">Aucun compteur associé à
                                            ce local.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">📂 Documents
                            Techniques & Photos</h2>

                        <ul class="space-y-3 mb-6">
                            @forelse($documents as $doc)
                                <li
                                    class="flex items-center justify-between p-3 bg-slate-50 border border-slate-100 rounded-lg transition hover:bg-slate-100">
                                    <div class="flex items-center gap-3">
                                        <span class="text-2xl">
                                            {{ in_array(strtolower($doc->type_doc), ['pdf']) ? '📄' : (in_array(strtolower($doc->type_doc), ['jpg', 'png', 'jpeg']) ? '🖼️' : '📁') }}
                                        </span>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800 line-clamp-1">{{ $doc->nom_fichier }}
                                            </p>
                                            <p class="text-xs text-slate-500">
                                                {{ \Carbon\Carbon::parse($doc->date_upload)->format('d/m/Y') }} •
                                                {{ number_format($doc->taille_ko, 0, ',', ' ') }} Ko
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <a href="{{ asset('storage/' . $doc->chemin_stockage) }}" target="_blank"
                                            class="text-blue-600 hover:text-blue-800 text-xs font-bold bg-blue-50 px-2 py-1 rounded border border-blue-100">
                                            Voir
                                        </a>
                                        @can('check-permission', ['Patrimoine & Equipements', 'suppression'])
                                            <form action="{{ route('documents.global.destroy', $doc->id_document) }}" method="POST"
                                                class="inline" onsubmit="return confirm('Supprimer ce document ?');">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-800 text-xs font-bold bg-red-50 px-2 py-1 rounded border border-red-100">
                                                    🗑️
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </li>
                            @empty
                                <li class="text-sm text-slate-400 italic text-center py-4">Aucun plan ou document rattaché à ce
                                    local.</li>
                            @endforelse
                        </ul>
                    </div>

                    @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                        <form action="{{ route('locaux.documents.store', $local->id_local) }}" method="POST"
                            enctype="multipart/form-data"
                            class="bg-slate-50 p-4 rounded-lg border border-slate-200 border-dashed mt-auto">
                            @csrf

                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Ajouter une pièce
                                jointe</label>
                            <p class="text-[10px] text-slate-500 mb-3">Formats acceptés : PDF, JPG, PNG, DOC, DOCX. (Max : 5 Mo)
                            </p>

                            <div class="flex items-start gap-2">
                                <div class="w-full">
                                    <input type="file" name="fichier" required
                                        class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:font-semibold file:bg-slate-900 file:text-white hover:file:bg-slate-800 cursor-pointer focus:outline-none">

                                    @error('fichier')
                                        <p class="text-xs text-red-600 font-bold mt-2">⚠️ {{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit"
                                    class="px-3 py-1.5 bg-indigo-600 text-white font-bold rounded-md hover:bg-indigo-700 transition text-xs whitespace-nowrap">
                                    📤 Envoyer
                                </button>
                            </div>
                        </form>
                    @endcan
                </div>
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-red-50/50 border-b border-red-100">
                        <h3 class="text-sm font-bold text-red-800 tracking-tight flex items-center gap-2">
                            🚨 Incidents en cours ({{ $actions->count() }})
                        </h3>
                    </div>
                    <div class="p-4 space-y-3 max-h-52 overflow-y-auto">
                        @forelse($actions as $sig)
                            <div class="p-2.5 bg-slate-50 border border-slate-150 rounded-lg text-xs">
                                <div class="flex justify-between font-semibold text-slate-800">
                                    <span class="truncate pr-2">⚠️ {{ $sig->statut_action }}</span>
                                    <span class="text-red-600 shrink-0">{{ $sig->priorite ?? 'Normale' }}</span>
                                </div>
                                <p class="text-slate-500 mt-1 line-clamp-2">{{ $sig->description }}</p>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 italic text-center py-2">Parfait ! Aucun incident déclaré ici.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200">
                        <h3 class="text-sm font-bold text-slate-800 tracking-tight">📝 Notes & Assurances</h3>
                    </div>
                    <div class="p-4 text-sm text-slate-600 space-y-3">
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase">Réf. Assurance</span>
                            {{ $local->ref_article_assurance ?? 'Non renseignée' }}
                            @if($local->prime_assurance_ttc)
                                <span class="text-slate-400">({{ $local->prime_assurance_ttc }} €)</span>
                            @endif
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase">Remarques</span>
                            {{ $local->remarque ?? 'Aucune observation.' }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection