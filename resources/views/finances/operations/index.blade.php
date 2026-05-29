@extends('layouts.app')
@section('header_title', 'Référentiel des Écritures Comptables')
@section('content')
<div class="max-w-5xl mx-auto space-y-6 pb-12">
    <div
        class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Nomenclature des Opérations</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Codes d'imputations analytiques du journal de la
                commune.</p>
        </div>
        @can('check-permission', ['Finances & Achats', 'ecriture'])
        <a href="{{ route('operations-comptables.create') }}"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-xs shadow-sm">➕ Nouvelle
            Opération</a>
        @endcan
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-slate-50 border-b font-bold text-slate-500 text-xs uppercase tracking-wider">
                    <th class="p-4 w-40">N° Opération</th>
                    <th class="p-4">Libellé analytique</th>
                    <th class="p-4">Nature</th>
                    <th class="p-4 text-center w-24">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                @foreach($operations as $op)
                <tr class="hover:bg-slate-50/80 transition">
                    <td class="p-4 font-mono font-bold text-slate-900">{{ $op->numero_operation }}</td>
                    <td class="p-4">{{ $op->libelle_operation ?? '—' }}</td>
                    <td class="p-4 text-xs text-slate-500">{{ $op->nature_operation ?? '—' }}</td>
                    <td class="p-4 text-center space-x-2 flex justify-center">
                        <a href="{{ route('operations-comptables.edit', $op->id_operation) }}"
                            class="text-amber-600 hover:underline">✏️</a>
                        @can('check-permission', ['Finances & Achats', 'suppression'])
                        <form action="{{ route('operations-comptables.destroy', $op->id_operation) }}" method="POST"
                            class="inline" onsubmit="return confirm('Supprimer ce code ?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500">🗑️</button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection