@extends('layouts.app')

@section('header_title', 'Fiche Tronçon - ' . $troncon->numero_troncon)

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Tronçon {{ $troncon->numero_troncon }}</h1>
                <p class="text-sm text-slate-500">Voie : <span
                        class="font-semibold text-slate-700">{{ $troncon->nom_voie }}</span></p>
            </div>
            <div class="flex gap-2">
                <button onclick="history.back()"
                    class="px-4 py-2 border border-slate-300 text-sm font-semibold rounded-lg hover:bg-slate-50">←
                    Retour</button>
                <a href="{{ route('troncons.edit', $troncon->id_troncon) }}"
                    class="px-4 py-2 bg-amber-500 text-white text-sm font-bold rounded-lg hover:bg-amber-600">✏️
                    Modifier</a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-slate-800 uppercase mb-4 border-b pb-2">📋 Informations techniques
                    </h3>
                    <div class="grid grid-cols-2 gap-6 text-sm">
                        <div>
                            <p class="text-slate-500">PK Début / Fin</p>
                            <p class="font-bold text-slate-900">{{ $troncon->pk_debut }} ➔ {{ $troncon->pk_fin }}</p>
                        </div>
                        <div>
                            <p class="text-slate-500">Revêtement</p>
                            <p class="font-bold text-slate-900">{{ $troncon->type_revetement ?? 'Non défini' }}</p>
                        </div>
                        <div>
                            <p class="text-slate-500">Date Goudronnage</p>
                            <p class="font-bold text-slate-900">
                                {{ $troncon->date_dernier_goudronnage ? \Carbon\Carbon::parse($troncon->date_dernier_goudronnage)->format('d/m/Y') : 'Non renseigné' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-slate-500">État Physique</p><span
                                class="px-2 py-1 rounded bg-slate-100 text-slate-700 font-bold">{{ $troncon->etat_physique ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between">
                        <h3 class="text-sm font-bold text-slate-800 uppercase">🛠️ Historique des interventions
                            ({{ $interventions->count() }})</h3>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse($interventions as $int)
                            <div class="p-4 flex justify-between items-center hover:bg-slate-50">
                                <div>
                                    <p class="text-sm font-bold text-slate-900">{{ $int->type_intervention }}</p>
                                    <p class="text-xs text-slate-500">
                                        {{ \Carbon\Carbon::parse($int->date_ouverture)->format('d/m/Y') }}
                                    </p>
                                </div>
                                <span
                                    class="px-2 py-1 text-[10px] font-bold rounded-full {{ $int->statut_global == 'Terminé' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $int->statut_global }}
                                </span>
                                <a href="{{ route('interventions.show', $int->id_int) }}"
                                    class="text-xs text-blue-600 font-bold hover:underline">Voir</a>
                            </div>
                        @empty
                            <p class="p-6 text-center text-sm text-slate-400 italic">Aucune intervention sur ce tronçon.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-slate-800 uppercase mb-4 border-b pb-2">🧩 Équipements</h3>
                    <ul class="space-y-2">
                        @forelse($equipements as $eq)
                            <li class="text-sm text-slate-700 font-medium border-b pb-2 last:border-0">⚙️
                                {{ $eq->nom_equipement }}
                            </li>
                        @empty
                            <li class="text-sm text-slate-400 italic">Aucun équipement rattaché.</li>
                        @endforelse
                    </ul>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-slate-800 uppercase mb-4 border-b pb-2">🔗 Liaisons Ouvrages</h3>
                    <p class="text-sm text-slate-600">Ouvrage associé : <span
                            class="font-bold text-slate-900">{{ $troncon->nom_ouvrage_lie ?? 'Aucun' }}</span></p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 uppercase mb-4 border-b border-slate-100 pb-2">📂 Documents
                        & Cartographies</h3>

                    <ul class="space-y-3 mb-6">
                        @forelse($documents as $doc)
                            <li
                                class="flex items-center justify-between p-3 bg-slate-50 border border-slate-100 rounded-lg transition hover:bg-slate-100">
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl">
                                        {{ in_array(strtolower($doc->type_doc), ['pdf']) ? '📄' : (in_array(strtolower($doc->type_doc), ['jpg', 'png', 'jpeg']) ? '🖼️' : '📁') }}
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800 line-clamp-1">{{ $doc->nom_fichier }}</p>
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
                                            class="inline" onsubmit="return confirm('Supprimer ce document du tronçon ?');">
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
                            <li class="text-sm text-slate-400 italic text-center py-4">Aucun document technique associé à ce
                                tronçon.</li>
                        @endforelse
                    </ul>
                </div>

                @can('check-permission', ['Patrimoine & Equipements', 'ecriture'])
                    <form action="{{ route('troncons.documents.store', $troncon->id_troncon) }}" method="POST"
                        enctype="multipart/form-data"
                        class="bg-slate-50 p-4 rounded-lg border border-slate-200 border-dashed mt-auto">
                        @csrf

                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Ajouter un document</label>
                        <p class="text-[10px] text-slate-500 mb-3">Formats acceptés : PDF, JPG, PNG, DOC, DOCX. (Max : 5 Mo)</p>

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
        </div>
    </div>
@endsection