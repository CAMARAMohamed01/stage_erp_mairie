@extends('layouts.app')

@section('title', 'Détail du Signalement #' . $signalement->id_sig)

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('technique.dashboard') }}"
                class="text-slate-500 hover:text-slate-800 text-sm flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Retour au tableau de bord
            </a>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mb-6">
            <div class="p-8">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 mb-2">Signalement #{{ $signalement->id_sig }}
                        </h1>
                        <p class="text-slate-500 text-lg">{{ $signalement->description }}</p>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <x-badge type="statut" :value="$signalement->statut_signalement" class="text-sm px-4 py-1" />
                        <x-badge type="priorite" :value="$signalement->priorite" class="text-sm px-4 py-1" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-slate-100 pt-6">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Informations Émetteur
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Nom :</span>
                                <span class="font-medium text-slate-800">{{ $signalement->emetteur_nom }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Réception :</span>
                                <span class="font-medium text-slate-800">{{ $signalement->mode_reception }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Date :</span>
                                <span
                                    class="font-medium text-slate-800">{{ \Carbon\Carbon::parse($signalement->date_creation)->format('d/m/Y à H:i') }}</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Classification</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Catégorie :</span>
                                <span
                                    class="font-medium text-slate-800">{{ $signalement->categorie->libelle ?? 'Non définie' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Assigné à :</span>
                                <span class="font-medium text-blue-600">Technicien Voirie</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 px-8 py-4 border-t border-slate-200 flex justify-between items-center">
                <div>
                    @if(session('success'))
                        <span class="text-green-600 font-medium text-sm flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z">
                                </path>
                            </svg>
                            {{ session('success') }}
                        </span>
                    @endif
                </div>

                <div class="flex gap-4">
                    <a href="{{ route('signalement.pdf', $signalement->id_sig) }}" target="_blank"
                        class="px-4 py-2 bg-slate-800 text-white rounded-lg font-medium hover:bg-slate-900 transition flex items-center">
                        🖨️ Imprimer Récépissé
                    </a>
                    @if($signalement->statut_signalement === 'Nouveau')
                        <form action="{{ route('signalement.prendre-en-charge', $signalement->id_sig) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 shadow-sm transition flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                    </path>
                                </svg>
                                Prendre en charge
                            </button>
                        </form>
                    @else
                        <button disabled
                            class="px-6 py-2 bg-slate-200 text-slate-500 rounded-lg font-medium cursor-not-allowed italic">
                            Déjà pris en charge
                        </button>
                    @endif

                    @if($signalement->statut_signalement !== 'Transmis')
                        <form action="{{ route('signalement.creer-intervention', $signalement->id_sig) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="px-4 py-2 bg-white border border-blue-600 text-blue-600 rounded-lg font-medium hover:bg-blue-50 transition flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Créer une intervention
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection