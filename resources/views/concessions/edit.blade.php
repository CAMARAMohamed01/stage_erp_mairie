@extends('layouts.app')

@section('header_title', 'Modifier la concession')

@section('content')
    <div class="max-w-4xl mx-auto pb-12">
        <div class="mb-6 flex justify-between items-end">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">✏️ Modifier l'acte de concession</h1>
                <p class="text-sm text-slate-500 mt-1">Mise à jour des informations ou ajout d'un nouveau défunt.</p>
            </div>
            <a href="{{ route('concessions.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">←
                Retour</a>
        </div>

        <form action="{{ route('concessions.update', $concession->id_concession) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">1. Emplacement Physique *</label>
                    <select name="id_emplacement" required
                        class="w-full rounded-lg border-slate-300 text-sm bg-slate-50 focus:ring-slate-900">
                        @foreach($emplacementsLibres as $emp)
                            <option value="{{ $emp->id_emplacement }}" {{ $concession->id_emplacement == $emp->id_emplacement ? 'selected' : '' }}>
                                {{ $emp->lieu->nom_lieu ?? 'Cimetière' }} - {{ $emp->reference_emplacement }}
                                ({{ $emp->type_emplacement }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">2. Contrat de Concession *</label>
                    <select name="id_contrat" required
                        class="w-full rounded-lg border-slate-300 text-sm bg-slate-50 focus:ring-slate-900">
                        @foreach($contratsDispos as $ct)
                            <option value="{{ $ct->id_contrat }}" {{ $concession->id_contrat == $ct->id_contrat ? 'selected' : '' }}>
                                N° {{ $ct->numero_contrat }} - {{ $ct->tiers->raison_sociale ?? $ct->tiers->nom_tiers }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">3. Personnes Inhumées (Défunts)</label>
                <select name="id_defunts[]" multiple size="5"
                    class="w-full rounded-lg border-slate-300 text-sm bg-slate-50 focus:ring-slate-900">
                    @foreach($personnes as $p)
                        @php
                            $isSelected = $concession->defunts->contains('id_tiers', $p->id_tiers);
                        @endphp
                        <option value="{{ $p->id_tiers }}" {{ $isSelected ? 'selected' : '' }}>
                            {{ $p->nom_tiers }} {{ $p->prenom_tiers }}
                        </option>
                    @endforeach
                </select>
                <p class="text-[10px] text-slate-400 mt-2">Maintenez CTRL (ou CMD) pour sélectionner plusieurs défunts.</p>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Bénéficiaires autorisés</label>
                    <textarea name="beneficiaires_autorises" rows="2"
                        class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900">{{ old('beneficiaires_autorises', $concession->beneficiaires_autorises) }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Observations</label>
                    <textarea name="commentaire_concession" rows="2"
                        class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-900">{{ old('commentaire_concession', $concession->commentaire_concession) }}</textarea>
                </div>
            </div>

            <div class="flex justify-between pt-4">
                <button type="button"
                    onclick="if(confirm('Supprimer définitivement cet acte et libérer la tombe ?')) document.getElementById('delete-form').submit();"
                    class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 font-semibold rounded-lg shadow-sm transition">
                    🗑️ Supprimer
                </button>

                <button type="submit"
                    class="px-6 py-3 bg-slate-900 text-white font-bold rounded-lg hover:bg-slate-800 transition shadow-lg">
                    💾 Mettre à jour l'acte
                </button>
            </div>
        </form>

        <form id="delete-form" action="{{ route('concessions.destroy', $concession->id_concession) }}" method="POST"
            class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
@endsection