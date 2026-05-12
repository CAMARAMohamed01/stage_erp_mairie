@extends('layouts.app')

@section('title', 'Fiche Intervention #' . $intervention->id_int)

@section('content')
    <div class="max-w-5xl mx-auto">
        <div class="mb-4">
            <a href="{{ route('interventions.index') }}"
                class="text-slate-500 hover:text-slate-800 text-sm flex items-center">
                ← Retour à la liste
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <span class="text-blue-600 font-bold text-sm uppercase tracking-wider">Bon de travaux</span>
                            <h1 class="text-3xl font-extrabold text-slate-900">{{ $intervention->type_intervention }}</h1>
                        </div>
                        <x-badge type="statut" :value="$intervention->statut_global" class="text-sm px-4 py-1" />
                    </div>

                    <div class="prose max-w-none text-slate-600 mb-8">
                        <h3 class="text-slate-900 font-semibold">Description du travail à effectuer :</h3>
                        <p>{{ $intervention->description }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-6 pt-6 border-t border-slate-100">
                        <div>
                            <p class="text-xs text-slate-400 uppercase font-bold">Date d'ouverture</p>
                            <p class="text-slate-800 font-medium">
                                {{ \Carbon\Carbon::parse($intervention->date_ouverture)->format('d/m/Y') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 uppercase font-bold">Catégorie technique</p>
                            <p class="text-slate-800 font-medium">{{ $intervention->categorie->libelle ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h2 class="text-lg font-bold text-slate-800 mb-4 border-b pb-2 text-slate-900">Suivi des étapes</h2>
                    <div class="space-y-4">
                        <div class="flex gap-4">
                            <div class="w-2 h-2 rounded-full bg-blue-500 mt-2"></div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">Intervention générée</p>
                                <p class="text-xs text-slate-500">
                                    {{ \Carbon\Carbon::parse($intervention->date_ouverture)->format('d/m/Y') }} - Système
                                </p>
                            </div>
                        </div>
                        @if($intervention->statut_global === 'Terminé')
                            <div class="flex gap-4">
                                <div class="w-2 h-2 rounded-full bg-green-500 mt-2"></div>
                                <div>
                                    <p class="text-sm font-bold text-slate-800">Intervention clôturée</p>
                                    <p class="text-xs text-slate-500">
                                        {{ \Carbon\Carbon::parse($intervention->date_cloture)->format('d/m/Y') }} - Agent
                                        technique
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h3 class="font-bold text-slate-900 mb-4">Actions de gestion</h3>

                    @if($intervention->statut_global !== 'Terminé')
                        <form action="{{ route('interventions.cloturer', $intervention->id_int) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="w-full bg-green-600 text-white font-bold py-3 rounded-lg hover:bg-green-700 transition shadow-md mb-3">
                                ✓ Clôturer les travaux
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('interventions.pdf', $intervention->id_int) }}"
                        class="w-full bg-white border border-slate-300 text-slate-700 py-2 rounded-lg hover:bg-slate-50 transition text-sm text-center block">
                        🖨️ Imprimer le bon (PDF)
                    </a>
                </div>

                <div class="bg-slate-100 rounded-xl p-6 border border-slate-200">
                    <h3 class="text-sm font-bold text-slate-500 uppercase mb-3">Lien Signalement</h3>
                    @if($intervention->id_sig)
                        <p class="text-sm text-slate-600 mb-2">Origine : Signalement #{{ $intervention->id_sig }}</p>
                        <a href="{{ route('signalement.show', $intervention->id_sig) }}"
                            class="text-blue-600 text-xs font-bold hover:underline">Voir le signalement source →</a>
                    @else
                        <p class="text-xs text-slate-400 italic">Créé sans signalement préalable</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection