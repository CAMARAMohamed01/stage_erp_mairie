@extends('layouts.app')

@section('header_title', 'Fiche d\'instruction d\'urbanisme')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6 pb-12">
        <!-- À insérer au début de vos vues pour démasquer les bugs -->
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl font-semibold text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl font-semibold text-sm">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl text-sm space-y-1">
                <p class="font-bold">🛑 Problème de validation :</p>
                <ul class="list-disc pl-5 text-xs font-medium">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div
            class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-wrap justify-between items-center gap-4">
            <div class="flex items-center gap-4">
                <div
                    class="w-14 h-14 rounded-xl bg-indigo-50 text-indigo-700 border border-indigo-200 flex flex-col items-center justify-center font-bold">
                    <span class="text-[10px] uppercase opacity-75">Acte</span>
                    <span class="text-lg font-black leading-none mt-0.5">{{ $dossier->type_dossier_CU_DP_ }}</span>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 font-mono">{{ $dossier->numero_dossier }}</h1>
                    <p class="text-xs text-slate-500 mt-0.5 font-medium">Déposé en mairie le
                        {{ $dossier->date_depot ? $dossier->date_depot->format('d/m/Y') : 'Inconnu' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('dossiers-urba.index') }}"
                    class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 transition">←
                    Liste</a>

                @can('check-permission', ['Urbanisme', 'ecriture'])
                    <a href="{{ route('dossiers-urba.edit', $dossier->id_dossier) }}"
                        class="px-4 py-2 bg-amber-50 text-amber-700 border border-amber-200 text-sm font-semibold rounded-lg hover:bg-amber-100 transition">✏️
                        Modifier</a>
                @endcan

                @can('check-permission', ['Urbanisme', 'suppression'])
                    <form action="{{ route('dossiers-urba.destroy', $dossier->id_dossier) }}" method="POST"
                        onsubmit="return confirm('Confirmer la suppression ? Le dossier sera clos et les liens de parcelles effacés.');">
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

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                    <div class="flex justify-between items-center border-b pb-2">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">🔬 Synthèse de l'instruction
                        </h3>
                        <div>
                            @if($dossier->nature_decision === 'Accordé')
                                <span
                                    class="px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-800 border border-green-200">✓
                                    Avis Favorable / Accordé</span>
                            @elseif($dossier->nature_decision === 'Refusé')
                                <span
                                    class="px-3 py-1 text-xs font-bold rounded-full bg-red-100 text-red-800 border border-red-200">❌
                                    Avis Défavorable / Refusé</span>
                            @else
                                <span
                                    class="px-3 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-200">⏳
                                    Instruction en cours</span>
                            @endif
                        </div>
                    </div>

                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-100 text-sm">
                        <p class="font-bold text-slate-500 text-xs uppercase mb-1">Objet des travaux :</p>
                        <p class="text-slate-800 font-medium leading-relaxed">
                            {{ $dossier->objet_travaux ?? 'Non spécifié' }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-sm pt-2">
                        <div>
                            <span class="text-slate-500 block">Surface de Plancher créée :</span>
                            <span class="font-bold text-slate-800 text-base">{{ $dossier->surface_plancher_m2 ?? '0.00' }}
                                m²</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Hauteur maximale :</span>
                            <span class="font-bold text-slate-800 text-base">{{ $dossier->hauteur_construction ?? '0.00' }}
                                m</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b pb-2">👥 Acteurs &
                        Délais Légaux</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <div class="space-y-3">
                            <div>
                                <span class="text-slate-400 block text-xs font-bold uppercase">Pétitionnaire :</span>
                                <span class="font-semibold text-slate-800">
                                    @if($dossier->demandeur)
                                        @if($dossier->demandeur->type_tiers === 'Physique')
                                            👤 {{ $dossier->demandeur->physique?->prenom_tiers }}
                                            {{ $dossier->demandeur->physique?->nom_tiers }}
                                        @else
                                            🏢 {{ $dossier->demandeur->morale?->raison_sociale }}
                                        @endif
                                    @else
                                        <span class="text-slate-400 italic">Non spécifié</span>
                                    @endif
                                </span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-xs font-bold uppercase">Instructeur de la Mairie
                                    :</span>
                                <span class="font-semibold text-slate-800">👤 {{ $dossier->instructeur->prenom_user ?? '' }}
                                    {{ $dossier->instructeur->nom_user ?? 'Non assigné' }}</span>
                            </div>
                        </div>

                        <div class="space-y-3 border-l pl-6 border-slate-100">
                            <div>
                                <span class="text-slate-400 block text-xs font-bold uppercase">Date limite d'instruction
                                    :</span>
                                <span
                                    class="font-semibold {{ $dossier->date_limite_instruction && $dossier->date_limite_instruction->isPast() && $dossier->nature_decision == 'En cours\'instruction' ? 'text-red-600 font-bold' : 'text-slate-800' }}">
                                    📅
                                    {{ $dossier->date_limite_instruction ? $dossier->date_limite_instruction->format('d/m/Y') : 'Non calculée' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-xs font-bold uppercase">Arrêté municipal officiel
                                    :</span>
                                <span class="font-semibold text-slate-800">
                                    @if($dossier->acteDecision)
                                        📜 Arrêté N°{{ $dossier->acteDecision->numero_decision }}
                                    @else
                                        <span class="text-slate-400 italic">En attente de signature</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b pb-2">📄 Plans et Pièces
                        graphiques numérisées</h3>

                    @forelse($dossier->documents as $doc)
                        <div class="flex justify-between items-center p-2.5 bg-slate-50 border rounded-lg text-sm group">
                            <div class="flex items-center gap-2">
                                <span class="text-lg">📁</span>
                                <div>
                                    <p class="font-semibold text-slate-800">{{ $doc->nom_fichier }}</p>
                                    <p class="text-[10px] text-slate-400">Ajouté le
                                        {{ \Carbon\Carbon::parse($doc->date_upload)->format('d/m/Y') }} |
                                        {{ number_format($doc->taille_ko / 1024, 2) }} Mo
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ asset('storage/' . $doc->chemin_stockage) }}" target="_blank"
                                    class="text-xs bg-white hover:bg-slate-100 text-slate-700 font-bold py-1 px-3 border rounded shadow-sm transition">
                                    Visualiser
                                </a>

                                @can('check-permission', ['Urbanisme', 'suppression'])
                                    <form action="{{ route('dossiers-urba.documents.destroy', $doc->id_document) }}" method="POST"
                                        onsubmit="return confirm('⚠️ Confirmer la suppression définitive de ce document/plan ? Le fichier sera effacé du serveur.');"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-500 hover:text-red-700 font-bold p-1 text-sm transition"
                                            title="Supprimer définitivement">
                                            🗑️
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic text-center py-2">Aucun plan de géomètre ou dossier CERFA
                            téléversé.</p>
                    @endforelse

                    @can('check-permission', ['Urbanisme', 'ecriture'])
                        <form action="{{ route('dossiers-urba.documents.store', $dossier->id_dossier) }}" method="POST"
                            enctype="multipart/form-data" class="pt-4 border-t border-dashed flex gap-3 items-center">
                            @csrf
                            <input type="file" name="fichier" required
                                class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 file:cursor-pointer hover:file:bg-indigo-100">
                            <button type="submit"
                                class="px-4 py-1.5 bg-slate-900 text-white text-xs font-bold rounded-lg hover:bg-slate-800 transition">Téléverser
                                la pièce</button>
                        </form>
                    @endcan
                </div>
            </div>

            <div>
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm h-full space-y-4">
                    <h3
                        class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b pb-2 flex items-center justify-between">
                        <span>📍 Parcelles d'assiette</span>
                        <span
                            class="bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full text-xs font-bold">{{ $dossier->parcelles->count() }}</span>
                    </h3>

                    <ul class="space-y-2">
                        @foreach($dossier->parcelles as $parcelle)
                            <li>
                                <a href="{{ route('parcelles.show', $parcelle->id_parcelle) }}"
                                    class="block p-3 bg-slate-50 hover:bg-indigo-50/50 border border-slate-100 hover:border-indigo-200 rounded-lg text-sm transition group">
                                    <div class="font-bold text-slate-800 group-hover:text-indigo-700">Section
                                        {{ $parcelle->section_cadastrale }} - N°{{ $parcelle->num_parcelle }}
                                    </div>
                                    <div class="text-xs text-slate-500 mt-1">Lieu-dit :
                                        {{ $parcelle->lieuDit->nom_lieu_dit ?? 'Non précisé' }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">Surface :
                                        {{ $parcelle->surface_cadastrale ?? 'N/A' }} m²
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

    </div>
@endsection