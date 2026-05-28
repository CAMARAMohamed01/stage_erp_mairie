@extends('layouts.app')

@section('header_title', 'Modifier le Support d\'Accès')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6 pb-12">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">✏️ Modifier le support d'accès</h1>
            <a href="{{ route('supports-acces.show', $support->id_support) }}"
                class="text-sm font-semibold text-slate-500 hover:text-slate-800 transition">← Annuler</a>
        </div>

        <form action="{{ route('supports-acces.update', $support->id_support) }}" method="POST"
            class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Numéro de série / Identifiant *</label>
                    <input type="text" name="numero_serie" value="{{ old('numero_serie', $support->numero_serie) }}"
                        required
                        class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-slate-500 font-mono uppercase">
                    @error('numero_serie') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Type de support d'accès</label>
                    <select name="type_support"
                        class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-slate-500">
                        <option value="Clé physique" {{ $support->type_support == 'Clé physique' ? 'selected' : '' }}>Clé
                            physique</option>
                        <option value="Badge RFID" {{ $support->type_support == 'Badge RFID' ? 'selected' : '' }}>Badge RFID
                        </option>
                        <option value="Vigik" {{ $support->type_support == 'Vigik' ? 'selected' : '' }}>Vigik</option>
                        <option value="Télécommande" {{ $support->type_support == 'Télécommande' ? 'selected' : '' }}>
                            Télécommande</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Observations / Commentaires</label>
                <textarea name="observations" rows="3"
                    class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-slate-500">{{ old('observations', $support->observations) }}</textarea>
            </div>

            <div class="flex items-center">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="est_actif" value="1" {{ $support->est_actif ? 'checked' : '' }}
                        class="w-5 h-5 text-slate-900 rounded focus:ring-slate-500 border-gray-300">
                    <span class="text-sm font-semibold text-slate-800">Support actif et fonctionnel</span>
                </label>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('supports-acces.show', $support->id_support) }}"
                    class="px-6 py-2.5 border border-slate-300 text-slate-700 font-semibold rounded-lg hover:bg-slate-50 transition">Annuler</a>
                <button type="submit"
                    class="px-6 py-2.5 bg-slate-900 text-white font-bold rounded-lg hover:bg-slate-800 transition shadow-sm">Enregistrer
                    les modifications</button>
            </div>
        </form>
    </div>
@endsection