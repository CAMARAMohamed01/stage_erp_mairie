@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto pb-12">
        <h1 class="text-2xl font-bold text-slate-900 mb-6">📜 Acter une nouvelle concession</h1>

        <form action="{{ route('concessions.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">1. Emplacement Physique *</label>
                    <select name="id_emplacement" required class="w-full rounded-lg border-slate-300 text-sm">
                        <option value="">-- Choisir une tombe libre --</option>
                        @foreach($emplacementsLibres as $emp)
                            <option value="{{ $emp->id_emplacement }}">
                                {{ $emp->lieu->nom_lieu }} - {{ $emp->reference_emplacement }} ({{ $emp->type_emplacement }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">2. Contrat de Concession *</label>
                    <select name="id_contrat" required class="w-full rounded-lg border-slate-300 text-sm">
                        <option value="">-- Choisir l'acte de vente --</option>
                        @foreach($contratsDispos as $ct)
                            <option value="{{ $ct->id_contrat }}">
                                N° {{ $ct->numero_contrat }} - {{ $ct->tiers->raison_sociale ?? $ct->tiers->nom_tiers }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">3. Personnes Inhumées (Défunts)</label>
                <select name="id_defunts[]" multiple size="5" class="w-full rounded-lg border-slate-300 text-sm">
                    @foreach($personnes as $p)
                        <option value="{{ $p->id_tiers }}">{{ $p->nom_tiers }} {{ $p->prenom_tiers }}</option>
                    @endforeach
                </select>
                <p class="text-[10px] text-slate-400 mt-2">Maintenez CTRL pour sélectionner plusieurs défunts.</p>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Bénéficiaires autorisés</label>
                    <textarea name="beneficiaires_autorises" rows="2" class="w-full rounded-lg border-slate-300 text-sm"
                        placeholder="Ex: M. Jean DUPONT et ses descendants..."></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Observations</label>
                    <textarea name="commentaire_concession" rows="2"
                        class="w-full rounded-lg border-slate-300 text-sm"></textarea>
                </div>
            </div>

            <button type="submit"
                class="w-full py-3 bg-slate-900 text-white font-bold rounded-lg hover:bg-slate-800 transition shadow-lg">
                ✍️ Enregistrer l'acte de concession
            </button>
        </form>
    </div>
@endsection