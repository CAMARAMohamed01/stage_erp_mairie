@extends('layouts.app')

@section('header_title', 'Relevés du compteur')

@section('content')
    <div class="max-w-4xl mx-auto pb-12">
        <div class="mb-6 flex justify-between items-end">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Relevés : {{ $compteur->point_comptage }}</h1>
                <p class="text-sm text-slate-500 mt-1">Historique des index pour le compteur n°
                    {{ $compteur->numero_compteur }}
                </p>
            </div>
            <a href="{{ route('compteurs.show', $compteur->id_compteur) }}"
                class="text-sm font-semibold text-slate-600 hover:text-slate-900">← Retour au compteur</a>
        </div>

        <div class="bg-indigo-50 border border-indigo-100 p-6 rounded-xl mb-8">
            <h2 class="text-sm font-bold text-indigo-900 mb-4">➕ Ajouter un nouveau relevé</h2>
            <form action="{{ route('compteurs.releves.store', $compteur->id_compteur) }}" method="POST"
                class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-indigo-800 uppercase mb-1">Date *</label>
                    <input type="date" name="date_releve" value="{{ date('Y-m-d') }}" required
                        class="w-full rounded-lg border-indigo-200 text-sm focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-indigo-800 uppercase mb-1">Index
                        ({{ $compteur->unite_mesure }}) *</label>
                    <input type="number" step="0.01" name="valeur_index" required
                        class="w-full rounded-lg border-indigo-200 text-sm focus:ring-indigo-500">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-xs font-bold text-indigo-800 uppercase mb-1">Commentaire</label>
                    <input type="text" name="commentaire_releve"
                        class="w-full rounded-lg border-indigo-200 text-sm focus:ring-indigo-500">
                </div>
                <button type="submit"
                    class="bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-indigo-700 transition">
                    Enregistrer
                </button>
            </form>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr
                        class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-bold">
                        <th class="p-4">Date</th>
                        <th class="p-4 text-right">Index ({{ $compteur->unite_mesure }})</th>
                        <th class="p-4">Commentaire</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($releves as $releve)
                        <tr class="hover:bg-slate-50 transition text-sm">
                            <td class="p-4 text-slate-700 font-medium">
                                {{ \Carbon\Carbon::parse($releve->date_releve)->format('d/m/Y') }}
                            </td>
                            <td class="p-4 text-right font-mono font-bold text-slate-900">
                                {{ number_format($releve->valeur_index, 2, ',', ' ') }}
                            </td>

                            <td class="p-4 text-right">
                                @if($releve->consommation !== null)
                                    <span class="font-bold {{ $releve->consommation > 500 ? 'text-red-600' : 'text-green-600' }}">
                                        {{ number_format($releve->consommation, 2, ',', ' ') }} {{ $compteur->unite_mesure }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic text-xs">Initial</span>
                                @endif
                            </td>

                            <td class="p-4 text-slate-500 italic">{{ $releve->commentaire_releve }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-slate-400 italic">Aucun relevé pour ce compteur.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection