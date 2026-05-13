@extends('layouts.app')

@section('title', 'Détail du Signalement #' . $signalement->id_sig)

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('signalements.index') }}"
                class="text-slate-500 hover:text-slate-800 text-sm flex items-center transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Retour à la liste des signalements
            </a>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mb-6">
            <div class="p-8">
                <div class="flex justify-between items-start mb-6 border-b border-slate-100 pb-6">
                    <div class="pr-6">
                        <h1 class="text-2xl font-bold text-slate-900 mb-2">Signalement #{{ $signalement->id_sig }}</h1>
                        <p class="text-slate-600 text-lg italic bg-slate-50 p-3 rounded border border-slate-100">
                            "{{ $signalement->description }}"
                        </p>
                    </div>
                    <div class="flex flex-col items-end gap-3 min-w-max">
                        <div class="flex gap-2">
                            <x-badge type="statut" :value="$signalement->statut_signalement" class="text-sm px-4 py-1" />
                            <x-badge type="priorite" :value="$signalement->priorite" class="text-sm px-4 py-1" />
                        </div>

                        <div class="flex items-center gap-2 mt-2">
                            <a href="{{ route('signalements.edit', $signalement->id_sig) }}"
                                class="text-xs bg-amber-100 text-amber-700 hover:bg-amber-200 px-3 py-1.5 rounded-lg font-bold transition">
                                ✏️ Modifier
                            </a>

                            <form action="{{ route('signalements.destroy', $signalement->id_sig) }}" method="POST"
                                onsubmit="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer ce signalement ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-xs bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1.5 rounded-lg font-bold transition">
                                    🗑️ Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-2">
                    <div>
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Informations Émetteur
                        </h3>
                        <div class="space-y-3 bg-slate-50 p-4 rounded-lg border border-slate-100">
                            <div class="flex justify-between items-center border-b border-slate-200 pb-2">
                                <span class="text-slate-500 text-sm">Nom :</span>
                                <div class="text-right">
                                    @php
                                        // On prépare le nom à afficher par défaut
                                        $nomAffiche = $signalement->emetteur_nom;

                                        // S'il y a un ID Tiers, on va chercher dynamiquement le vrai nom !
                                        if ($signalement->id_tiers) {
                                            $citoyen = \App\Models\TiersPhysique::where(
                                                'id_tiers',
                                                $signalement->id_tiers
                                            )->first();
                                            if ($citoyen) {
                                                $nomAffiche = $citoyen->prenom_tiers . ' ' . $citoyen->nom_tiers;
                                            }
                                        }
                                    @endphp

                                    <span class="font-bold text-slate-800">{{ $nomAffiche }}</span>

                                    @if($signalement->id_tiers)
                                        <span
                                            class="ml-2 inline-block text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full uppercase font-bold"
                                            title="Citoyen enregistré dans la base Tiers">
                                            Inscrit
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex justify-between items-center border-b border-slate-200 pb-2">
                                <span class="text-slate-500 text-sm">Contact :</span>
                                <span
                                    class="font-medium text-slate-800">{{ $signalement->emetteur_contact ?? 'Non renseigné' }}</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-slate-200 pb-2">
                                <span class="text-slate-500 text-sm">Réception :</span>
                                <span class="font-medium text-slate-800">{{ $signalement->mode_reception }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500 text-sm">Date d'alerte :</span>
                                <span
                                    class="font-medium text-slate-800">{{ \Carbon\Carbon::parse($signalement->date_creation)->format('d/m/Y à H:i') }}</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                            Classification technique
                        </h3>
                        <div class="space-y-3 bg-slate-50 p-4 rounded-lg border border-slate-100">
                            <div class="flex justify-between items-center border-b border-slate-200 pb-2">
                                <span class="text-slate-500 text-sm">Catégorie :</span>
                                <span
                                    class="font-bold text-slate-800">{{ $signalement->categorie->libelle ?? 'Non définie' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500 text-sm">Assigné à :</span>
                                <span class="font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded">Service
                                    Technique</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 px-8 py-4 border-t border-slate-200 flex flex-wrap justify-between items-center gap-4">
                <div>
                    @if(session('success'))
                        <span
                            class="text-green-600 font-medium text-sm flex items-center bg-green-50 px-3 py-1.5 rounded-lg border border-green-200">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            {{ session('success') }}
                        </span>
                    @endif
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('signalement.pdf', $signalement->id_sig) }}" target="_blank"
                        class="px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg font-bold hover:bg-slate-100 transition shadow-sm flex items-center text-sm">
                        🖨️ Imprimer Récépissé
                    </a>

                    @if($signalement->statut_signalement === 'Nouveau')
                        <form action="{{ route('signalement.prendre-en-charge', $signalement->id_sig) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="px-5 py-2 bg-slate-800 text-white rounded-lg font-bold hover:bg-slate-900 shadow-sm transition flex items-center text-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                    </path>
                                </svg>
                                Prendre en charge
                            </button>
                        </form>
                    @else
                        <button disabled
                            class="px-5 py-2 bg-slate-200 text-slate-500 rounded-lg font-medium cursor-not-allowed italic text-sm">
                            ✓ Pris en charge
                        </button>
                    @endif

                    @if($signalement->statut_signalement !== 'Transmis')
                        <form action="{{ route('signalement.creer-intervention', $signalement->id_sig) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="px-5 py-2 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 shadow-sm transition flex items-center text-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Générer une intervention
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection