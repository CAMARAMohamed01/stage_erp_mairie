@extends('layouts.app')
@section('title', 'Saisie Arbitrage Commission')

@section('content')
<div class="max-w-3xl mx-auto pb-12">
    <form action="{{ route('decisions-commission.store') }}" method="POST"
        class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        @csrf

        <div class="p-6 bg-slate-50 border-b border-slate-200">
            <h1 class="text-base font-bold text-slate-900">Enregistrer une décision de commission</h1>
            <p class="text-xs text-slate-400 font-medium mt-0.5">Saisie des comptes-rendus de vote et maillage avec les
                dossiers municipaux.</p>
        </div>

        <div class="p-6 space-y-5 text-xs font-medium text-slate-700">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Date de la commission *</label>
                    <input type="date" name="date_commission" required
                        value="{{ old('date_commission', date('Y-m-d')) }}"
                        class="w-full border rounded-lg p-2.5 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                </div>
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Statut de la décision *</label>
                    <select name="statut_decision" required
                        class="w-full border rounded-lg p-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700">
                        <option value="Validé"> Validé / Approuvé</option>
                        <option value="En attente"> En attente d'éléments</option>
                        <option value="Ajourné"> Ajourné à la prochaine session</option>
                        <option value="Refusé"> Refusé / Rejeté</option>
                    </select>
                </div>
            </div>

            <div class="bg-slate-50/50 p-4 rounded-xl border border-dashed space-y-4">
                <h3 class="font-bold text-slate-800 uppercase tracking-wider text-[10px] text-blue-600">🔗 Maillage et
                    Impacts dossier (Optionnel)</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-600 mb-1">Rattacher à un Projet de mandat</label>
                        <select name="id_projet" class="w-full border rounded-lg p-2 bg-white focus:outline-none">
                            <option value="">-- Aucun projet lié --</option>
                            @foreach($projets as $p)
                            <option value="{{ $p->id_projet }}">📂 {{ $p->nom_projet }} (Mandat: {{ $p->annee_mandat }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-600 mb-1">Associer à une écriture comptable</label>
                        <select name="id_operation"
                            class="w-full border rounded-lg p-2 bg-white focus:outline-none font-mono">
                            <option value="">-- Aucune opération --</option>
                            @foreach($operations as $o)
                            <option value="{{ $o->id_operation }}">[{{ $o->numero_operation }}] -
                                {{ $o->libelle_operation }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-600 mb-1">Lier à une Intervention technique</label>
                    <select name="id_int" class="w-full border rounded-lg p-2 bg-white focus:outline-none">
                        <option value="">-- Aucune intervention liée --</option>
                        @foreach($interventions as $i)
                        <option value="{{ $i->id_int }}">🛠️ [{{ $i->statut_global }}] {{ $i->type_intervention }}
                            ({{ \Carbon\Carbon::parse($i->date_ouverture)->format('d/m/Y') }})</option>
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
                    <option value="{{ $a->id_user }}">👤 {{ $a->nom_user }} {{ $a->prenom_user }} ({{ $a->role_appli }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-600 mb-1">Commentaires et consignes des élus</label>
                <textarea name="commentaire_elus" rows="4"
                    placeholder="Saisir les remarques formulées par les adjoints ou le maire durant la commission..."
                    class="w-full border rounded-lg p-2.5 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition"></textarea>
            </div>
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-2">
            <a href="{{ route('decisions-commission.index') }}"
                class="px-4 py-2 border rounded-lg font-bold text-slate-600 bg-white hover:bg-slate-50 transition">Annuler</a>
            <button type="submit"
                class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-sm transition">💾
                Notifier l'arbitrage</button>
        </div>
    </form>
</div>
@endsection