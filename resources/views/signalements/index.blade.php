@extends('layouts.app')

@section('title', 'Registre des Signalements')

@section('content')
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex justify-between items-center">
            <div>
                <h1 class="text-xl font-bold text-slate-800">📋 Registre des Signalements</h1>
                <p class="text-sm text-slate-500">Liste exhaustive des doléances citoyennes</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('signalements.excel') }}"
                    class="flex items-center gap-2 bg-white border border-slate-300 px-4 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Exporter
                </a>

                <a href="{{ route('signalements.create') }}"
                    class="flex items-center gap-2 bg-blue-600 px-4 py-2 rounded-lg text-sm font-bold text-white hover:bg-blue-700 shadow-md transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nouveau Signalement
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold">Réf</th>
                        <th class="px-6 py-4 font-semibold">Date</th>
                        <th class="px-6 py-4 font-semibold">Émetteur</th>
                        <th class="px-6 py-4 font-semibold">Description</th>
                        <th class="px-6 py-4 font-semibold">Statut</th>
                        <th class="px-6 py-4 font-semibold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($signalements as $sig)
                        <tr class="hover:bg-slate-50 transition-colors text-sm">
                            <td class="px-6 py-4 font-bold text-slate-700">#{{ $sig->id_sig }}</td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ \Carbon\Carbon::parse($sig->date_creation)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-900">{{ $sig->emetteur_nom }}</div>
                                <div class="text-slate-500 text-xs">{{ $sig->mode_reception }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ Str::limit($sig->description, 60) }}
                            </td>
                            <td class="px-6 py-4">
                                <x-badge type="statut" :value="$sig->statut_signalement" />
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('signalement.show', $sig->id_sig) }}"
                                    class="text-blue-600 hover:text-blue-800 font-medium">
                                    Consulter →
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection