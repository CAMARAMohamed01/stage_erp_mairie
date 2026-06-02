@extends('layouts.app')

@section('title', 'Modifier le lieu-dit')

@section('content')
    <div class="max-w-xl mx-auto bg-white p-8 rounded-xl border border-slate-200 shadow-sm my-6">
        <div class="border-b pb-4 mb-6">
            <h2 class="text-xl font-bold text-slate-800">📝 Modifier le lieu-dit</h2>
            <p class="text-sm text-slate-500">Mise à jour du libellé pour l'adresse et le cadastre.</p>
        </div>

        <form action="{{ route('lieux-dits.update', $lieuDit->id_lieu_dit) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="nom_lieu_dit" class="block text-xs font-bold text-slate-500 uppercase mb-1">Nom officiel du
                    lieu-dit</label>
                <input type="text" name="nom_lieu_dit" id="nom_lieu_dit" value="{{ $lieuDit->nom_lieu_dit }}" required
                    class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                @error('nom_lieu_dit')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end pt-4 gap-2">
                <a href="{{ route('lieux-dits.index') }}"
                    class="px-4 py-2 bg-slate-100 text-slate-600 text-sm font-bold rounded-lg hover:bg-slate-200 transition">
                    Annuler
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 shadow-md transition">
                    Sauvegarder les modifications
                </button>
            </div>
        </form>
    </div>
@endsection