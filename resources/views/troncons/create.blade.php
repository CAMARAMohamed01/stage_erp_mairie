@extends('layouts.app')

@section('header_title', 'Ajouter un Tronçon')

@section('content')
    <div class="max-w-3xl mx-auto">
        <form action="{{ route('troncons.store') }}" method="POST"
            class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-6">
            @csrf
            <h2 class="text-lg font-bold text-slate-800 border-b pb-2">➕ Création d'un tronçon</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-1">Voie associée</label>
                    <select name="id_voie" class="w-full border-slate-300 rounded-lg">
                        @foreach($voies as $v)
                            <option value="{{ $v->id_voie }}" {{ $selectedVoieId == $v->id_voie ? 'selected' : '' }}>
                                {{ $v->nom_voie }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Numéro/Code Tronçon</label>
                    <input type="text" name="numero_troncon" required class="w-full border-slate-300 rounded-lg">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Type de revêtement</label>
                    <input type="text" name="type_revetement" placeholder="Ex: Enrobé, Pavés..."
                        class="w-full border-slate-300 rounded-lg">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">PK Début</label>
                    <input type="number" step="0.01" name="pk_debut" required class="w-full border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">PK Fin</label>
                    <input type="number" step="0.01" name="pk_fin" required class="w-full border-slate-300 rounded-lg">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Zone (Secteur)</label>
                    <select name="id_zone" class="w-full border-slate-300 rounded-lg">
                        <option value="">-- Aucune zone --</option>
                        @foreach($zones as $z)
                            <option value="{{ $z->id_zone }}">{{ $z->nom_zone }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Ouvrage lié</label>
                    <select name="id_ouvrage_lie" class="w-full border-slate-300 rounded-lg">
                        <option value="">-- Aucun --</option>
                        @foreach($ouvrages as $o)
                            <option value="{{ $o->id_ouvrage }}">{{ $o->nom_ouvrage }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="history.back()"
                    class="px-4 py-2 border border-slate-300 rounded-lg">Annuler</button>
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700">Enregistrer</button>
            </div>
        </form>
    </div>
@endsection