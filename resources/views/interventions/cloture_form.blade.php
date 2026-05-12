@extends('layouts.app')

@section('title', 'Clôture Intervention #' . $intervention->id_int)

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl border border-slate-200 shadow-lg overflow-hidden">
            <div class="bg-green-600 px-6 py-4">
                <h1 class="text-xl font-bold text-white">✅ Rapport de Fin d'Intervention</h1>
                <p class="text-green-100 text-sm">Intervention : {{ $intervention->type_intervention }}</p>
            </div>

            <form action="{{ route('interventions.cloturer.save', $intervention->id_int) }}" method="POST"
                class="p-6 space-y-6">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Observations et travaux réalisés</label>
                    <textarea name="compte_rendu" rows="5" required
                        class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                        placeholder="Détaillez les actions menées, les pièces remplacées, etc..."></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Temps passé (heures)</label>
                        <input type="number" name="temps_passe" step="0.25" placeholder="ex: 1.5"
                            class="w-full border-slate-300 rounded-lg shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Statut après action</label>
                        <select name="statut_final" class="w-full border-slate-300 rounded-lg shadow-sm text-sm">
                            <option value="Terminé">✅ Terminé / Résolu</option>
                            <option value="En cours">🚧 En cours (nécessite un autre passage)</option>
                            <option value="En attente">⏳ En attente (pièce manquante)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Date de clôture</label>
                        <input type="date" name="date_cloture" value="{{ date('Y-m-d') }}" required
                            class="w-full border-slate-300 rounded-lg shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Résultat</label>
                        <select name="resultat" class="w-full border-slate-300 rounded-lg shadow-sm">
                            <option value="Succès">✅ Résolu avec succès</option>
                            <option value="Partiel">⚠️ Résolu partiellement</option>
                            <option value="Echec">❌ Non résolu / Nouveau devis nécessaire</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                    <a href="{{ route('interventions.show', $intervention->id_int) }}"
                        class="px-4 py-2 text-slate-500 font-medium">Annuler</a>
                    <button type="submit"
                        class="px-6 py-2 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 transition shadow-md">
                        Valider et Clôturer l'intervention
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection