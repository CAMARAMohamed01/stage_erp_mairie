@extends('layouts.app')

@section('header_title', 'Registre des cimetières')

@section('content')
    <div class="max-w-6xl mx-auto pb-12">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Emplacements Funéraires</h1>
                <p class="text-sm text-slate-500 mt-1">Gestion physique des tombes et caveaux de la commune.</p>
            </div>
            @if(auth()->user()->can('check-permission', ['État Civil & Cimetières', 'ecriture']))
                <a href="{{ route('emplacements.create') }}"
                    class="px-4 py-2 bg-slate-900 text-white font-bold rounded-lg shadow hover:bg-slate-800 transition">
                    ➕ Nouvel emplacement
                </a>
            @endif
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-bold">
                        <th class="p-4">Cimetière</th>
                        <th class="p-4">Référence</th>
                        <th class="p-4">Type</th>
                        <th class="p-4">Places</th>
                        <th class="p-4">Statut</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($emplacements as $emp)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-4 text-sm font-medium text-slate-800">{{ $emp->lieu->nom_lieu ?? 'Non assigné' }}</td>
                            <td class="p-4 text-sm font-mono text-slate-600">{{ $emp->reference_emplacement }}</td>
                            <td class="p-4 text-sm text-slate-600">{{ $emp->type_emplacement }}</td>
                            <td class="p-4 text-sm text-slate-600">{{ $emp->capacite_max }} pl.</td>
                            <td class="p-4 text-sm">
                                @if($emp->statut_occupation == 'Libre')
                                    <span class="px-2 py-1 text-xs font-bold rounded-md bg-green-100 text-green-700">Libre</span>
                                @elseif($emp->statut_occupation == 'Occupé')
                                    <span class="px-2 py-1 text-xs font-bold rounded-md bg-red-100 text-red-700">Occupé</span>
                                @else
                                    <span
                                        class="px-2 py-1 text-xs font-bold rounded-md bg-amber-100 text-amber-700">{{ $emp->statut_occupation }}</span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('emplacements.edit', $emp->id_emplacement) }}"
                                    class="text-blue-600 hover:text-blue-800 text-sm font-semibold">Modifier</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400 italic text-sm">
                                Aucun emplacement funéraire n'est enregistré pour le moment.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection