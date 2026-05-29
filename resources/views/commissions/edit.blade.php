@extends('layouts.app')
@section('title', 'Modifier l\'Arbitrage')

@section('content')
<div class="max-w-3xl mx-auto pb-12">
    <form action="{{ route('decisions-commission.update', $decision->id_decision) }}" method="POST"
        class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        @csrf
        @method('PUT')

        <div class="p-6 bg-slate-50 border-b border-slate-200 flex items-center gap-4">
            <div
                class="w-12 h-12 rounded-xl bg-blue-500 text-white-600 flex items-center justify-center text-2xl shadow-sm">
                ⚖️</div>
            <div>
                <h1 class="text-base font-bold text-slate-900">Rectifier la décision du
                    {{ \Carbon\Carbon::parse($decision->date_commission)->format('d/m/Y') }}
                </h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Mise à jour des retours sur l'avis des élus ou
                    réaffectation des liens dossiers.</p>
            </div>
        </div>

        <div class="p-6 space-y-5 text-xs font-medium text-slate-700">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Date de la commission *</label>
                    <input type="date" name="date_commission" required
                        value="{{ old('date_commission', $decision->date_commission) }}"
                        class="w-full border rounded-lg p-2.5 bg-slate-50 focus:bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Statut de la décision *</label>
                    <select name="statut_decision" required
                        class="w-full border rounded-lg p-2.5 bg-white focus:outline-none text-slate-700">
                        <option value="Validé" {{ $decision->statut_decision == 'Validé' ? 'selected' : '' }}> Validé
                            /
                            Approuvé</option>
                        <option value="En attente" {{ $decision->statut_decision == 'En attente' ? 'selected' : '' }}>
                            En attente d'éléments</option>
                        <option value="Ajourné" {{ $decision->statut_decision == 'Ajourné' ? 'selected' : '' }}>
                            Ajourné
                            à la prochaine session</option>
                        <option value="Refusé" {{ $decision->statut_decision == 'Refusé' ? 'selected' : '' }}> Refusé
                            /
                            Rejeté</option>
                    </select>
                </div>
            </div>

            <div class="bg-slate-50/50 p-4 rounded-xl border border-dashed space-y-4">
                <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[10px] text-blue-600">🔗 Modifier le
                    Maillage dossier</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-600 mb-1">Projet de mandat lié</label>
                        <select name="id_projet" class="w-full border rounded-lg p-2 bg-white focus:outline-none">
                            <option value="">-- Aucun projet lié --</option>
                            @foreach($projets as $p)
                            <option value="{{ $p->id_projet }}"
                                {{ $decision->id_projet == $p->id_projet ? 'selected' : '' }}>📂 {{ $p->nom_projet }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-600 mb-1">Écriture comptable associée</label>
                        <select name="id_operation"
                            class="w-full border rounded-lg p-2 bg-white focus:outline-none font-mono">
                            <option value="">-- Aucune opération --</option>
                            @foreach($operations as $o)
                            <option value="{{ $o->id_operation }}"
                                {{ $decision->id_operation == $o->id_operation ? 'selected' : '' }}>
                                [{{ $o->numero_operation }}] - {{ $o->libelle_operation }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-600 mb-1">Intervention technique reliée</label>
                    <select name="id_int" class="w-full border rounded-lg p-2 bg-white focus:outline-none">
                        <option value="">-- Aucune intervention liée --</option>
                        @foreach($interventions as $i)
                        <option value="{{ $i->id_int }}" {{ $decision->id_int == $i->id_int ? 'selected' : '' }}>🛠️
                            {{ $i->type_intervention }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-600 mb-1">Secrétaire / Enregistreur du PV</label>
                <select name="id_enregistreur_decision"
                    class="w-full border rounded-lg p-2.5 bg-white focus:outline-none text-slate-700">
                    <option value="">-- Non spécifié --</option>
                    @foreach($agents as $a)
                    <option value="{{ $a->id_user }}"
                        {{ $decision->id_enregistreur_decision == $a->id_user ? 'selected' : '' }}>👤 {{ $a->nom_user }}
                        {{ $a->prenom_user }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-600 mb-1">Commentaires et consignes des élus</label>
                <textarea name="commentaire_elus" rows="4"
                    class="w-full border rounded-lg p-2.5 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition">{{ old('commentaire_elus', $decision->commentaire_elus) }}</textarea>
            </div>
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-2">
            <a href="{{ route('decisions-commission.index') }}"
                class="px-4 py-2 border rounded-lg font-bold text-slate-600 bg-white hover:bg-slate-50 transition">Annuler</a>
            <button type="submit"
                class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-sm transition">💾
                Enregistrer les rectifications</button>
        </div>
    </form>
</div>
@endsection